<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\DuplicateRequestException;
use Bizgo\Exception\InternalServerException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\TimeoutException;
use Bizgo\Exception\ValidationException;
use Bizgo\Model\AlimtalkMessage;
use Bizgo\Model\Destination;
use Bizgo\Model\MessageFlowItem;
use Bizgo\Model\MmsMessage;
use Bizgo\Model\SendOmniRequest;
use Bizgo\Model\SmsMessage;
use Bizgo\Result\SendResult;
use Bizgo\Tests\Support\NetworkError;
use Bizgo\Version;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\RequestInterface;

final class SendTest extends TestCase
{
    private const PATH = '/api/comm/v1/send/omni';

    public function testSmsRequestBodyAndHeaders(): void
    {
        $this->http->push(self::reply(self::accepted('A000')));
        $result = $this->client->send->sms(to: '01000000000', from: '01000000000', text: '인증번호는 123456 입니다.', ref: 'ref-1');

        $request = $this->http->last();
        self::assertSame('POST', $request->getMethod());
        self::assertSame(self::BASE . self::PATH, (string) $request->getUri());
        self::assertSame(self::API_KEY, $request->getHeaderLine('Authorization')); // raw key, no prefix (sandbox verified)
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertStringStartsWith('application/json', $request->getHeaderLine('Content-Type'));
        self::assertStringStartsWith('bizgo-sdk-comm-php/' . Version::SDK . ' php/', $request->getHeaderLine('User-Agent'));
        self::assertSame([
            'destinations' => [['to' => '01000000000']],
            'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => '인증번호는 123456 입니다.']]],
            'ref' => 'ref-1',
        ], $this->http->lastJson());
        self::assertSame(['KEY000'], $result->msgKeys());
        self::assertSame([], $result->failed());
        self::assertSame('ref-1', $result->ref);
        self::assertSame('TR-TEST', $result->trackingId);
    }

    public function testFallbackFlowKeepsOrderAndOmitsUnsetDefaults(): void
    {
        $this->http->push(self::reply(self::accepted('A000')));
        $this->client->send->omni(
            to: [new Destination(to: '01000000000', replaceWords: ['name' => '홍길동'])],
            messages: [
                new AlimtalkMessage(
                    senderKey: 'SENDER_KEY_EXAMPLE',
                    templateCode: 'TEMPLATE_CODE_EXAMPLE',
                    msgType: 'AT',
                    text: '#{name}님 주문 완료',
                ),
                new SmsMessage(from: '01000000000', text: '#{name}님 주문 완료'),
            ],
            idempotencyKey: 'order-1',
        );
        $body = $this->http->lastJson();
        self::assertIsArray($body['messageFlow']);
        self::assertSame(['alimtalk', 'sms'], array_map(static fn (mixed $item): string => is_array($item) ? (string) array_key_first($item) : '', $body['messageFlow']));
        self::assertSame([
            'msgType' => 'AT',
            'senderKey' => 'SENDER_KEY_EXAMPLE',
            'templateCode' => 'TEMPLATE_CODE_EXAMPLE',
            'text' => '#{name}님 주문 완료',
        ], $this->http->lastAt('messageFlow.0.alimtalk')); // responseMethod/timeout defaults are not sent
        self::assertSame([['to' => '01000000000', 'replaceWords' => ['name' => '홍길동']]], $body['destinations']);
        self::assertSame('order-1', $body['idempotencyKey']);
    }

    public function testPartialFailureIsReportedPerRecipient(): void
    {
        $this->http->push(self::reply(self::accepted('A000', 'A306')));
        $result = $this->client->send->sms(to: ['01000000000', '01000001234'], from: '01000000000', text: 'x');
        self::assertSame(['A306'], array_map(static fn ($d) => $d->code, $result->failed()));
        self::assertCount(1, $result->succeeded());
        self::assertSame(['KEY000'], $result->msgKeys());
    }

