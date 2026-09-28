<?php

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * Holds up to `rate` tokens (one second of burst), refilled continuously at `rate` tokens per second, and starts full.
 *
 * Callers reserve tokens in call order: the balance may go negative, and each caller waits until its own tokens have
 * been refilled. A cost above the capacity waits for a full bucket and leaves the rest as debt.
 *
 * @internal
 */
final class TokenBucket
{
    public readonly float $capacity;
    private float $tokens;
    private float $updated;

    /**
     * @param \Closure(): float $clock monotonic seconds
     */
    public function __construct(
        public readonly float $rate,
        private readonly \Closure $clock,
    ) {
        $this->capacity = $rate;
        $this->tokens = $rate;
        $this->updated = ($this->clock)();
    }

    /** Takes `$cost` tokens and returns how many seconds the caller must wait before sending. */
    public function reserve(float $cost = 1.0): float
    {
        $cost = max($cost, 1.0);
        $now = ($this->clock)();
        $this->tokens = min($this->capacity, $this->tokens + max(0.0, $now - $this->updated) * $this->rate);
        $this->updated = $now;
        $need = min($cost, $this->capacity);
        $wait = max(0.0, ($need - $this->tokens) / $this->rate);
        $this->tokens -= $cost;

        return $wait;
    }

    /** Current balance (negative while in debt), for tests. */
    public function tokens(): float
    {
        return $this->tokens;
    }
}
