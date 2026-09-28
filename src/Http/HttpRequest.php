<?php

declare(strict_types=1);

namespace Bizgo\Http;

/**
 * A prepared request. The body is a complete string, so a retry resends exactly the same bytes.
 *
 * @internal
 */
final readonly class HttpRequest
{
    /**
     * @param non-empty-string $method
     * @param non-empty-string $url
     * @param array<string, string> $headers
     */
    public function __construct(
        public string $method,
        public string $url,
        #[\SensitiveParameter]
        public array $headers,
        #[\SensitiveParameter]
        public ?string $body = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return ['method' => $this->method, 'headers' => '[hidden]', 'body' => '[hidden]'];
    }
}
