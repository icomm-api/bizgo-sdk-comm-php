<?php

declare(strict_types=1);

namespace Bizgo\Result;

use Bizgo\Internal\Mask;
use Bizgo\Model\MessageStatus;

/** One page of send history. Pass {@see self::$lastSeq} as `lastSeq` to get the next page. */
final readonly class MessagePage
{
    /**
     * @param list<MessageStatus> $messages
     */
    public function __construct(
        public array $messages,
        public ?int $lastSeq,
        public bool $hasNext,
    ) {
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
        return Mask::describe('MessagePage', get_object_vars($this));
    }
}
