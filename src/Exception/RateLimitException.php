<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * Too many requests (HTTP 429 / A020). Default limits: 200 TPS for send APIs, 5 TPS for the others.
 * Thrown after the automatic retries are used up.
 */
final class RateLimitException extends ApiException
{
    /**
     * @param 'gateway'|'service' $layer
     * @param array<array-key, mixed>|null $body
     * @param float|null $retryAfter seconds from the `Retry-After` header, if the server sent one
     */
    public function __construct(
        string $serverMessage,
        int $httpStatus,
        ?string $errorCode,
        string $layer,
        ?string $trackingId = null,
        ?array $body = null,
        public readonly ?float $retryAfter = null,
    ) {
        parent::__construct($serverMessage, $httpStatus, $errorCode, $layer, $trackingId, $body);
    }

    /** @return array<string, mixed> */
    public function __serialize(): array
    {
        return parent::__serialize() + ['retryAfter' => $this->retryAfter];
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $value = $data['retryAfter'] ?? null;
        $this->retryAfter = is_float($value) || is_int($value) ? (float) $value : null;
    }
}
