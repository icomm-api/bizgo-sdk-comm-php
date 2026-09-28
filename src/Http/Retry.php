<?php

declare(strict_types=1);

namespace Bizgo\Http;

/**
 * Which failures may be retried for a request.
 *
 * @internal
 */
enum Retry
{
    /** Read-only or idempotent calls (lookups, ack, sends with an idempotencyKey): 429, 5xx and network errors. */
    case Safe;

    /**
     * Calls that could create a duplicate (sends without an idempotencyKey, uploads): only 429, which the gateway
     * returns before the request is processed.
     */
    case RateLimitOnly;
}
