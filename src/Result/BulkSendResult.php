<?php

declare(strict_types=1);

namespace Bizgo\Result;

use Bizgo\Internal\Mask;
use Bizgo\Model\SendDestinationResult;

/**
 * Result of `$client->send->bulk()`. Check {@see self::$errors} (chunks that failed as a whole) and
 * {@see self::failed()} (recipients rejected at acceptance). Recipients already accepted by an earlier run with the
 * same idempotency keys are in {@see self::duplicates()}.
 */
final readonly class BulkSendResult
{
    /**
     * @param array<int, SendResult|null> $results per chunk, in order: the SendResult, or null if the chunk failed
     * @param list<BulkChunkError> $errors failed chunks, with their recipient index range and exception
     */
    public function __construct(
        public array $results,
        public array $errors = [],
    ) {
    }

    /**
     * Recipients accepted with `A000`, across all chunks.
     *
     * @return list<SendDestinationResult>
     */
    public function succeeded(): array
    {
        $out = [];
        foreach ($this->results as $result) {
            array_push($out, ...($result?->succeeded() ?? []));
        }

        return $out;
    }

    /**
     * Recipients already accepted earlier with the same chunk idempotency key (per-recipient `A301`), across all
     * chunks. A re-run chunk whose recipients are all duplicates is answered normally, not recorded in
     * {@see self::$errors}.
     *
     * @return list<SendDestinationResult>
     */
    public function duplicates(): array
    {
        $out = [];
        foreach ($this->results as $result) {
            array_push($out, ...($result?->duplicates() ?? []));
        }

        return $out;
    }

    /**
     * Recipients rejected at acceptance in the chunks that were answered (failed chunks are in {@see self::$errors}).
     *
     * @return list<SendDestinationResult>
     */
    public function failed(): array
    {
        $out = [];
        foreach ($this->results as $result) {
            array_push($out, ...($result?->failed() ?? []));
        }

        return $out;
    }

    /**
     * Message keys of every accepted recipient.
     *
     * @return list<string>
     */
    public function msgKeys(): array
    {
        $out = [];
        foreach ($this->results as $result) {
            array_push($out, ...($result?->msgKeys() ?? []));
        }

        return $out;
    }

    /** True when every chunk was answered and no recipient was rejected (duplicates are not rejections). */
    public function ok(): bool
    {
        return $this->errors === [] && $this->failed() === [];
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return Mask::fields(get_object_vars($this));
    }

    public function __toString(): string
    {
        return sprintf(
            'BulkSendResult{chunks=%d, failedChunks=%d, succeeded=%d, duplicates=%d, failed=%d}',
            count($this->results),
            count($this->errors),
            count($this->succeeded()),
            count($this->duplicates()),
            count($this->failed()),
        );
    }
}
