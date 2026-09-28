<?php

declare(strict_types=1);

namespace Bizgo\Testing;

/**
 * One request the client made to a {@see FakeTransport}. Header **values** (including the API key) are never recorded.
 */
final readonly class RecordedRequest
{
    /**
     * @param array<string, string> $query decoded query string
     * @param array<string, string|RecordedFile|list<string|RecordedFile>>|null $form multipart fields: text and JSON parts
     *        as text, files as {@see RecordedFile}
     * @param list<string> $headerNames names of the request headers (lower case)
     */
    public function __construct(
        /** Matched `operationId`, or null for a path that is not in the spec. */
        public ?string $operationId,
        /** `<resource>.<method>` of the matched operation. */
        public ?string $operation,
        public string $method,
        /** Real path, with the encoded path values. */
        public string $path,
        /** Path template of the matched operation. */
        public ?string $pathTemplate,
        public array $query,
        /** Decoded JSON body, if any. */
        public mixed $json,
        public ?array $form,
        public array $headerNames,
    ) {
    }

    public function __toString(): string
    {
        return 'RecordedRequest(' . $this->method . ' ' . ($this->pathTemplate ?? $this->path) . ')';
    }
}
