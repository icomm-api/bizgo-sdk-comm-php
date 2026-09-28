<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Exception\ApiException;
use Bizgo\Exception\BadRequestException;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\DuplicateRequestException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\ValidationException;
use Bizgo\Exception\WebhookPayloadException;
use Bizgo\Exception\WebhookVerificationException;
use Bizgo\Http\HttpResponse;
use Bizgo\Http\Transport;
use Bizgo\Internal\Mask;
use Bizgo\Model\Destination;
use Bizgo\Model\MoMessage;
use Bizgo\Model\SmsMessage;
use Bizgo\Result\SendResult;
use Bizgo\Testing\FakeTransport;
use Bizgo\Upload;
use Bizgo\Webhook\WebhookReceiver;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * One regression test (at least) per hardening rule of SDK-DESIGN.md §12. Rules covered elsewhere say where.
 */
final class HardeningTest extends TestCase
{
    private static function sms(): SmsMessage
    {
        return new SmsMessage(from: '01000000000', text: 'hi');
    }

    /** §12.1: parse failures are InvalidResponseException with status, trackingId and body; sends say "may be accepted". */
    public function testRule01ParseFailureIsAnSdkException(): void
    {
        $fake = new FakeTransport();
        $body = ['common' => ['authCode' => 'A000', 'infobankTrId' => 'TR-1'], 'data' => ['code' => 'A000', 'data' => ['destinations' => 'oops']]];
        $fake->on('sendOmni')->respond(200, $body);
        try {
            $fake->client()->send->omni(to: '01000000000', messages: [self::sms()]);
            self::fail('expected InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertSame(200, $e->httpStatus);
            self::assertSame('TR-1', $e->trackingId);
            self::assertSame($body, $e->body);
            self::assertStringContainsString('접수됐을 수 있으니', $e->getMessage());
        }
        // envelope text fields are optional: no authResult/result is still a success
        $fake->on('getReportInquiry')->respond(200, ['common' => ['authCode' => 'A000'], 'data' => ['code' => 'A000']]);
        self::assertSame([], $fake->client()->reports->inquiry('KEY001'));
        // a non-send operation has no "may be accepted" hint
        $fake->on('getMessageStatusByMsgKey')->respond(200, ['data' => ['code' => 'A000', 'data' => ['messages' => 5]]]);
        try {
            $fake->client()->messages->status('KEY001');
            self::fail('expected InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertStringNotContainsString('접수', $e->getMessage());
        }
    }

    /** §12.2 and §12.3 (more in BulkTest): a failing chunk never loses the others; the cross-SDK key vector. */
    public function testRule02And03Bulk(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->timeout()->default();
        $result = $fake->client()->send->bulk(to: ['01000000000', '01000000001', '01000000002'], messages: [self::sms()], chunkSize: 2, idempotencyKeyPrefix: 'camp');
        self::assertSame([], $result->errors, "the timed-out chunk is retried safely thanks to its key");
        self::assertCount(3, $result->msgKeys());
        $keys = array_map(static fn (\Bizgo\Testing\RecordedRequest $r): mixed => is_array($r->json) ? $r->json['idempotencyKey'] ?? null : null, $fake->requests());
        // the timed-out first chunk was retried (safe with the key), so it appears twice
        self::assertSame(['camp-2-0-32fe30d2', 'camp-2-0-32fe30d2', 'camp-2-2-370752d8'], $keys);
    }

    /** @return iterable<string, array{\Closure(\Bizgo\Testing\Stub): mixed}> */
    public static function firstFailures(): iterable
    {
        yield 'timeout' => [static fn (\Bizgo\Testing\Stub $s): mixed => $s->timeout()];
        yield '503' => [static fn (\Bizgo\Testing\Stub $s): mixed => $s->respond(503, 'busy')];
        yield '429' => [static fn (\Bizgo\Testing\Stub $s): mixed => $s->fail('gateway', 429, 'A020', retryAfter: 0)];
    }

    /**
     * §12.4: A301 after any retry means an earlier attempt was accepted.
     *
     * @param \Closure(\Bizgo\Testing\Stub): mixed $first
     */
    #[DataProvider('firstFailures')]
    public function testRule04DuplicateAfterAnyRetryIsAlreadyAccepted(\Closure $first): void
    {
        $fake = new FakeTransport();
        $stub = $fake->on('sendOmni');
        $first($stub);
        $stub->fail('service', 200, 'A301');
        try {
            $fake->client()->send->omni(to: '01000000000', messages: [self::sms()], idempotencyKey: 'order-1');
            self::fail('expected DuplicateRequestException');
        } catch (DuplicateRequestException $e) {
            self::assertTrue($e->alreadyAccepted);
            self::assertStringContainsString('이전 시도가 이미 접수', $e->getMessage());
        }
        $fake->reset();
        $fake->on('sendOmni')->fail('service', 200, 'A301');
        try {
            $fake->client()->send->omni(to: '01000000000', messages: [self::sms()], idempotencyKey: 'order-1');
        } catch (DuplicateRequestException $e) {
            self::assertFalse($e->alreadyAccepted, 'first attempt: a real duplicate');
        }
    }

    /** §12.5: dot segments refused, everything else one encoded segment (generated methods too). */
    public function testRule05PathValues(): void
    {
        $fake = new FakeTransport();
        $client = $fake->client();
        $client->alimtalk->templates->delete(senderKey: 'a/b?c#d%e', templateCode: 'T 1');
        self::assertSame('/api/comm/v1/center/alimtalk/template/senderKey/a%2Fb%3Fc%23d%25e/templateCode/T%201', $fake->lastRequest()->path);
        foreach (['.', '..', ''] as $bad) {
            try {
                $client->reservations->get(resvKey: $bad);
                self::fail('expected ValidationException');
            } catch (ValidationException $e) {
                self::assertSame('resvKey', $e->errors[0]['path']);
            }
        }
        self::assertCount(1, $fake->requests());
    }

    /** §12.6: the key header cannot be replaced; clients with their own auth/cookies and URL credentials are refused. */
    public function testRule06AuthorizationCannotBeTakenOver(): void
    {
        // clients the SDK cannot inspect are refused unless explicitly trusted (Guzzle/Symfony: HttpClientPolicyTest)
        $generic = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new \LogicException('must not be called');
            }
        };
        $factory = new Psr17Factory();
        try {
            new BizgoClient(apiKey: self::API_KEY, httpClient: $generic, requestFactory: $factory, streamFactory: $factory);
            self::fail('expected ConfigurationException');
        } catch (ConfigurationException $e) {
            self::assertStringContainsString('trustHttpClient', $e->getMessage());
        }

        // the SDK sets Authorization/User-Agent/X-Bizgo-Client itself on every request
        $this->http->push(self::reply(self::envelope(['report' => []])));
        $this->client->reports->inquiry('KEY001');
        $request = $this->http->last();
        self::assertSame([self::API_KEY], $request->getHeader('Authorization'));
        self::assertCount(1, $request->getHeader('User-Agent'));
        foreach (['https://user:pw@mars.ibapi.kr', 'https://mars.ibapi.kr?x=1', 'https://mars.ibapi.kr#f'] as $url) {
            try {
                new BizgoClient(apiKey: self::API_KEY, baseUrl: $url);
                self::fail('expected ConfigurationException');
            } catch (ConfigurationException) {
                self::addToAssertionCount(1);
            }
        }
    }

