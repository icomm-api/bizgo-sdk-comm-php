<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\AppInfo;
use Bizgo\ClientConfig;
use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\DuplicateRequestException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Operations;
use Bizgo\Internal\Path;
use Bizgo\Internal\RateLimiter;
use Bizgo\Model\RequestModel;
use Bizgo\Model\ResponseModel;
use Bizgo\Observability\Hook;
use Bizgo\Observability\RequestEvent;
use Bizgo\Resource\Send;
use Psr\Log\LoggerInterface;

/**
 * Headers, rate limiting, retries, response envelope parsing, error mapping and hooks.
 *
 * Nothing here logs, throws or passes to hooks the request bodies, header values, query strings or real path
 * values: they can contain the API key and phone numbers. The only log line is
 * `METHOD path-template -> status (ms, attempt n)` at debug level.
 *
 * @internal
 *
 * @phpstan-import-type Operation from Operations
 */
final class Transport
{
    private const RETRYABLE_STATUS = [429, 500, 502, 503, 504];
    private const MAX_BACKOFF = 8.0;
    private const MAX_RETRY_AFTER = 60.0;
    /** JSON nesting limit for responses and webhooks (SDK-DESIGN.md §12.8). */
    public const MAX_JSON_DEPTH = 64;
    /** Headers the SDK sets on every request; parameters and HTTP clients cannot replace them. */
    private const RESERVED_HEADERS = ['authorization', 'accept', 'user-agent', 'x-bizgo-client', 'content-type', 'content-length', 'host'];

    private readonly string $userAgent;

    /** @var array<class-string, true> hook classes that already logged a failure */
    private array $warnedHooks = [];

    /**
     * @param \Closure(float): void $sleep waits the given number of seconds
     * @param \Closure(): float $clock monotonic seconds
     * @param list<Hook> $hooks
     */
    public function __construct(
        private readonly ClientConfig $config,
        private readonly HttpSender $sender,
        private readonly LoggerInterface $logger,
        private readonly \Closure $sleep,
        private readonly \Closure $clock,
        private readonly ?RateLimiter $rateLimiter = null,
        private readonly array $hooks = [],
        ?AppInfo $appInfo = null,
    ) {
        $this->userAgent = Identification::userAgent($appInfo);
    }

    /** The `User-Agent` sent with every request. */
    public function userAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * Calls one spec operation and returns the parsed response.
     *
     * @template T of ResponseModel
     *
     * @param class-string<T> $response response model of the operation
     * @param array<string, string> $path path parameter values by name (encoded here, `.`/`..` refused)
     * @param array<string, string|int|null> $query null values are left out
     * @param \JsonSerializable|array<string, mixed>|null $json
     * @param array<string, string> $headers header parameters of the operation (validated by the caller)
     * @param Retry|null $retry overrides `x-sdk-retry` (sends with an idempotencyKey are safe to retry)
     *
     * @return T
     *
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     * @throws ValidationException
     */
    public function call(
        string $operationId,
        string $response,
        array $path = [],
        array $query = [],
        #[\SensitiveParameter]
        \JsonSerializable|array|null $json = null,
        #[\SensitiveParameter]
        ?Multipart $multipart = null,
        #[\SensitiveParameter]
        array $headers = [],
        ?Retry $retry = null,
    ): ResponseModel {
        $op = Operations::get($operationId);
        if ($op['idempotency']) {
            $json = self::withDefaultTtl($json);
        }
        $retry ??= $op['retry'] === 'safe' ? Retry::Safe : Retry::RateLimitOnly;
        $realPath = self::expand($op['path'], $path);
        $cost = $op['rate'] === 'send' ? self::recipients($json) : 1;
        $event = new RequestEvent($operationId, $op['name'], $op['method'], $op['path'], ($this->clock)());
        $this->emit('start', $event);
        try {
            $body = $this->send($op, $realPath, $retry, $query, $json, $multipart, $headers, $cost, $event);
            $model = $this->model($response, $body, $event->status, $op['rate'] === 'send');
            $event->success = true;

            return $model;
        } catch (\Throwable $e) {
            $event->errorType = self::shortName($e);
            if ($e instanceof ApiException) {
                $event->layer = $e->layer;
                $event->code = $e->errorCode;
            }
            throw $e;
        } finally {
            $event->durationMs = (($this->clock)() - $event->startedAt) * 1000;
            $this->emit('end', $event);
        }
    }

