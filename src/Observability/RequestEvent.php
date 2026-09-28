<?php

declare(strict_types=1);

namespace Bizgo\Observability;

/**
 * What a {@see Hook} may see about one API call (all its retries together).
 *
 * It carries the operation, the HTTP method, the **path template** (`/api/comm/v1/report/inquiry/{msgKey}`), the
 * status, the error layer/code/class, the number of attempts and the duration. It never carries request or response
 * bodies, query strings, header values, real path values, phone numbers or the API key.
 *
 * The same object is passed to {@see Hook::onRequestStart()} and {@see Hook::onRequestEnd()}; the result fields
 * are filled in at the end.
 */
final class RequestEvent
{
    /** HTTP status of the last attempt; null after a network error or before the end. */
    public ?int $status = null;

    /** `gateway` or `service` when the call failed with an API error code. */
    public ?string $layer = null;

    /** API error code (`A020`, `A401`, ...) when the call failed with one. */
    public ?string $code = null;

    /** Short exception class name (`RateLimitException`, `TimeoutException`, ...) when the call failed. */
    public ?string $errorType = null;

    /** True when the call returned a parsed success response. A response that could not be parsed is a failure. */
    public bool $success = false;

    /** HTTP attempts made (1 = no retry). */
    public int $attempts = 0;

    /** Milliseconds from the start of the call (including rate limit waits and retries) to the end. */
    public ?float $durationMs = null;

    public function __construct(
        /** `operationId` from the spec, for example `sendOmni`. */
        public readonly string $operationId,
        /** `<resource>.<method>`, for example `send.omni` or `alimtalk.templates.list`. */
        public readonly string $operation,
        /** HTTP method. */
        public readonly string $method,
        /** Path template. Never the real path values. */
        public readonly string $pathTemplate,
        /** Monotonic start time in seconds (internal). */
        public readonly float $startedAt = 0.0,
    ) {
    }
}
