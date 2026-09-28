<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Exception\BadRequestException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\HttpResponse;
use Bizgo\Http\Transport;
use Bizgo\Model\Destination;
use Bizgo\Model\SendOmniRequest;
use Bizgo\Model\SmsMessage;
use Bizgo\Testing\FakeTransport;
use Bizgo\Upload;
use Bizgo\Webhook\WebhookReceiver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** Regression tests for the security review findings (M1-M3, L2-L6; H1/H2/L1/M3 in HttpClientPolicyTest, L8 in CurlSenderTest). */
final class ReviewFixesTest extends TestCase
{
    private const PHONE = '01000000000';

    /** @return list<array{string, list<mixed>}> function name and args of each trace frame */
    private static function frames(\Throwable $e): array
    {
        $out = [];
        for ($x = $e; $x !== null; $x = $x->getPrevious()) {
            foreach ($x->getTrace() as $frame) {
                $out[] = [($frame['class'] ?? '') . '::' . $frame['function'], array_values($frame['args'] ?? [])];
            }
        }

        return $out;
    }

    private static function assertNoPhoneInTrace(\Throwable $e): void
    {
        foreach (self::frames($e) as [$function, $args]) {
            self::assertStringNotContainsString(self::PHONE, print_r($args, true), $function);
        }
        self::assertStringNotContainsString(self::PHONE, print_r($e, true));
    }

    /** M1: request arrays, response bodies and file paths appear as SensitiveParameterValue in stack traces. */
    public function testSensitiveParametersAreHiddenInTraces(): void
    {
        try {
            SendOmniRequest::fromArray(['destinations' => [['to' => self::PHONE]], 'messageFlow' => [['sms' => ['from' => self::PHONE, 'text' => 'x']]], 'bogus' => 1]);
            self::fail('expected ValidationException');
        } catch (ValidationException $e) {
            $fromArray = array_values(array_filter(self::frames($e), static fn (array $f): bool => str_ends_with($f[0], '::fromArray')));
            self::assertNotEmpty($fromArray);
            self::assertInstanceOf(\SensitiveParameterValue::class, $fromArray[0][1][0]);
            self::assertNoPhoneInTrace($e);
        }

        $fake = new FakeTransport();
        $fake->on('sendOmni')->respond(400, ['common' => ['authCode' => 'A000'], 'data' => ['code' => 'A306', 'result' => 'bad', 'data' => ['destinations' => [['to' => self::PHONE]]]]]);
        try {
            $fake->client()->send->sms(to: self::PHONE, from: self::PHONE, text: 'x');
            self::fail('expected BadRequestException');
        } catch (BadRequestException $e) {
            self::assertNoPhoneInTrace($e);
        }

        try {
            Upload::fromPath(sys_get_temp_dir() . '/' . self::PHONE . '/banner.jpg');
            self::fail('expected ValidationException');
        } catch (ValidationException $e) {
            self::assertNoPhoneInTrace($e);
        }
    }

    /** M2: substitution values are shown as lengths only. */
    public function testReplaceWordsAreMasked(): void
    {
        $destination = new Destination(to: self::PHONE, replaceWords: ['name' => '홍길동', 'amount' => '1,000원']);
        $text = (string) $destination . print_r($destination, true);
        self::assertStringNotContainsString('홍길동', $text);
        self::assertStringNotContainsString('1,000원', $text);
        self::assertStringContainsString('name', $text);
        self::assertStringContainsString('(3자)', $text);
    }

    /** L2: an unusable 2xx answer to a send says the message may have been accepted. */
    public function testUnusableSendResponsesCarryTheAcceptedHint(): void
    {
        foreach (['<html>oops</html>', '{"common":{"authCode":"A000"},"data":' . str_repeat('[', 70) . str_repeat(']', 70) . '}'] as $body) {
            $fake = new FakeTransport();
            $fake->on('sendOmni')->respond(200, $body);
            try {
                $fake->client()->send->sms(to: self::PHONE, from: self::PHONE, text: 'x');
                self::fail('expected InvalidResponseException');
            } catch (InvalidResponseException $e) {
                self::assertStringContainsString('접수됐을 수 있으니', $e->getMessage());
            }
        }
        try {
            Transport::parse(new HttpResponse(200, [], 'not json'));
        } catch (InvalidResponseException $e) {
            self::assertStringNotContainsString('접수', $e->getMessage(), 'no hint for other operations');
        }
    }

