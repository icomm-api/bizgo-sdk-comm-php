<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * Implemented by every exception this SDK throws. Catch it to handle all SDK errors.
 *
 * ```
 * BizgoException
 * ├── ConfigurationException          API Key가 없음, base URL이 https가 아님 등
 * ├── ValidationException             보내기 전 요청 검증 실패 (필드 경로와 이유만 담음)
 * ├── ConnectionException             응답을 받기 전 네트워크 오류 (발송은 접수됐을 수도 있음)
 * │   └── TimeoutException
 * ├── ApiException                    비즈고가 실패 코드를 응답함
 * │   ├── BadRequestException         400
 * │   ├── AuthenticationException     401 / gateway A401, service A001·A002·A100
 * │   ├── PermissionDeniedException   403 / gateway A403, service A110·A111
 * │   ├── NotFoundException           404
 * │   ├── DuplicateRequestException   service A301 (같은 idempotencyKey가 이미 접수됨)
 * │   ├── RateLimitException          429 / A020
 * │   └── InternalServerException     5xx
 * ├── InvalidResponseException        응답이 문서의 {common, data} 형식이 아님
 * └── WebhookVerificationException    웹훅 서명·timestamp·본문 검증 실패
 * ```
 *
 * Exception messages never contain the API key, request bodies, query strings or phone numbers.
 */
interface BizgoException extends \Throwable
{
}
