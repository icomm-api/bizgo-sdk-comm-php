<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Internal\TokenBucket;
use Bizgo\Model\ReservationCreateRequest;
use Bizgo\Model\SmsMessage;
use Bizgo\RateLimit;
use Bizgo\Testing\FakeTransport;
use PHPUnit\Framework\TestCase;

/** Client-side token buckets with a fake clock (SDK-DESIGN.md §11.2, §11.5, §12.14). */
final class RateLimitTest extends TestCase
{
    private float $now = 1000.0;

    /** @var list<float> */
    private array $sleeps = [];

    private FakeTransport $fake;

    protected function setUp(): void
    {
        $this->fake = new FakeTransport();
    }

    private function client(RateLimit $limit = new RateLimit(), int $maxRetries = 2): BizgoClient
    {
        return $this->fake->client(
            maxRetries: $maxRetries,
            rateLimit: $limit,
            sleep: function (float $seconds): void {
                $this->sleeps[] = $seconds;
                $this->now += $seconds;
            },
            clock: fn (): float => $this->now,
        );
    }

    /** @return list<string> */
    private static function numbers(int $count): array
    {
        return array_map(static fn (int $i): string => sprintf('010000%05d', $i), range(0, $count - 1));
    }

    public function testBucketStartsFullAndRefillsContinuously(): void
    {
        $now = 0.0;
        $bucket = new TokenBucket(5.0, static function () use (&$now): float {
            return $now;
        });
        for ($i = 0; $i < 5; $i++) {
            self::assertSame(0.0, $bucket->reserve(), 'initial burst of one second (§12.14)');
        }
        self::assertEqualsWithDelta(0.2, $bucket->reserve(), 1e-9);
        $now = 10.0;
        self::assertSame(0.0, $bucket->reserve(), 'refilled, capped at the capacity');
        self::assertEqualsWithDelta(4.0, $bucket->tokens(), 1e-9);
    }

    public function testCostAboveCapacityWaitsForAFullBucketAndLeavesDebt(): void
    {
        $now = 0.0;
        $bucket = new TokenBucket(100.0, static function () use (&$now): float {
            return $now;
        });
        self::assertSame(0.0, $bucket->reserve(200), 'a full bucket lets it through');
        self::assertEqualsWithDelta(-100.0, $bucket->tokens(), 1e-9, 'the rest is debt');
        self::assertEqualsWithDelta(2.0, $bucket->reserve(200), 1e-9, 'the next one waits until the bucket is full again');
    }

    public function testOtherOperationsShareFiveRequestsPerSecond(): void
    {
        $client = $this->client();
        for ($i = 0; $i < 7; $i++) {
            $client->reports->inquiry('KEY001');
        }
        self::assertCount(2, $this->sleeps);
        self::assertEqualsWithDelta(0.2, $this->sleeps[0], 1e-9);
    }

    public function testSendCostsOneTokenPerRecipient(): void
    {
        $client = $this->client();
        $sms = new SmsMessage(from: '01000000000', text: 'hi');
        $client->send->omni(to: self::numbers(150), messages: [$sms]);
        self::assertSame([], $this->sleeps, '150 of 200 tokens');
        $client->send->omni(to: self::numbers(100), messages: [$sms]);
        self::assertCount(1, $this->sleeps);
        self::assertEqualsWithDelta(0.25, $this->sleeps[0], 1e-9, '50 missing tokens at 200/s');
        // the other bucket is independent
        $client->messages->status('KEY001');
        self::assertCount(1, $this->sleeps);
    }

    /** §11.5: `x-sdk-rate: send` operations (not a hard-coded list) use the send bucket, by recipient count. */
    public function testReservationsUseTheSendBucket(): void
    {
        $client = $this->client(new RateLimit(send: 10, other: 1000));
        $request = ReservationCreateRequest::fromArray([
            'destinations' => array_map(static fn (string $n): array => ['to' => $n], self::numbers(10)),
            'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'hi']]],
            'resvSendTime' => '2026-12-01 10:00:00',
        ]);
        $client->reservations->create($request);
        $client->reservations->create($request);
        self::assertEqualsWithDelta([1.0], $this->sleeps, 1e-9);
    }

    public function testCounselSendsCostOneSendToken(): void
    {
        $client = $this->client(new RateLimit(send: 1, other: 1000));
        $body = \Bizgo\Tests\Support\SpecSamples::value(['$ref' => '#/components/schemas/CounselPlainMessageRequest']);
        self::assertIsArray($body);
        $request = \Bizgo\Model\CounselPlainMessageRequest::fromArray($body);
        $client->counsel->messages->sendPlain($request);
        $client->counsel->messages->sendPlain($request);
        self::assertEqualsWithDelta([1.0], $this->sleeps, 1e-9, 'send bucket, cost 1 (no recipient list)');
        $client->counsel->sessions->get(userKey: 'USER_KEY_EXAMPLE');
        self::assertCount(1, $this->sleeps, 'non-send counsel APIs use the other bucket');
    }

    public function testEveryAttemptWaitsIncludingRetries(): void
    {
        $this->fake->on('getReportInquiry')->fail('gateway', 429, 'A020', retryAfter: 0)->default();
        $client = $this->client(new RateLimit(send: 200, other: 1));
        $client->reports->inquiry('KEY001');
        self::assertCount(2, $this->fake->requests());
        // first attempt: full bucket; retry (after Retry-After 0): waits one second for its token
        self::assertEqualsWithDelta([0.0, 1.0], $this->sleeps, 1e-9);
    }

    public function testRateLimitCanBeTurnedOff(): void
    {
        $client = $this->fake->client(rateLimit: null, sleep: function (float $s): void {
            $this->sleeps[] = $s;
        });
        for ($i = 0; $i < 20; $i++) {
            $client->reports->inquiry('KEY001');
        }
        self::assertSame([], $this->sleeps);
    }

    public function testInvalidRatesAreRejected(): void
    {
        foreach ([0.0, -1.0, INF, NAN] as $rate) {
            try {
                new RateLimit(send: $rate);
                self::fail('expected ConfigurationException');
            } catch (ConfigurationException) {
                self::addToAssertionCount(1);
            }
        }
    }

    public function testDefaultsAreTwoHundredMessagesAndFiveRequests(): void
    {
        $limit = new RateLimit();
        self::assertSame([200.0, 5.0], [$limit->send, $limit->other]);
    }
}