    /**
     * Response judgement: non-JSON (4xx/5xx -> ApiException by status, 2xx -> InvalidResponseException), redirects
     * (never followed, never retried), then `common.authCode` (gateway), then `data.code` (service), then HTTP >= 400
     * (gateway).
     *
     * @return array<array-key, mixed>
     *
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public static function parse(#[\SensitiveParameter] HttpResponse $response, bool $send = false): array
    {
        $status = $response->status;
        $body = self::decode($response->body, $status, $send);
        if ($status >= 300 && $status < 400) {
            throw new InvalidResponseException(
                "HTTP {$status} 리다이렉트 응답은 따르지 않습니다. baseUrl(https://mars.ibapi.kr 또는 sandbox)을 확인하세요" . self::hint($send),
                $status,
            );
        }
        if ($body === null) {
            if ($status >= 400) {
                $message = "응답 본문이 JSON이 아닙니다 (HTTP {$status})";
                $retryAfter = self::retryAfter($response);
                throw ErrorMapper::create($status, null, $message, ApiException::LAYER_GATEWAY, null, null, $retryAfter);
            }
            throw new InvalidResponseException("응답 본문이 JSON 객체가 아닙니다 (HTTP {$status})" . self::hint($send), $status);
        }

        $common = is_array($body['common'] ?? null) ? $body['common'] : [];
        $data = is_array($body['data'] ?? null) ? $body['data'] : null;
        $trackingId = self::str($common['infobankTrId'] ?? null);
        $authCode = self::str($common['authCode'] ?? null);
        $serviceCode = $data === null ? null : self::str($data['code'] ?? null);

        if ($authCode !== null && $authCode !== 'A000') {
            throw ErrorMapper::create(
                $status,
                $authCode,
                self::str($common['authResult'] ?? null) ?? '',
                ApiException::LAYER_GATEWAY,
                $trackingId,
                $body,
                self::retryAfter($response),
            );
        }
        if ($serviceCode !== null && $serviceCode !== 'A000') {
            throw ErrorMapper::create(
                $status,
                $serviceCode,
                self::str($data['result'] ?? null) ?? '',
                ApiException::LAYER_SERVICE,
                $trackingId,
                $body,
                self::retryAfter($response),
            );
        }
        if ($status >= 400) {
            $retryAfter = self::retryAfter($response);
            throw ErrorMapper::create($status, null, '요청이 실패했습니다', ApiException::LAYER_GATEWAY, $trackingId, $body, $retryAfter);
        }

        return $body;
    }

    /**
     * Seconds from a numeric `Retry-After` header: a finite number of 0 or more, at most 60. Anything else
     * (negative, `NaN`, an HTTP date, text) is ignored and the default backoff is used.
     */
    public static function retryAfter(HttpResponse $response): ?float
    {
        $value = $response->header('retry-after');
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $value) !== 1) {
            return null;
        }
        $seconds = (float) $value;

        return is_finite($seconds) ? min($seconds, self::MAX_RETRY_AFTER) : null;
    }

    /** `Retry-After` if given, else `min(0.5 * 2^attempt, 8)` seconds with ±25% jitter. */
    public static function backoff(int $attempt, ?float $retryAfter): float
    {
        if ($retryAfter !== null) {
            return $retryAfter;
        }
        $base = min(0.5 * (2 ** $attempt), self::MAX_BACKOFF);

        return $base * (random_int(750, 1250) / 1000);
    }

    /**
     * Decodes a JSON object with the nesting limit; null when the body is not a JSON object.
     *
     * @return array<array-key, mixed>|null
     *
     * @throws InvalidResponseException the body is nested deeper than {@see self::MAX_JSON_DEPTH}
     */
    public static function decode(#[\SensitiveParameter] string $raw, ?int $status = null, bool $send = false): ?array
    {
        try {
            // json_decode counts the outer object as one level; allow MAX_JSON_DEPTH levels of nesting inside it
            $body = json_decode($raw, true, self::MAX_JSON_DEPTH + 1, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (\JsonException $e) {
            if ($e->getCode() === JSON_ERROR_DEPTH) {
                $message = '응답 JSON의 중첩이 너무 깊습니다 (최대 ' . self::MAX_JSON_DEPTH . '단계)';
                throw new InvalidResponseException($message . self::hint($send), $status);
            }

            return null;
        }

        return is_array($body) && ($body === [] || !array_is_list($body)) ? $body : null;
    }

    /**
     * @param Operation $op
     * @param non-empty-string $path
     * @param array<string, string|int|null> $query
     * @param \JsonSerializable|array<string, mixed>|null $json
     * @param array<string, string> $extraHeaders
     *
     * @return array<array-key, mixed>
     *
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     * @throws ValidationException
     */
    private function send(
        array $op,
        string $path,
        Retry $retry,
        array $query,
        #[\SensitiveParameter]
        \JsonSerializable|array|null $json,
        #[\SensitiveParameter]
        ?Multipart $multipart,
        #[\SensitiveParameter]
        array $extraHeaders,
        int $cost,
        RequestEvent $event,
    ): array {
        $headers = [];
        foreach ($extraHeaders as $name => $value) {
            if (in_array(strtolower($name), self::RESERVED_HEADERS, true) || preg_match('/^[A-Za-z0-9-]+$/', $name) !== 1) {
                throw ValidationException::of($name, 'SDK가 정하는 헤더는 바꿀 수 없습니다');
            }
            $headers[$name] = $value;
        }
        // set last, so nothing can replace the key or the identification headers
        $headers['Authorization'] = $this->config->apiKey();
        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = $this->userAgent;
        $headers['X-Bizgo-Client'] = Identification::CLIENT;
        $body = null;
        if ($json !== null) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
            try {
                $body = json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } catch (\JsonException) {
                throw ValidationException::of('', '요청 본문을 JSON으로 만들 수 없습니다 (UTF-8이 아닌 문자열 등)');
            }
        } elseif ($multipart !== null) {
            $headers['Content-Type'] = $multipart->contentType();
            $body = $multipart->body;
        }
        $query = array_filter($query, static fn (string|int|null $v): bool => $v !== null);
        $url = $this->config->baseUrl . $path . ($query === [] ? '' : '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986));
        $request = new HttpRequest($op['method'], $url, $headers, $body);

        $maxRetries = $this->config->maxRetries;
        for ($attempt = 0;; $attempt++) {
            $this->rateLimiter?->wait($op['rate'], $cost); // before every attempt, retries included
            $started = hrtime(true);
            $last = $attempt >= $maxRetries;
            $event->attempts = $attempt + 1;
            try {
                $response = $this->sender->send($request);
            } catch (ConnectionException $e) {
                $event->status = null;
                $this->log($op['method'], $op['path'], self::shortName($e), $started, $attempt);
                if ($last || $retry !== Retry::Safe) {
                    throw $e;
                }
                ($this->sleep)(self::backoff($attempt, null));
                continue;
            }
            $event->status = $response->status;
            $this->log($op['method'], $op['path'], (string) $response->status, $started, $attempt);
            if (!$last && self::shouldRetry($retry, $response->status)) {
                ($this->sleep)(self::backoff($attempt, self::retryAfter($response)));
                continue;
            }
            try {
                return self::parse($response, $op['rate'] === 'send');
            } catch (DuplicateRequestException $e) {
                throw $attempt > 0 ? self::alreadyAccepted($e) : $e;
            }
        }
    }

    /**
     * Builds the response model; any parsing problem becomes an InvalidResponseException with the status,
     * tracking ID and body (SDK-DESIGN.md §12.1).
     *
     * @template T of ResponseModel
     *
     * @param class-string<T> $class
     * @param array<array-key, mixed> $body
     *
     * @return T
     *
     * @throws InvalidResponseException
     */
    private function model(string $class, #[\SensitiveParameter] array $body, ?int $status, bool $send): ResponseModel
    {
        try {
            return $class::fromArray($body);
        } catch (InvalidResponseException $e) {
            $common = is_array($body['common'] ?? null) ? $body['common'] : [];
            $reason = $e->getMessage();
            if ($send) {
                $reason .= self::hint(true);
            }
            throw new InvalidResponseException($reason, $status, self::str($common['infobankTrId'] ?? null), $body);
        }
    }

    /**
     * @param array<string, string> $values
     *
     * @return non-empty-string
     *
     * @throws ValidationException
     */
    private static function expand(string $template, #[\SensitiveParameter] array $values): string
    {
        $path = preg_replace_callback('/\{([A-Za-z0-9_]+)\}/', static function (array $m) use ($values): string {
            $value = $values[$m[1]] ?? throw ValidationException::of($m[1], '경로 파라미터가 없습니다');

            return Path::segment($m[1], $value);
        }, $template);
        if (!is_string($path) || $path === '') {
            throw new \LogicException('invalid path template');
        }

        return $path;
    }

    /**
     * Send bucket cost: the number of `destinations` (minimum 1).
     *
     * @param \JsonSerializable|array<string, mixed>|null $json
     */
    private static function recipients(#[\SensitiveParameter] \JsonSerializable|array|null $json): int
    {
        $data = $json instanceof RequestModel ? $json->toArray() : (is_array($json) ? $json : []);
        $destinations = $data['destinations'] ?? null;

        return is_array($destinations) ? max(count($destinations), 1) : 1;
    }

    /**
     * The body with `idempotencyTtl` = {@see Send::DEFAULT_IDEMPOTENCY_TTL} when it has an `idempotencyKey` but no
     * TTL (Bizgo rejects that with A309, SDK-DESIGN.md §12.20). An explicit TTL, 0 included, is kept, and without a
     * key nothing is added. A model is never changed: the TTL is added to a copy of its JSON form.
     *
     * @param \JsonSerializable|array<string, mixed>|null $json
     *
     * @return \JsonSerializable|array<string, mixed>|null
     */
    private static function withDefaultTtl(#[\SensitiveParameter] \JsonSerializable|array|null $json): \JsonSerializable|array|null
    {
        $data = $json instanceof \JsonSerializable ? $json->jsonSerialize() : $json;
        if (!is_array($data) || ($data['idempotencyKey'] ?? null) === null || ($data['idempotencyTtl'] ?? null) !== null) {
            return $json;
        }
        $body = [];
        foreach ($data as $key => $value) {
            $body[(string) $key] = $value;
        }
        $body['idempotencyTtl'] = Send::DEFAULT_IDEMPOTENCY_TTL;

        return $body;
    }

    private static function shouldRetry(Retry $retry, int $status): bool
    {
        if ($status === 429) {
            return true;
        }

        return $retry === Retry::Safe && in_array($status, self::RETRYABLE_STATUS, true);
    }

    private static function alreadyAccepted(DuplicateRequestException $e): DuplicateRequestException
    {
        return new DuplicateRequestException(
            '이전 시도가 이미 접수된 것으로 보입니다(같은 idempotencyKey, 자동 재시도 중 A301). 메시지는 다시 발송되지 않았으며, '
            . '접수 결과는 상태 조회 API로 확인하세요.',
            $e->httpStatus,
            $e->errorCode,
            $e->layer,
            $e->trackingId,
            $e->body,
            true,
        );
    }

    /** @param 'start'|'end' $phase */
    private function emit(string $phase, RequestEvent $event): void
    {
        foreach ($this->hooks as $hook) {
            try {
                $phase === 'start' ? $hook->onRequestStart($event) : $hook->onRequestEnd($event);
            } catch (\Throwable) {
                // observability must never change a result, a retry or a bulk send (SDK-DESIGN.md §12.15)
                if (!isset($this->warnedHooks[$hook::class])) {
                    $this->warnedHooks[$hook::class] = true;
                    $this->safeLog('warning', 'bizgo hook ' . self::shortName($hook) . ' threw; ignored (reported once)');
                }
            }
        }
    }

    private function log(string $method, string $template, string $outcome, int|float $started, int $attempt): void
    {
        // path template only: real values and query strings can carry phone numbers
        $elapsed = (hrtime(true) - $started) / 1e6;
        $this->safeLog('debug', sprintf('%s %s -> %s (%.0f ms, attempt %d)', $method, $template, $outcome, $elapsed, $attempt + 1));
    }

    /** @param 'debug'|'warning' $level */
    private function safeLog(string $level, string $message): void
    {
        try {
            $this->logger->log($level, $message);
        } catch (\Throwable) {
            // a broken logger must not break the request (SDK-DESIGN.md §12.15)
        }
    }

    /** The note for an unusable response to a send (SDK-DESIGN.md §12.1): it may still have been accepted. */
    private static function hint(bool $send): string
    {
        return $send ? ' — 요청은 접수됐을 수 있으니 다시 보내기 전에 상태 조회로 확인하세요' : '';
    }

    private static function str(mixed $value): ?string
    {
        return is_string($value) ? $value : (is_int($value) || is_float($value) ? (string) $value : null);
    }

    private static function shortName(object $object): string
    {
        $class = $object::class;
        $pos = strrpos($class, '\\');

        return $pos === false ? $class : substr($class, $pos + 1);
    }
}
