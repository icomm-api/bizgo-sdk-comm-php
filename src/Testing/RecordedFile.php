<?php

declare(strict_types=1);

namespace Bizgo\Testing;

/** A multipart file part as recorded by {@see FakeTransport}: name, type and size only (not the bytes). */
final readonly class RecordedFile
{
    public function __construct(
        public string $filename,
        public string $contentType,
        public int $size,
    ) {
    }
}
