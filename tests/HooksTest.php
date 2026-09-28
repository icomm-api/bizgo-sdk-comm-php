<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Model\SmsMessage;
use Bizgo\Observability\Hook;
use Bizgo\Observability\OpenTelemetryHook;
use Bizgo\Observability\RequestEvent;
use Bizgo\Testing\FakeTransport;
use Bizgo\Tests\Support\MemoryLogger;
use Bizgo\Tests\Support\RecordingTracer;
use PHPUnit\Framework\TestCase;

/** Request hooks and the OpenTelemetry adapter (SDK-DESIGN.md §11.4, §12.15). */
final class HooksTest extends TestCase
{
    /** @return Hook&object{starts: list<RequestEvent>, ends: list<RequestEvent>} */
    private static function recorder(): Hook
    {
        return new class implements Hook {
            /** @var list<RequestEvent> */
            public array $starts = [];

            /** @var list<RequestEvent> */
            public array $ends = [];

            public function onRequestStart(RequestEvent $event): void
            {
                $this->starts[] = clone $event;
            }

            public function onRequestEnd(RequestEvent $event): void
            {
                $this->ends[] = clone $event;
            }
        };
    }

    public function testEventCarriesOperationTemplateStatusAndTimingOnly(): void
    {
        $hook = self::recorder();
        $now = 10.0;
        $fake = new FakeTransport();
        $fake->on('getReportInquiry')->fail('gateway', 429, 'A020', retryAfter: 0)->data(['report' => []]);
        $client = $fake->client(hooks: [$hook], clock: static function () use (&$now): float {
            $now += 0.5;

            return $now;
        });
        $client->reports->inquiry('01000001234'); // a path value that looks like a phone number

        self::assertCount(1, $hook->starts);
        self::assertCount(1, $hook->ends);
        $end = $hook->ends[0];
        self::assertSame('getReportInquiry', $end->operationId);
        self::assertSame('reports.inquiry', $end->operation);
        self::assertSame('GET', $end->method);
        self::assertSame('/api/comm/v1/report/inquiry/{msgKey}', $end->pathTemplate);
        self::assertSame(200, $end->status);
        self::assertSame(2, $end->attempts);
        self::assertTrue($end->success);
        self::assertNull($end->code);
        self::assertGreaterThan(0, $end->durationMs);
        self::assertNull($hook->starts[0]->status, 'start: operation fields only');
        self::assertStringNotContainsString('01000001234', serialize([$hook->starts, $hook->ends]));
        self::assertStringNotContainsString(FakeTransport::API_KEY, serialize([$hook->starts, $hook->ends]));
    }

    public function testFailureCarriesLayerCodeAndErrorType(): void
    {
        $hook = self::recorder();
        $fake = new FakeTransport();
        $fake->on('sendOmni')->fail('service', 200, 'A020');
        $client = $fake->client(hooks: [$hook], maxRetries: 0);
        try {
            $client->send->sms(to: '01000000000', from: '01000000000', text: '비밀 본문');
            self::fail('expected RateLimitException');
        } catch (RateLimitException) {
            $end = $hook->ends[0];
            self::assertFalse($end->success);
            self::assertSame(['service', 'A020', 'RateLimitException'], [$end->layer, $end->code, $end->errorType]);
            self::assertStringNotContainsString('비밀 본문', serialize($hook->ends));
            self::assertStringNotContainsString('01000000000', serialize($hook->ends));
        }
    }

    /** §12.2: a response that cannot be parsed is not reported as a success. */
    public function testParseFailureIsNotASuccess(): void
    {
        $hook = self::recorder();
        $fake = new FakeTransport();
        $fake->on('getReportInquiry')->respond(200, '{"common":{"authCode":"A000"},"data":{"code":"A000","data":{"report":"x"}}}');
        try {
            $fake->client(hooks: [$hook])->reports->inquiry('KEY001');
            self::fail('expected InvalidResponseException');
        } catch (InvalidResponseException) {
            self::assertFalse($hook->ends[0]->success);
            self::assertSame('InvalidResponseException', $hook->ends[0]->errorType);
        }
    }

