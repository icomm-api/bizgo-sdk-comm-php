<?php

declare(strict_types=1);

namespace Bizgo\Result;

/**
 * One chunk of `$client->send->bulk()` that failed as a whole. Only indexes are kept, never the recipients:
 * `start`/`end` are positions in the `to` list you passed.
 */
final readonly class BulkChunkError
{
    public function __construct(
        /** Chunk number, from 0. */
        public int $chunkIndex,
        /** Index of the first recipient of the chunk in your `to` list. */
        public int $start,
        /** Index after the last recipient of the chunk (exclusive). */
        public int $end,
        /** What failed: ApiException, ConnectionException, InvalidResponseException, ... */
        public \Throwable $error,
    ) {
    }

    public function __toString(): string
    {
        $parts = explode('\\', $this->error::class);

        return sprintf(
            'chunk %d (recipients %d-%d): %s: %s',
            $this->chunkIndex,
            $this->start,
            $this->end - 1,
            $parts[count($parts) - 1],
            $this->error->getMessage(),
        );
    }
}
