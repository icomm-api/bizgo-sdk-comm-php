<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * The response could not be used: not the documented `{common, data}` envelope, a field of an incompatible type, a
 * redirect (never followed), too large, or too deeply nested.
 *
 * For a send operation the request may still have been accepted: check with the status APIs before sending again.
 */
final class InvalidResponseException extends \UnexpectedValueException implements BizgoException
{
    /**
     * @param array<array-key, mixed>|null $body the parsed body, if it was JSON; it can contain phone numbers
     */
    public function __construct(
        string $message,
        /** HTTP status of the response, if one arrived. */
        public readonly ?int $httpStatus = null,
        /** `common.infobankTrId`, if the body had one. */
        public readonly ?string $trackingId = null,
        public readonly ?array $body = null,
    ) {
        parent::__construct($message);
    }

    /**
     * Keeps the body (it can contain phone numbers) out of var_dump()/print_r().
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['message' => $this->getMessage(), 'httpStatus' => $this->httpStatus, 'trackingId' => $this->trackingId];
    }

    /**
     * Serializable for job queues (without the stack trace, whose arguments may not be serializable).
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'message' => $this->getMessage(),
            'httpStatus' => $this->httpStatus,
            'trackingId' => $this->trackingId,
            'body' => $this->body,
        ];
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        $this->message = is_string($data['message'] ?? null) ? $data['message'] : '';
        $this->httpStatus = is_int($data['httpStatus'] ?? null) ? $data['httpStatus'] : null;
        $this->trackingId = is_string($data['trackingId'] ?? null) ? $data['trackingId'] : null;
        $this->body = is_array($data['body'] ?? null) ? $data['body'] : null;
    }
}