    public function testPerRecipientA301IsADuplicateNotAFailure(): void
    {
        $this->http->push(self::reply(self::accepted('A301', 'A301')));
        $result = $this->client->send->sms(to: ['01000000000', '01000001234'], from: '01000000000', text: 'x', idempotencyKey: 'order-1');
        self::assertSame([SendResult::DUPLICATE_CODE, SendResult::DUPLICATE_CODE], array_map(static fn ($d) => $d->code, $result->duplicates()));
        self::assertSame([], $result->failed());
        self::assertSame([], $result->succeeded());
        self::assertSame([], $result->msgKeys());
    }

    public function testMixedAcceptedDuplicateAndRejected(): void
    {
        $this->http->push(self::reply(self::accepted('A000', 'A301', 'A306')));
        $result = $this->client->send->sms(to: ['01000000000', '01000001234', '01000000000'], from: '01000000000', text: 'x', idempotencyKey: 'order-1');
        self::assertSame(['KEY000'], array_map(static fn ($d) => $d->msgKey, $result->succeeded()));
        self::assertSame(['KEY001'], array_map(static fn ($d) => $d->msgKey, $result->duplicates()));
        self::assertSame(['A306'], array_map(static fn ($d) => $d->code, $result->failed()));
        self::assertSame(['KEY000'], $result->msgKeys());
        // duplicates are masked like the other destination lists
        self::assertStringNotContainsString('01000000000', (string) $result);
        self::assertStringNotContainsString('01000000000', print_r($result, true));
    }

    public function testMmsAndLms(): void
    {
        $this->http->always(self::reply(self::accepted('A000')));
        $this->client->send->mms(to: '01000000000', from: '01000000000', text: '본문', fileKeys: ['FILE_KEY_001'], title: '제목');
        self::assertSame(
            ['from' => '01000000000', 'title' => '제목', 'text' => '본문', 'fileKey' => ['FILE_KEY_001']],
            $this->http->lastAt('messageFlow.0.mms'),
        );
        $this->client->send->lms(to: '01000000000', from: '01000000000', text: '본문');
        self::assertSame(['from' => '01000000000', 'text' => '본문'], $this->http->lastAt('messageFlow.0.mms'));
    }

    /** @return iterable<string, array{string, string}> */
    public static function tooLongTexts(): iterable
    {
        yield '46 Hangul syllables = 92 bytes' => [str_repeat('가', 46), '최대 90byte인데 92byte'];
        yield 'emoji' => ['안녕😀', 'EUC-KR로 표현할 수 없는 문자'];
    }