    /** §12.6: the built-in cURL transport never follows redirects nor authenticates by itself. */
    public function testRule06CurlOptions(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../src/Http/CurlSender.php');
        self::assertStringContainsString('CURLOPT_FOLLOWLOCATION => false', $source);
        self::assertStringContainsString('CURLOPT_MAXREDIRS => 0', $source);
        foreach (['CURLOPT_HTTPAUTH', 'CURLOPT_USERPWD', 'CURLOPT_COOKIE', 'CURLOPT_SSL_VERIFYPEER => false', 'CURLOPT_SSL_VERIFYHOST => 0'] as $never) {
            self::assertStringNotContainsString($never, $source);
        }
    }

    /** §12.7: webhook input: timestamp 1-16 digits, JSON/depth/shape errors are payload errors, secret/tolerance checks. */
    public function testRule07WebhookInput(): void
    {
        $receiver = new WebhookReceiver('test-webhook-secret');
        foreach (['', '12345678901234567', '12a', ' 1700000000'] as $timestamp) {
            try {
                WebhookReceiver::verifySignature('test-webhook-secret', $timestamp, 'abcd', null);
                self::fail('expected WebhookVerificationException');
            } catch (WebhookVerificationException $e) {
                self::assertStringContainsString('X-IB-Timestamp', $e->getMessage());
            }
        }
        $deep = str_repeat('{"a":', 70) . '1' . str_repeat('}', 70);
        foreach (['not json', '[1,2]', $deep, '{"msgKey":{"x":1}}', '{"msgKey":"KEY001"}'] as $body) {
            try {
                WebhookReceiver::parseMo($body);
                self::fail('expected WebhookPayloadException');
            } catch (WebhookPayloadException) {
                self::addToAssertionCount(1);
            }
        }
        foreach ([' ', "\n"] as $secret) {
            try {
                new WebhookReceiver($secret);
                self::fail('expected WebhookVerificationException');
            } catch (WebhookVerificationException) {
                self::addToAssertionCount(1);
            }
        }
        foreach ([0.0, -5.0, INF, NAN] as $tolerance) {
            try {
                new WebhookReceiver('test-webhook-secret', $tolerance);
                self::fail('expected WebhookVerificationException');
            } catch (WebhookVerificationException) {
                self::addToAssertionCount(1);
            }
        }
        self::assertSame(['msgKey' => 'KEY001'], $receiver::ack('KEY001'));
        new WebhookReceiver('test-webhook-secret', null); // explicit opt-out is allowed
    }

