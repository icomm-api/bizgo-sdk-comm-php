<?php

declare(strict_types=1);

namespace Bizgo\Http;

/**
 * A buffered response.
 *
 * @internal
 */
final readonly class HttpResponse
{
    /**
     * @param array<string, string> $headers lower-case header name => value
     */
    public function __construct(
        public int $status,
        public array $headers,
        #[\SensitiveParameter]
        public string $body,
    ) {
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return ['status' => $this->status, 'body' => '[hidden]', 'size' => strlen($this->body)];
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
}