    /** L3: the key and the webhook secret are not in var_export(), casts, serialize() or json_encode(). */
    public function testSecretsAreNotExportable(): void
    {
        $client = new BizgoClient(apiKey: FakeTransport::API_KEY, proxy: 'http://user:proxy-secret@proxy.example:3128');
        $receiver = new WebhookReceiver('whsec-not-real');
        $dumps = [
            var_export($client, true),
            var_export($client->send, true),
            var_export($receiver, true),
            print_r((array) $client, true),
            print_r((array) $receiver, true),
            (string) json_encode($client),
            (string) json_encode($receiver),
        ];
        foreach ([$client, $client->send] as $object) {
            try {
                $dumps[] = serialize($object);
            } catch (\LogicException) {
                $dumps[] = '';
            }
        }
        foreach ($dumps as $dump) {
            self::assertStringNotContainsString(FakeTransport::API_KEY, $dump);
            self::assertStringNotContainsString('whsec-not-real', $dump);
            self::assertStringNotContainsString('proxy-secret', $dump);
        }
    }

    /** @return iterable<string, array{string, string, string}> input, method, expected */
    public static function dates(): iterable
    {
        yield 'naive MO time is KST' => ['2026-09-01T00:00:00', 'offsetTime', '2026-09-01T00:00:00+09:00'];
        yield 'Z converted to KST' => ['2026-09-01T00:00:00Z', 'offsetTime', '2026-09-01T09:00:00+09:00'];
        yield 'offset converted' => ['2026-09-01T00:00:00+00:00', 'localTime', '2026-09-01T09:00:00'];
        yield 'space separator' => ['2026-09-01 10:00:00', 'localTime', '2026-09-01T10:00:00'];
        yield 'local stays' => ['2026-09-01T10:00:00', 'localTime', '2026-09-01T10:00:00'];
        yield 'date' => ['20260901', 'yyyymmdd', '20260901'];
        yield 'dashed date' => ['2026-09-01', 'yyyymmdd', '20260901'];
    }

    /** L5: date strings are parsed and normalised like the other SDKs. */
    #[DataProvider('dates')]
    public function testDateStringsAreNormalised(string $input, string $method, string $expected): void
    {
        $normalise = [\Bizgo\Internal\Dates::class, $method];
        self::assertIsCallable($normalise);
        self::assertSame($expected, $normalise($input));
    }

    public function testInvalidDatesAreRefusedBeforeSending(): void
    {
        $fake = new FakeTransport();
        $client = $fake->client();
        foreach (
            [
            static fn () => $client->messages->history(requestTime: 'nope'),
            static fn () => $client->messages->history(requestTime: '2026-02-30T00:00:00'),
            static fn () => $client->messages->statistics(startDate: '2026-13-01'),
            static fn () => $client->messages->moHistory(occurredTime: '2026-09-01T25:00:00'),
            static fn () => $client->insights->alimtalk->get(startDate: '20261301', endDate: '20260101', senderKey: 'S'),
            ] as $call
        ) {
            try {
                $call();
                self::fail('expected ValidationException');
            } catch (ValidationException) {
                self::addToAssertionCount(1);
            }
        }
        self::assertSame([], $fake->requests());
    }

    /** L6: int64 cursors up to PHP_INT_MAX; larger numbers in string fields are kept exactly. */
    public function testLargeNumbers(): void
    {
        $fake = new FakeTransport();
        $fake->on('getMessageHistory')->respond(200, '{"common":{"authCode":"A000"},"data":{"code":"A000","data":{"messages":[],"lastSeq":9223372036854775807,"hasNext":false}}}');
        self::assertSame(PHP_INT_MAX, $fake->client()->messages->history(requestTime: '2026-09-01T00:00:00')->lastSeq);

        $body = '{"msgKey":"k","serviceType":"SMS","reportTime":"t","reportType":"x","reportCode":"A000","resCnt":123456789012345678901234}';
        self::assertSame('123456789012345678901234', WebhookReceiver::parseReport($body)->resCnt);
    }

    public function testSmsSendStillWorksEndToEnd(): void
    {
        $result = (new FakeTransport())->client()->send->omni(to: self::PHONE, messages: [new SmsMessage(from: self::PHONE, text: 'x')]);
        self::assertCount(1, $result->msgKeys());
    }
}