    /** §12.8: 64 levels of JSON nesting are accepted, 65 are an InvalidResponseException (size cap: CurlSenderTest). */
    public function testRule08JsonDepth(): void
    {
        $nested = static fn (int $levels): string => '{"common":{"authCode":"A000"},"data":{"code":"A000","x":'
            . str_repeat('[', $levels - 2) . str_repeat(']', $levels - 2) . '}}';
        self::assertIsArray(Transport::parse(new HttpResponse(200, [], $nested(64))));
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('64');
        Transport::parse(new HttpResponse(200, [], $nested(65)));
    }

    /** §12.9: only a finite, non-negative number of seconds (max 60) is used from Retry-After. */
    public function testRule09RetryAfter(): void
    {
        $values = ['5' => 5.0, '0' => 0.0, '1.5' => 1.5, '3600' => 60.0, '-1' => null, 'NaN' => null, 'INF' => null, '1e3' => null,
            'Wed, 21 Oct 2026 07:28:00 GMT' => null, '' => null];
        foreach ($values as $header => $expected) {
            self::assertSame($expected, Transport::retryAfter(new HttpResponse(429, ['retry-after' => (string) $header], '')), (string) $header);
        }
    }

    /** §12.10: API exceptions survive serialize()/unserialize() (job queues). */
    public function testRule10ExceptionsAreSerializable(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->fail('service', 200, 'A020');
        try {
            $fake->client(maxRetries: 0)->send->omni(to: '01000000000', messages: [self::sms()]);
            self::fail('expected RateLimitException');
        } catch (RateLimitException $e) {
            $copy = unserialize(serialize($e));
            self::assertInstanceOf(RateLimitException::class, $copy);
            self::assertSame(
                [$e->getMessage(), $e->errorCode, $e->layer, $e->httpStatus, $e->trackingId, $e->description],
                [$copy->getMessage(), $copy->errorCode, $copy->layer, $copy->httpStatus, $copy->trackingId, $copy->description]
            );
        }
        $duplicate = new DuplicateRequestException('dup', 200, 'A301', ApiException::LAYER_SERVICE, 'TR', null, true);
        $copy = unserialize(serialize($duplicate));
        self::assertInstanceOf(DuplicateRequestException::class, $copy);
        self::assertTrue($copy->alreadyAccepted);
        $invalid = unserialize(serialize(new InvalidResponseException('bad', 502, 'TR', ['a' => 1])));
        self::assertInstanceOf(InvalidResponseException::class, $invalid);
        self::assertSame([502, 'TR', ['a' => 1]], [$invalid->httpStatus, $invalid->trackingId, $invalid->body]);
    }

