<?php

declare(strict_types=1);

namespace Bizgo\Tests\Support;

use Psr\Log\AbstractLogger;

/** Collects log lines (message and context) as text. */
final class MemoryLogger extends AbstractLogger
{
    /** @var list<string> */
    public array $lines = [];

    /**
     * @param mixed $level
     * @param array<array-key, mixed> $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->lines[] = (is_string($level) ? $level : 'unknown') . ' ' . $message . ($context === [] ? '' : ' ' . json_encode($context));
    }

    public function text(): string
    {
        return implode("\n", $this->lines);
    }
}
