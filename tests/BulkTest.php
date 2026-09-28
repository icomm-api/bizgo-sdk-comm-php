<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Exception\BulkSendException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\DuplicateRequestException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\HttpRequest;
use Bizgo\Http\HttpResponse;
use Bizgo\Http\HttpSender;
use Bizgo\Model\Destination;
use Bizgo\Model\SmsMessage;
use Bizgo\RateLimit;
use Bizgo\Resource\Send;
use Bizgo\Testing\FakeTransport;
use PHPUnit\Framework\TestCase;

/** `$client->send->bulk()` (SDK-DESIGN.md §11.1, §12.2, §12.3). */
final class BulkTest extends TestCase
{
    /** @return list<string> placeholder numbers 010000000xx */
    private static function numbers(int $count): array
    {
        return array_map(static fn (int $i): string => sprintf('010000%05d', $i), range(0, $count - 1));
    }

    private static function sms(): SmsMessage
    {
        return new SmsMessage(from: '01000000000', text: '안내');
    }

    /** The cross-SDK test vector of §12.3: every language must produce exactly these keys. */
    public function testIdempotencyKeysMatchTheCrossSdkVector(): void
    {
        $fake = new FakeTransport();
        $result = $fake->client()->send->bulk(
            to: ['01000000000', '01000000001', '01000000002'],
            messages: [self::sms()],
            chunkSize: 2,
            idempotencyKeyPrefix: 'camp',
        );
        $keys = array_map(static fn ($r): mixed => is_array($r->json) ? $r->json['idempotencyKey'] ?? null : null, $fake->requests());
        self::assertSame(['camp-2-0-32fe30d2', 'camp-2-2-370752d8'], $keys);
        self::assertSame('camp-2-0-32fe30d2', Send::bulkIdempotencyKey('camp', 2, 0, [new Destination('01000000000'), new Destination('01000000001')]));
        self::assertTrue($result->ok());
        self::assertCount(3, $result->msgKeys());
    }

    public function testChunksKeepOrderAndCount(): void
    {
        $fake = new FakeTransport();
        $result = $fake->client()->send->bulk(to: self::numbers(450), messages: [self::sms()]);
        $sizes = array_map(static fn ($r): int => is_array($r->json) && is_array($r->json['destinations'] ?? null) ? count($r->json['destinations']) : 0, $fake->requests());
        self::assertSame([200, 200, 50], $sizes);
        self::assertCount(3, $result->results);
        self::assertCount(450, $result->succeeded());
        self::assertSame([], $result->errors);
        self::assertArrayNotHasKey('idempotencyKey', is_array($fake->requests()[0]->json) ? $fake->requests()[0]->json : []);
    }

    public function testAFailedChunkDoesNotStopTheOthers(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->default()->fail('gateway', 429, 'A020', retryAfter: 0)->default();
        $result = $fake->client(maxRetries: 0)->send->bulk(to: self::numbers(5), messages: [self::sms()], chunkSize: 2);
        self::assertCount(3, $fake->requests());
        self::assertCount(1, $result->errors);
        $error = $result->errors[0];
        self::assertSame([1, 2, 4], [$error->chunkIndex, $error->start, $error->end]);
        self::assertInstanceOf(RateLimitException::class, $error->error);
        self::assertNull($result->results[1]);
        self::assertCount(3, $result->succeeded(), 'chunks 0 and 2');
        self::assertFalse($result->ok());
        // indexes only: never the recipients
        self::assertStringNotContainsString('0100000', (string) $error);
        self::assertStringNotContainsString('0100000', (string) $result);
    }

    public function testRejectedRecipientsAreReportedPerChunk(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->sendResult('A000', 'A306');
        $result = $fake->client()->send->bulk(to: self::numbers(2), messages: [self::sms()]);
        self::assertCount(1, $result->failed());
        self::assertSame('A306', $result->failed()[0]->code);
    }

    public function testDuplicatesAreAggregatedAcrossChunks(): void
    {
        $fake = new FakeTransport();
        // re-run: chunk 0 was fully accepted before (all A301), chunk 1 partly, chunk 2 is new
        $fake->on('sendOmni')->sendResult('A301', 'A301')->sendResult('A301', 'A306')->sendResult('A000');
        $result = $fake->client()->send->bulk(to: self::numbers(5), messages: [self::sms()], chunkSize: 2, idempotencyKeyPrefix: 'camp');
        self::assertSame([], $result->errors, 'an all-A301 chunk is not an error');
        self::assertCount(3, $result->duplicates());
        self::assertSame(['A306'], array_map(static fn ($d) => $d->code, $result->failed()));
        self::assertCount(1, $result->succeeded());
        self::assertCount(1, $result->msgKeys());
        self::assertFalse($result->ok(), 'A306 is a rejection');
        self::assertStringContainsString('succeeded=1, duplicates=3, failed=1', (string) $result);
        self::assertStringNotContainsString('0100000', (string) $result);
    }