    /** §12.11: phone numbers masked, content length only, person data first character — in models and results. */
    public function testRule11Masking(): void
    {
        self::assertSame('010****0000', Mask::phone('01000000000'));
        self::assertSame('158****0', Mask::phone('15880000'));
        self::assertSame('021*****0', Mask::phone('021000000'));
        self::assertSame('*******', Mask::phone('1234567'));
        $message = new SmsMessage(from: '01000001234', text: '인증번호 123456');
        $destination = new Destination(to: '01000005678');
        $mo = MoMessage::fromArray(['from' => '01000009876', 'content' => '회신 내용', 'userName' => '홍길동']);
        $result = new SendResult([\Bizgo\Model\SendDestinationResult::fromArray(['to' => '01000004321', 'msgKey' => 'K', 'code' => 'A000'])]);
        $text = (string) $message . (string) $destination . (string) $mo . (string) $result
            . print_r([$message, $destination, $mo, $result], true);
        ob_start();
        var_dump($message, $mo, $result);
        $text .= (string) ob_get_clean();
        foreach (['01000001234', '01000005678', '01000009876', '01000004321', '123456', '회신 내용', '홍길동'] as $secret) {
            self::assertStringNotContainsString($secret, $text);
        }
        self::assertStringContainsString('010****1234', $text);
        self::assertSame('01000001234', $message->from, 'the value itself is unchanged');
    }

    /** §12.12: the redirect error names the status (SECURITY.md documents the TLS behaviour of injected clients). */
    public function testRule12RedirectMessageHasTheStatus(): void
    {
        try {
            Transport::parse(new HttpResponse(307, ['location' => 'https://elsewhere.example'], ''));
            self::fail('expected InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertStringContainsString('HTTP 307', $e->getMessage());
            self::assertStringNotContainsString('elsewhere', $e->getMessage());
        }
        self::assertStringContainsString('TLS', (string) file_get_contents(__DIR__ . '/../SECURITY.md'));
    }

    /** §12.13: examples are analysed by PHPStan (phpstan.neon.dist paths) and README code blocks parse; see ExamplesTest. */
    public function testRule13ReadmeCodeParses(): void
    {
        $readme = (string) file_get_contents(__DIR__ . '/../README.md');
        preg_match_all('/```php\n(.*?)```/s', $readme, $m);
        self::assertNotEmpty($m[1]);
        foreach ($m[1] as $i => $code) {
            $source = str_starts_with(ltrim($code), '<?php') ? $code : "<?php\n" . $code;
            try {
                self::assertNotEmpty(token_get_all($source, TOKEN_PARSE));
            } catch (\ParseError $e) {
                self::fail("README php block {$i}: " . $e->getMessage());
            }
        }
    }

    /** §12.14: buckets start full; see RateLimitTest::testBucketStartsFullAndRefillsContinuously. */
    public function testRule14InitialBurst(): void
    {
        $bucket = new \Bizgo\Internal\TokenBucket(200.0, static fn (): float => 0.0);
        self::assertSame(0.0, $bucket->reserve(200));
    }

    /** §12.15 (more in HooksTest): a throwing logger cannot lose an accepted send. */
    public function testRule15CallbackIsolation(): void
    {
        $logger = new class extends \Psr\Log\AbstractLogger {
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                throw new \Error('broken logger');
            }
        };
        $result = (new FakeTransport())->client(logger: $logger)->send->omni(to: '01000000000', messages: [self::sms()]);
        self::assertCount(1, $result->msgKeys());
    }

