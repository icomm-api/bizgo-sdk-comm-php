<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * A request with the same `idempotencyKey` was already accepted (service code A301). The message was not sent again.
 *
 * {@see self::$alreadyAccepted} is true when this answered an automatic retry: an earlier attempt of the same call
 * (whose response was lost to a timeout, a 5xx or a 429) was accepted, so the message went out once.
 */
final class DuplicateRequestException extends ApiException
{
    /**
     * @param 'gateway'|'service' $layer
     * @param array<array-key, mixed>|null $body
     */
    public function __construct(
        string $serverMessage,
        int $httpStatus,
        ?string $errorCode,
        string $layer,
        ?string $trackingId = null,
        ?array $body = null,
        /** True when an earlier attempt of this call was already accepted (A301 after an automatic retry). */
        public readonly bool $alreadyAccepted = false,
    ) {
        parent::__construct($serverMessage, $httpStatus, $errorCode, $layer, $trackingId, $body);
    }

    /** @return array<string, mixed> */
    public function __serialize(): array
    {
        return parent::__serialize() + ['alreadyAccepted' => $this->alreadyAccepted];
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $this->alreadyAccepted = ($data['alreadyAccepted'] ?? false) === true;
    }
}
