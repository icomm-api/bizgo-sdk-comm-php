<?php

declare(strict_types=1);

namespace Bizgo\Result;

use Bizgo\Internal\Mask;
use Bizgo\Model\Report;

/**
 * One batch from report polling. Call `$client->reports->ack($batch->reportId)` after you stored it,
 * or use `$client->reports->consume()`, which does that for you.
 */
final readonly class ReportBatch
{
    /**
     * @param string $reportId ID for the receipt confirmation; empty when there are no reports
     * @param list<Report> $reports
     */
    public function __construct(
        public string $reportId,
        public array $reports = [],
    ) {
    }

    /** True when there is nothing new. */
    public function isEmpty(): bool
    {
        return $this->reports === [];
    }

    /**
     * For var_dump()/print_r(): phone numbers masked and message content hidden (the properties keep the real values).
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return Mask::fields(get_object_vars($this));
    }

    /** A log-safe description. */
    public function __toString(): string
    {
        return Mask::describe('ReportBatch', get_object_vars($this));
    }
}