    /** §12.16: the key must be printable ASCII without spaces; surrounding whitespace is reported, never trimmed. */
    public function testRule16ApiKeyFormat(): void
    {
        foreach ([" test-api-key-not-real", "test-api-key-not-real\n", "키-not-ascii", "a\tb"] as $key) {
            try {
                new BizgoClient(apiKey: $key);
                self::fail('expected ConfigurationException');
            } catch (ConfigurationException $e) {
                self::assertStringNotContainsString('not-real', $e->getMessage());
            }
        }
        self::assertSame('BizgoClient(baseUrl=https://mars.ibapi.kr)', (string) new BizgoClient(apiKey: 'Aa0!~key'));
    }

    /** §12.17: a redirect is never retried, even for a safe operation. */
    public function testRule17RedirectsAreNotRetried(): void
    {
        $fake = new FakeTransport();
        $fake->on('getReportInquiry')->respond(302, '', ['location' => '/elsewhere']);
        try {
            $fake->client(maxRetries: 3)->reports->inquiry('KEY001');
            self::fail('expected InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertSame(302, $e->httpStatus);
        }
        self::assertCount(1, $fake->requests());
    }

    /** §12.18: an unreadable file is a ValidationException naming the file name only, without PHP warnings. */
    public function testRule18FileErrors(): void
    {
        $missing = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bizgo-private-dir' . DIRECTORY_SEPARATOR . 'missing-banner.jpg';
        $warnings = [];
        set_error_handler(static function (int $no, string $message) use (&$warnings): bool {
            $warnings[] = $message;

            return true;
        });
        try {
            Upload::fromPath($missing);
            self::fail('expected ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString('missing-banner.jpg', $e->getMessage());
            self::assertStringNotContainsString('bizgo-private-dir', $e->getMessage());
        } finally {
            restore_error_handler();
        }
        self::assertSame([], $warnings);
        $fake = new FakeTransport();
        try {
            $fake->client()->files->uploadAlimtalkTemplateImage(file: $missing);
            self::fail('expected ValidationException');
        } catch (ValidationException $e) {
            self::assertSame('file', $e->errors[0]['path']);
        }
        self::assertSame([], $fake->requests());
    }

    /** §12.19: a success without `data.data` gives an empty result object or list, never null. */
    public function testRule19NoNullResults(): void
    {
        $fake = new FakeTransport();
        $client = $fake->client();
        $empty = FakeTransport::success(null);
        $fake->on('getAlimtalkTemplate')->respond(200, $empty);
        self::assertNull($client->alimtalk->templates->get(senderKey: 'S', templateCode: 'T')->templateCode);
        $fake->on('listAlimtalkTemplates')->respond(200, ['common' => ['authCode' => 'A000'], 'data' => ['code' => 'A000', 'data' => null]]);
        self::assertSame([], $client->alimtalk->templates->list(senderKey: 'S'));
        $fake->on('getReportPolling')->respond(200, $empty);
        self::assertTrue($client->reports->poll()->isEmpty());
        $fake->on('uploadMmsFile')->respond(200, $empty);
        self::assertNull($client->files->uploadMms(Upload::fromContents('x', 'a.jpg'))->fileKey);
        $fake->on('createReservation')->respond(200, $empty);
        $reservation = $client->reservations->create([
            'destinations' => [['to' => '01000000000']],
            'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'hi']]],
            'resvSendTime' => '2026-12-01 10:00:00',
        ]);
        self::assertNull($reservation->resvKey);
    }

    /** A service error on HTTP 200 of a generated operation maps like the P0 ones. */
    public function testGeneratedOperationsMapErrorsLikeP0(): void
    {
        $fake = new FakeTransport();
        $fake->on('getAlimtalkTemplate')->fail('service', 200, 'A306');
        $this->expectException(BadRequestException::class);
        $fake->client()->alimtalk->templates->get(senderKey: 'S', templateCode: 'T');
    }
}
