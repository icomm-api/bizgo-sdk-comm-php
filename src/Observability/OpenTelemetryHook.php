<?php

declare(strict_types=1);

namespace Bizgo\Observability;

use Bizgo\Version;
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\API\Trace\TracerInterface;

/**
 * OpenTelemetry tracing on top of {@see Hook}: one client span per API call (SDK-DESIGN.md §11.4).
 *
 * Optional: it needs `open-telemetry/api` (`composer require open-telemetry/api`), which this SDK only suggests.
 * Without it {@see self::create()} returns null and nothing else of this class is loaded.
 *
 * ```php
 * $otel = OpenTelemetryHook::create();              // tracer from Globals::tracerProvider()
 * $client = new BizgoClient(hooks: $otel === null ? [] : [$otel]);
 * ```
 *
 * Span name `bizgo <resource>.<method>`; attributes `http.request.method`, `url.template` (the path template, never
 * real values), `http.response.status_code`, `bizgo.operation_id`, `bizgo.code`, `bizgo.layer`, `bizgo.retry_count`.
 * No body, query string, phone number or key is recorded.
 */
final class OpenTelemetryHook implements Hook
{
    /** @var \WeakMap<RequestEvent, SpanInterface> open spans; an abandoned event does not leak its span */
    private \WeakMap $spans;

    public function __construct(private readonly TracerInterface $tracer)
    {
        $this->spans = new \WeakMap();
    }

    /** True when `open-telemetry/api` is installed. */
    public static function isAvailable(): bool
    {
        return interface_exists(TracerInterface::class) && class_exists(Globals::class);
    }

    /**
     * A hook using `$tracer`, or the global tracer provider's `bizgo` tracer. Null when `open-telemetry/api` is not
     * installed.
     */
    public static function create(?TracerInterface $tracer = null): ?self
    {
        if ($tracer === null && !self::isAvailable()) {
            return null;
        }

        return new self($tracer ?? Globals::tracerProvider()->getTracer('bizgo', Version::SDK));
    }

    public function onRequestStart(RequestEvent $event): void
    {
        $span = $this->tracer->spanBuilder('bizgo ' . $event->operation)
            ->setSpanKind(SpanKind::KIND_CLIENT)
            ->setAttribute('http.request.method', $event->method)
            ->setAttribute('url.template', $event->pathTemplate)
            ->setAttribute('bizgo.operation_id', $event->operationId)
            ->startSpan();
        $this->spans[$event] = $span;
    }

    public function onRequestEnd(RequestEvent $event): void
    {
        $span = $this->spans[$event] ?? null;
        if ($span === null) {
            return;
        }
        unset($this->spans[$event]);
        $span->setAttribute('http.response.status_code', $event->status);
        $span->setAttribute('bizgo.retry_count', max($event->attempts - 1, 0));
        if ($event->code !== null) {
            $span->setAttribute('bizgo.code', $event->code);
        }
        if ($event->layer !== null) {
            $span->setAttribute('bizgo.layer', $event->layer);
        }
        if ($event->success) {
            $span->setStatus(StatusCode::STATUS_OK);
        } else {
            $span->setAttribute('error.type', $event->errorType ?? 'error');
            $span->setStatus(StatusCode::STATUS_ERROR, $event->errorType);
        }
        $span->end();
    }
}
