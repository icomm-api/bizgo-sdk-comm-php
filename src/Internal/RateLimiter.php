<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\RateLimit;

/**
 * The two token buckets of one client (see {@see RateLimit}).
 *
 * @internal
 */
final class RateLimiter
{
    public readonly TokenBucket $send;
    public readonly TokenBucket $other;

    /**
     * @param \Closure(): float $clock monotonic seconds
     * @param \Closure(float): void $sleep
     */
    public function __construct(
        RateLimit $limits,
        \Closure $clock,
        private readonly \Closure $sleep,
    ) {
        $this->send = new TokenBucket($limits->send, $clock);
        $this->other = new TokenBucket($limits->other, $clock);
    }

    /** Waits until `$cost` tokens of the bucket (`send` or `other`) are available. */
    public function wait(string $bucket, int $cost = 1): void
    {
        $delay = ($bucket === 'send' ? $this->send : $this->other)->reserve($cost);
        if ($delay > 0) {
            ($this->sleep)($delay);
        }
    }
}
