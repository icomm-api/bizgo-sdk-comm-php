<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\AuthenticationException;
use Bizgo\Exception\BadRequestException;
use Bizgo\Exception\DuplicateRequestException;
use Bizgo\Exception\InternalServerException;
use Bizgo\Exception\NotFoundException;
use Bizgo\Exception\PermissionDeniedException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\ServiceCodes;

/**
 * Picks the exception class for a failure.
 *
 * Gateway (`common.authCode`) and service (`data.code`) codes are looked up in separate tables because the
 * same code means different things: service `A401` is an invalid `paymentCode` field, not an authentication
 * failure. Codes not in the tables fall back to the HTTP status; for a service failure inside an HTTP 200 the
 * status documented for the code in the error code table is used (A306 -> 400 -> BadRequestException).
 *
 * @internal
 */
final class ErrorMapper
{
    /** @var array<string, class-string<ApiException>> */
    private const GATEWAY = [
        'A400' => BadRequestException::class,
        'A401' => AuthenticationException::class,
        'A403' => PermissionDeniedException::class,
        'A404' => NotFoundException::class,
    ];

    /** @var array<string, class-string<ApiException>> */
    private const SERVICE = [
        'A001' => AuthenticationException::class,
        'A002' => AuthenticationException::class,
        'A100' => AuthenticationException::class,
        'A110' => PermissionDeniedException::class,
        'A111' => PermissionDeniedException::class,
        'A020' => RateLimitException::class,
        'A301' => DuplicateRequestException::class,
    ];

    /** @var array<int, class-string<ApiException>> */
    private const STATUS = [
        400 => BadRequestException::class,
        401 => AuthenticationException::class,
        403 => PermissionDeniedException::class,
        404 => NotFoundException::class,
        429 => RateLimitException::class,
    ];

    private function __construct()
    {
    }

    /**
     * @param 'gateway'|'service' $layer
     *
     * @return class-string<ApiException>
     */
    public static function classFor(int $httpStatus, ?string $code, string $layer): string
    {
        $table = $layer === ApiException::LAYER_GATEWAY ? self::GATEWAY : self::SERVICE;
        if ($code !== null && isset($table[$code])) {
            return $table[$code];
        }
        if ($layer === ApiException::LAYER_SERVICE && $httpStatus < 400 && $code !== null) {
            $httpStatus = ServiceCodes::httpStatus($code) ?? $httpStatus;
        }
        if (isset(self::STATUS[$httpStatus])) {
            return self::STATUS[$httpStatus];
        }
        if ($httpStatus >= 500) {
            return InternalServerException::class;
        }
        if ($httpStatus >= 400) {
            return BadRequestException::class;
        }

        return ApiException::class;
    }

    /**
     * @param 'gateway'|'service' $layer
     * @param array<array-key, mixed>|null $body
     */
    public static function create(
        int $httpStatus,
        ?string $code,
        string $message,
        string $layer,
        ?string $trackingId,
        #[\SensitiveParameter]
        ?array $body,
        ?float $retryAfter,
    ): ApiException {
        $class = self::classFor($httpStatus, $code, $layer);
        if ($class === RateLimitException::class) {
            return new RateLimitException($message, $httpStatus, $code, $layer, $trackingId, $body, $retryAfter);
        }

        return new $class($message, $httpStatus, $code, $layer, $trackingId, $body);
    }
}
