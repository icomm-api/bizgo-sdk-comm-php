<?php

declare(strict_types=1);

namespace Bizgo\Tests\Support;

use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanContext;
use OpenTelemetry\API\Trace\SpanContextInterface;
use OpenTelemetry\API\Trace\SpanInterface;

/** A recorded span. */
final class RecordingSpan extends Span
{
    /** @var array<string, mixed> */
    public array $attributes = [];
    public ?int $kind = null;
    public string $status = 'Unset';
    public bool $ended = false;

    public function __construct(public string $name)
    {
    }

    public function getContext(): SpanContextInterface
    {
        return SpanContext::getInvalid();
    }

    public function isRecording(): bool
    {
        return !$this->ended;
    }

    /** @param bool|int|float|string|array<array-key, mixed>|null $value */
    public function setAttribute(string $key, bool|int|float|string|array|null $value): SpanInterface
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /** @param iterable<mixed, mixed> $attributes */
    public function setAttributes(iterable $attributes): SpanInterface
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[is_string($key) ? $key : json_encode($key, JSON_THROW_ON_ERROR)] = $value;
        }

        return $this;
    }

    /** @param iterable<mixed, mixed> $attributes */
    public function addLink(SpanContextInterface $context, iterable $attributes = []): SpanInterface
    {
        return $this;
    }

    /** @param iterable<mixed, mixed> $attributes */
    public function addEvent(string $name, iterable $attributes = [], ?int $timestamp = null): SpanInterface
    {
        return $this;
    }

    /** @param iterable<mixed, mixed> $attributes */
    public function recordException(\Throwable $exception, iterable $attributes = []): SpanInterface
    {
        return $this;
    }

    public function updateName(string $name): SpanInterface
    {
        $this->name = $name;

        return $this;
    }

    public function setStatus(string $code, ?string $description = null): SpanInterface
    {
        $this->status = $code;

        return $this;
    }

    public function end(?int $endEpochNanos = null): void
    {
        $this->ended = true;
    }
}
