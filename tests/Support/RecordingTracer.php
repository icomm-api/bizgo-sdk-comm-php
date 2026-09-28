<?php

declare(strict_types=1);

namespace Bizgo\Tests\Support;

use OpenTelemetry\API\Trace\SpanBuilderInterface;
use OpenTelemetry\API\Trace\SpanContextInterface;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\ContextInterface;

/** An in-memory OpenTelemetry tracer for the adapter test. */
final class RecordingTracer implements TracerInterface
{
    /** @var list<RecordingSpan> */
    public array $spans = [];

    public function spanBuilder(string $spanName): SpanBuilderInterface
    {
        $span = new RecordingSpan($spanName);
        $this->spans[] = $span;

        return new class ($span) implements SpanBuilderInterface {
            public function __construct(private readonly RecordingSpan $span)
            {
            }

            public function setParent(ContextInterface|false|null $context): SpanBuilderInterface
            {
                return $this;
            }

            /** @param iterable<mixed, mixed> $attributes */
            public function addLink(SpanContextInterface $context, iterable $attributes = []): SpanBuilderInterface
            {
                return $this;
            }

            public function setAttribute(string $key, mixed $value): SpanBuilderInterface
            {
                $this->span->attributes[$key] = $value;

                return $this;
            }

            /** @param iterable<mixed, mixed> $attributes */
            public function setAttributes(iterable $attributes): SpanBuilderInterface
            {
                foreach ($attributes as $key => $value) {
                    $this->span->attributes[is_string($key) ? $key : json_encode($key, JSON_THROW_ON_ERROR)] = $value;
                }

                return $this;
            }

            public function setStartTimestamp(int $timestampNanos): SpanBuilderInterface
            {
                return $this;
            }

            public function setSpanKind(int $spanKind): SpanBuilderInterface
            {
                $this->span->kind = $spanKind;

                return $this;
            }

            public function startSpan(): SpanInterface
            {
                return $this->span;
            }
        };
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
