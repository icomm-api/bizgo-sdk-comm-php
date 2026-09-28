<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * Bizgo answered with a failure.
 *
 * The response is judged in this order: `common.authCode` (gateway layer: authentication, permissions,
 * request format), then `data.code` (service layer: the product API). The same code can mean different
 * things in each layer (service `A401` is an invalid `paymentCode`, gateway `A401` is a bad key),
 * so check {@see self::$layer} together with {@see self::$errorCode}.
 *
 * The Bizgo code is `$errorCode` because `\Exception::$code` is reserved; `getCode()` returns the HTTP status.
 *
 * `getMessage()` looks like `HTTP 400 | service code=A306 | <result> | <description> | infobankTrId=...`.
 */
class ApiException extends \RuntimeException implements BizgoException
{
    public const LAYER_GATEWAY = 'gateway';
    public const LAYER_SERVICE = 'service';

    /** Korean description of a service-layer {@see self::$errorCode} from the error code table, if known. */
    public readonly ?string $description;

    /**
     * @param string $serverMessage `authResult` / `result` text from the server (or an SDK explanation)
     * @param int $httpStatus HTTP status code
     * @param string|null $errorCode `common.authCode` (gateway) or `data.code` (service), for example `A401`
     * @param 'gateway'|'service' $layer which part of the response failed
     * @param string|null $trackingId `common.infobankTrId`; give it to Bizgo support when asking about a request
     * @param array<array-key, mixed>|null $body the parsed response body; it can contain phone numbers, do not log it as is
     */
    public function __construct(
        public readonly string $serverMessage,
        public readonly int $httpStatus,
        public readonly ?string $errorCode,
        public readonly string $layer,
        public readonly ?string $trackingId = null,
        public readonly ?array $body = null,
    ) {
        $known = $layer === self::LAYER_SERVICE && $errorCode !== null ? ServiceCodes::describe($errorCode) : null;
        $this->description = $known;
        $parts = ['HTTP ' . $httpStatus, $layer . ' code=' . ($errorCode ?? 'none')];
        if ($serverMessage !== '') {
            $parts[] = $serverMessage;
        }
        if ($known !== null && $known !== $serverMessage) {
            $parts[] = $known;
        }
        if ($trackingId !== null && $trackingId !== '') {
            $parts[] = 'infobankTrId=' . $trackingId;
        }
        parent::__construct(implode(' | ', $parts), $httpStatus);
    }

    /**
     * Keeps the response body (it can contain phone numbers) out of var_dump()/print_r() output.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'message' => $this->getMessage(),
            'httpStatus' => $this->httpStatus,
            'errorCode' => $this->errorCode,
            'layer' => $this->layer,
            'trackingId' => $this->trackingId,
        ];
    }

    /**
     * Serializable with serialize()/unserialize() for job queues and worker processes. The stack trace is left out
     * (its arguments may be closures or hold request data).
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'message' => $this->getMessage(),
            'serverMessage' => $this->serverMessage,
            'httpStatus' => $this->httpStatus,
            'errorCode' => $this->errorCode,
            'layer' => $this->layer,
            'trackingId' => $this->trackingId,
            'body' => $this->body,
            'description' => $this->description,
        ];
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        $string = static fn (string $key): ?string => is_string($data[$key] ?? null) ? $data[$key] : null;
        $this->message = $string('message') ?? '';
        $this->serverMessage = $string('serverMessage') ?? '';
        $this->httpStatus = is_int($data['httpStatus'] ?? null) ? $data['httpStatus'] : 0;
        $this->code = $this->httpStatus;
        $this->errorCode = $string('errorCode');
        $this->layer = $string('layer') === self::LAYER_SERVICE ? self::LAYER_SERVICE : self::LAYER_GATEWAY;
        $this->trackingId = $string('trackingId');
        $this->body = is_array($data['body'] ?? null) ? $data['body'] : null;
        $this->description = $string('description');
    }
}