    #[DataProvider('tooLongTexts')]
    public function testSmsByteLimitIsCheckedBeforeSending(string $text, string $message): void
    {
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: $text);
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString($message, $e->getMessage());
            self::assertSame('text', $e->errors[0]['path']);
        }
        self::assertSame(0, $this->http->count());
    }

    public function testSmsLimitCountsBytesNotCharacters(): void
    {
        new SmsMessage(from: '01000000000', text: str_repeat('가', 45)); // 90 bytes: ok
        new SmsMessage(from: '01000000000', text: str_repeat('a', 90));
        new SmsMessage(from: '01000000000', text: '뷁'); // CP949 extension syllable (not in plain EUC-KR): 2 bytes
        new MmsMessage(from: '01000000000', text: str_repeat('가', 1000)); // 2,000 bytes: ok
        $this->expectException(ValidationException::class);
        new MmsMessage(from: '01000000000', text: str_repeat('가', 1001));
    }

    public function testUnknownFieldsAreRejected(): void
    {
        try {
            AlimtalkMessage::fromArray(['senderKey' => 'S', 'templateCode' => 'T', 'templatecode' => 'typo']);
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame([['path' => 'templatecode', 'reason' => '알 수 없는 필드입니다']], $e->errors);
        }
    }

    public function testNestedErrorsCarryTheFieldPath(): void
    {
        try {
            $this->client->send->request([
                'destinations' => [['to' => '01000000000']],
                'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => '😀', 'ttl' => 'soon']]],
            ]);
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            $paths = array_column($e->errors, 'path');
            self::assertSame(['messageFlow[0].sms.text', 'messageFlow[0].sms.ttl'], $paths);
        }
        self::assertSame(0, $this->http->count());
    }

    public function testMoreThan200RecipientsIsRejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('destinations: 항목은 최대 200개인데 201개입니다');
        $this->client->send->sms(to: array_fill(0, 201, '01000000000'), from: '01000000000', text: 'x');
    }

    /** @return iterable<string, array{array<string, mixed>}> */
    public static function badFlowItems(): iterable
    {
        yield 'two channels' => [['sms' => ['from' => '01000000000', 'text' => 'x'], 'mms' => ['from' => '01000000000', 'text' => 'x']]];
        yield 'no channel' => [[]];
        yield 'unknown channel' => [['fax' => ['text' => 'x']]];
    }

    /** @param array<string, mixed> $item */
    #[DataProvider('badFlowItems')]
    public function testMessageFlowItemNeedsExactlyOneChannel(array $item): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('정확히 하나');
        $this->client->send->request(['destinations' => [['to' => '01000000000']], 'messageFlow' => [$item]]);
    }

    public function testValidationErrorsDoNotEchoPhoneNumbers(): void
    {
        try {
            $this->client->send->sms(to: '01000001234', from: '01000001234', text: '😀');
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertStringNotContainsString('01000001234', $e->getMessage());
            // #[\SensitiveParameter] keeps the values out of the stack trace, which loggers often record
            self::assertStringNotContainsString('01000001234', $e->getTraceAsString());
            self::assertStringNotContainsString('01000001234', print_r($e->getTrace(), true));
        }
    }

    public function testSendWithoutIdempotencyKeyIsNotRetriedAfterTimeout(): void
    {
        $this->http->always(static fn (RequestInterface $request) => throw new NetworkError($request, 'cURL error 28: Operation timed out'));
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x');
            self::fail('expected a TimeoutException');
        } catch (TimeoutException) {
        }
        self::assertSame(1, $this->http->count());
    }

    public function testSendWithoutIdempotencyKeyIsNotRetriedOn5xx(): void
    {
        $this->http->always(self::reply('<html>bad gateway</html>', 502));
        $this->expectException(InternalServerException::class);
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x');
        } finally {
            self::assertSame(1, $this->http->count());
        }
    }

    public function testSendWithIdempotencyKeyIsRetriedAfterTimeout(): void
    {
        $this->http
            ->push(static fn (RequestInterface $request) => throw new NetworkError($request, 'cURL error 28: Operation timed out'))
            ->push(self::reply(self::accepted('A000')));
        $result = $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x', idempotencyKey: 'k-1');
        self::assertSame(2, $this->http->count());
        self::assertSame(['KEY000'], $result->msgKeys());
        self::assertSame((string) $this->http->requests[0]->getBody(), (string) $this->http->requests[1]->getBody());
    }

    public function testDuplicateAfterLostResponseExplainsWhatHappened(): void
    {
        $this->http
            ->push(static fn (RequestInterface $request) => throw new NetworkError($request))
            ->push(self::reply(self::envelope(code: 'A301')));
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x', idempotencyKey: 'k-1');
            self::fail('expected a DuplicateRequestException');
        } catch (DuplicateRequestException $e) {
            self::assertStringContainsString('이미 접수', $e->getMessage());
            self::assertSame('A301', $e->errorCode);
            self::assertSame('service', $e->layer);
        }
    }

    public function testDuplicateWithoutLostResponseKeepsTheServerMessage(): void
    {
        $this->http->push(self::reply(self::envelope(code: 'A301')));
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x', idempotencyKey: 'k-1');
            self::fail('expected a DuplicateRequestException');
        } catch (DuplicateRequestException $e) {
            self::assertStringNotContainsString('이전 시도', $e->getMessage());
            self::assertSame('이미 동일한 요청이 처리되었습니다.', $e->description);
        }
    }

    public function testRateLimitIsRetriedWithRetryAfter(): void
    {
        $limited = self::reply(['common' => ['authCode' => 'A020', 'authResult' => 'Ratelimit']], 429, ['Retry-After' => '2']);
        $this->http->push($limited)->push(self::reply(self::accepted('A000')));
        $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x');
        self::assertSame(2, $this->http->count());
        self::assertSame([2.0], $this->sleeps);
    }

    public function testRetryAfterIsCappedAndBackoffIsBounded(): void
    {
        $this->http->always(self::reply(['data' => ['code' => 'A020', 'result' => 'Ratelimit']], 429, ['Retry-After' => '3600']));
        $client = $this->newClient(maxRetries: 3);
        try {
            $client->messages->statistics('20260101');
            self::fail('expected a RateLimitException');
        } catch (RateLimitException $e) {
            self::assertSame(60.0, $e->retryAfter);
        }
        self::assertSame([60.0, 60.0, 60.0], $this->sleeps);

        $this->sleeps = [];
        $this->http->always(self::reply('', 503));
        try {
            $client->messages->statistics('20260101');
        } catch (InternalServerException) {
        }
        self::assertCount(3, $this->sleeps);
        foreach ([0.5, 1.0, 2.0] as $i => $base) {
            self::assertGreaterThanOrEqual($base * 0.75, $this->sleeps[$i]);
            self::assertLessThanOrEqual($base * 1.25, $this->sleeps[$i]);
        }
    }

    public function testRateLimitGivesUpAfterMaxRetries(): void
    {
        $this->http->always(self::reply(['data' => ['code' => 'A020', 'result' => "Number of 'Ratelimit' exceeded"]], 429));
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x');
            self::fail('expected a RateLimitException');
        } catch (RateLimitException $e) {
            self::assertNull($e->retryAfter);
            self::assertSame(429, $e->httpStatus);
        }
        self::assertSame(3, $this->http->count()); // 1 + maxRetries(2)
    }

    public function testConnectionErrorWithoutKeyIsNotRetried(): void
    {
        $this->http->always(static fn (RequestInterface $request) => throw new NetworkError($request));
        $this->expectException(ConnectionException::class);
        try {
            $this->client->send->sms(to: '01000000000', from: '01000000000', text: 'x');
        } finally {
            self::assertSame(1, $this->http->count());
        }
    }

    public function testRequestAcceptsModelsAndArrays(): void
    {
        $this->http->always(self::reply(self::accepted('A000')));
        $request = new SendOmniRequest(
            destinations: [new Destination(to: '01000000000')],
            messageFlow: [new SmsMessage(from: '01000000000', text: 'x'), new MessageFlowItem(new SmsMessage(from: '01000000000', text: 'y'))],
        );
        $this->client->send->request($request);
        self::assertSame([['sms' => ['from' => '01000000000', 'text' => 'x']], ['sms' => ['from' => '01000000000', 'text' => 'y']]], $this->http->lastJson()['messageFlow']);

        $array = ['destinations' => [['to' => '01000000000']], 'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'x']]]];
        $this->client->send->request($array);
        self::assertSame($array, $this->http->lastJson());
    }

    public function testRecipientsAsStringsDestinationsAndArrays(): void
    {
        $this->http->always(self::reply(self::accepted('A000')));
        $this->client->send->omni(
            to: ['01000000000', new Destination(to: '01000001234', ref: 'r2'), ['to' => '01000000000', 'replaceWords' => ['a' => 'b']]],
            messages: [new SmsMessage(from: '01000000000', text: 'x')],
        );
        self::assertSame(
            [['to' => '01000000000'], ['to' => '01000001234', 'ref' => 'r2'], ['to' => '01000000000', 'replaceWords' => ['a' => 'b']]],
            $this->http->lastJson()['destinations'],
        );
        $this->client->send->omni(to: ['to' => '01000000000'], messages: [['sms' => ['from' => '01000000000', 'text' => 'x']]]);
        self::assertSame([['to' => '01000000000']], $this->http->lastJson()['destinations']);
    }

    public function testEmptyMessagesAreRejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('messages');
        $this->client->send->omni(to: '01000000000', messages: []);
    }

    public function testEmptyReplaceWordsIsSentAsJsonObject(): void
    {
        $this->http->push(self::reply(self::accepted('A000')));
        $this->client->send->omni(to: [new Destination(to: '01000000000', replaceWords: [])], messages: [new SmsMessage(from: '01000000000', text: 'x')]);
        self::assertStringContainsString('"replaceWords":{}', (string) $this->http->last()->getBody());
    }
}