    public function testAllDuplicateRerunIsOk(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->sendResult('A301', 'A301');
        $result = $fake->client()->send->bulk(to: self::numbers(2), messages: [self::sms()], idempotencyKeyPrefix: 'camp');
        self::assertTrue($result->ok());
        self::assertCount(2, $result->duplicates());
        self::assertSame([], $result->failed());
    }

    public function testRequestLevelA301IsStillAChunkError(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->fail('service', 200, 'A301');
        $result = $fake->client()->send->bulk(to: self::numbers(2), messages: [self::sms()], idempotencyKeyPrefix: 'camp');
        self::assertCount(1, $result->errors);
        self::assertInstanceOf(DuplicateRequestException::class, $result->errors[0]->error);
        self::assertSame([], $result->duplicates());
    }

    public function testNetworkErrorsAreKeptPerChunk(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->networkError()->default();
        $result = $fake->client()->send->bulk(to: self::numbers(4), messages: [self::sms()], chunkSize: 2);
        self::assertInstanceOf(ConnectionException::class, $result->errors[0]->error);
        self::assertCount(2, $result->msgKeys());
    }

    /** §12.2: a PHP Error stops the loop, but the chunks already accepted are handed back in the exception. */
    public function testAnErrorKeepsTheAcceptedChunks(): void
    {
        $fake = new FakeTransport();
        $sender = new class ($fake) implements HttpSender {
            private int $calls = 0;

            public function __construct(private readonly FakeTransport $fake)
            {
            }

            public function send(HttpRequest $request): HttpResponse
            {
                if (++$this->calls === 2) {
                    throw new \Error('simulated fatal error');
                }

                return $this->fake->send($request);
            }

            public function name(): string
            {
                return 'failing';
            }
        };
        $client = new BizgoClient(apiKey: FakeTransport::API_KEY, rateLimit: null, sender: $sender);
        try {
            $client->send->bulk(to: self::numbers(6), messages: [self::sms()], chunkSize: 2);
            self::fail('expected BulkSendException');
        } catch (BulkSendException $e) {
            self::assertSame(1, $e->chunkIndex);
            self::assertInstanceOf(\Error::class, $e->getPrevious());
            self::assertCount(2, $e->partial->msgKeys(), 'the accepted first chunk is not lost');
            self::assertStringNotContainsString('0100000', $e->getMessage());
        }
        self::assertCount(1, $fake->requests(), 'nothing after the failing chunk is sent');
    }

    /** An unparsable response is a chunk error (InvalidResponseException), not a stop. */
    public function testAnInvalidResponseIsAChunkError(): void
    {
        $fake = new FakeTransport();
        $fake->on('sendOmni')->default()->respond(200, '{"common":{"authCode":"A000"},"data":{"code":"A000","data":{"destinations":"not-a-list"}}}')->default();
        $result = $fake->client()->send->bulk(to: self::numbers(6), messages: [self::sms()], chunkSize: 2);
        self::assertCount(1, $result->errors);
        self::assertInstanceOf(InvalidResponseException::class, $result->errors[0]->error);
        self::assertCount(4, $result->msgKeys());
    }

    public function testEverythingIsValidatedBeforeSending(): void
    {
        $fake = new FakeTransport();
        try {
            $fake->client()->send->bulk(to: ['01000000000', ['to' => '01000000001', 'bogus' => 1], '01000000002'], messages: [self::sms()], chunkSize: 1);
            self::fail('expected ValidationException');
        } catch (ValidationException $e) {
            self::assertSame('to[1].bogus', $e->errors[0]['path']);
            self::assertStringNotContainsString('01000000001', $e->getMessage());
        }
        self::assertSame([], $fake->requests(), 'nothing was sent');
    }

    public function testOptionsAreValidated(): void
    {
        $send = (new FakeTransport())->client()->send;
        foreach ([[0, 4, null], [201, 4, null], [10, 0, null], [10, 4, ' '], [10, 4, str_repeat('x', 190)]] as [$chunk, $concurrency, $prefix]) {
            try {
                $send->bulk(to: self::numbers(3), messages: [self::sms()], chunkSize: $chunk, concurrency: $concurrency, idempotencyKeyPrefix: $prefix);
                self::fail('expected ValidationException');
            } catch (ValidationException $e) {
                self::assertNotSame([], $e->errors);
            }
        }
        $this->expectException(ValidationException::class);
        $send->bulk(to: [], messages: [self::sms()]);
    }

    /** §11.1/§11.2: with the default 200 messages/s, full chunks go out one per second. */
    public function testChunksArePacedByTheSendBucket(): void
    {
        $now = 0.0;
        $sleeps = [];
        $fake = new FakeTransport();
        $client = $fake->client(
            rateLimit: new RateLimit(),
            sleep: static function (float $seconds) use (&$now, &$sleeps): void {
                $sleeps[] = $seconds;
                $now += $seconds;
            },
            clock: static function () use (&$now): float {
                return $now;
            },
        );
        $client->send->bulk(to: self::numbers(600), messages: [self::sms()]);
        self::assertCount(3, $fake->requests());
        self::assertEqualsWithDelta([1.0, 1.0], $sleeps, 1e-9, 'the first chunk uses the full bucket, the next two wait a second each');
    }
}