    /** §12.15: exceptions from hooks and loggers are swallowed; the result is unchanged. */
    public function testThrowingHooksAndLoggersDoNotChangeResults(): void
    {
        $bad = new class implements Hook {
            public function onRequestStart(RequestEvent $event): void
            {
                throw new \RuntimeException('hook start failed');
            }

            public function onRequestEnd(RequestEvent $event): void
            {
                throw new \Error('hook end failed');
            }
        };
        $logger = new class extends \Psr\Log\AbstractLogger {
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                throw new \RuntimeException('logger failed');
            }
        };
        $good = self::recorder();
        $client = (new FakeTransport())->client(hooks: [$bad, $good], logger: $logger);
        $result = $client->send->sms(to: '01000000000', from: '01000000000', text: 'hi');
        self::assertCount(1, $result->msgKeys(), 'the accepted send is not lost');
        self::assertCount(1, $good->ends, 'the other hooks still run');
        $client->reports->inquiry('KEY001');
        self::assertCount(2, $good->ends);
    }

    public function testThrowingHookIsReportedOncePerClass(): void
    {
        $bad = new class implements Hook {
            public function onRequestStart(RequestEvent $event): void
            {
                throw new \RuntimeException('secret detail');
            }

            public function onRequestEnd(RequestEvent $event): void
            {
            }
        };
        $logger = new MemoryLogger();
        $client = (new FakeTransport())->client(hooks: [$bad], logger: $logger);
        $client->reports->inquiry('KEY001');
        $client->reports->inquiry('KEY001');
        $warnings = array_values(array_filter($logger->lines, static fn (string $l): bool => str_contains($l, 'hook')));
        self::assertCount(1, $warnings);
        self::assertStringNotContainsString('secret detail', $warnings[0]);
    }

    public function testHooksMustImplementTheInterface(): void
    {
        $this->expectException(\Bizgo\Exception\ConfigurationException::class);
        (new \ReflectionClass(\Bizgo\BizgoClient::class))->newInstanceArgs(['apiKey' => FakeTransport::API_KEY, 'hooks' => [new \stdClass()]]);
    }

    public function testOpenTelemetrySpans(): void
    {
        self::assertTrue(OpenTelemetryHook::isAvailable(), 'open-telemetry/api is a dev dependency');
        $tracer = new RecordingTracer();
        $otel = OpenTelemetryHook::create($tracer);
        self::assertNotNull($otel);
        $fake = new FakeTransport();
        $fake->on('sendOmni')->default();
        $fake->on('getReportInquiry')->fail('gateway', 404, 'A404');
        $client = $fake->client(hooks: [$otel], maxRetries: 0);
        $client->send->omni(to: '01000000000', messages: [new SmsMessage(from: '01000000000', text: 'hi')]);
        try {
            $client->reports->inquiry('KEY001');
        } catch (\Bizgo\Exception\NotFoundException) {
        }
        self::assertCount(2, $tracer->spans);
        [$send, $inquiry] = $tracer->spans;
        self::assertSame('bizgo send.omni', $send->name);
        self::assertSame('POST', $send->attributes['http.request.method']);
        self::assertSame('/api/comm/v1/send/omni', $send->attributes['url.template']);
        self::assertSame(200, $send->attributes['http.response.status_code']);
        self::assertSame(0, $send->attributes['bizgo.retry_count']);
        self::assertSame('Ok', $send->status);
        self::assertTrue($send->ended);
        self::assertSame('bizgo reports.inquiry', $inquiry->name);
        self::assertSame('/api/comm/v1/report/inquiry/{msgKey}', $inquiry->attributes['url.template']);
        self::assertSame('A404', $inquiry->attributes['bizgo.code']);
        self::assertSame('gateway', $inquiry->attributes['bizgo.layer']);
        self::assertSame('Error', $inquiry->status);
        self::assertStringNotContainsString('KEY001', serialize($tracer->spans));
        self::assertStringNotContainsString('01000000000', serialize($tracer->spans));
    }
}
