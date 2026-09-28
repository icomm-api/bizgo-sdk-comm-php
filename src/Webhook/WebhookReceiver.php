<?php

declare(strict_types=1);

namespace Bizgo\Webhook;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\WebhookPayloadException;
use Bizgo\Exception\WebhookVerificationException;
use Bizgo\Internal\Secret;
use Bizgo\Model\MoWebhookPayload;
use Bizgo\Model\ReportWebhookPayload;
use Bizgo\Model\ResponseModel;
use Psr\Http\Message\MessageInterface;

/**
 * Verifies and parses Bizgo webhooks: delivery reports, MO messages and the counsel (상담톡) events.
 *
 * ```php
 * $receiver = new WebhookReceiver((string) getenv('BIZGO_WEBHOOK_SECRET'));
 * try {
 *     $report = $receiver->report(getallheaders(), file_get_contents('php://input'));
 * } catch (WebhookPayloadException) {
 *     http_response_code(400);
 *     exit;
 * } catch (WebhookVerificationException) {
 *     http_response_code(401);
 *     exit;
 * }
 * store($report);                                        // idempotent: the same report can arrive again
 * echo json_encode(WebhookReceiver::ack($report->msgKey)); // within 5 seconds
 * ```
 *
 * Security notes:
 * - The signature is `X-IB-Signature = HmacSHA256(secret, X-IB-Timestamp)`; it is verified together with the
 *   timestamp window. As general practice, also restrict the endpoint to the Bizgo webhook source IPs, serve it
 *   over HTTPS and confirm important results with the inquiry APIs.
 * - Report and MO webhooks always need a valid signature.
 * - Counsel (상담톡) webhooks are not signed (signatures apply only to report and MO webhooks). The counsel methods do
 *   not require or check a signature and ignore signature headers if present; they apply the same body checks (size,
 *   JSON depth, field types). The static `parseCounsel*()` methods need no webhook secret, for counsel-only endpoints.
 * - Bizgo retries a webhook up to 3 times when it does not get the expected answer: `{"msgKey": ...}` for reports and
 *   MO ({@see self::ack()}), `{"code": "A000", "result": "Success"}` for counsel events ({@see self::counselAck()}).
 *   Deduplicate by `msgKey`.
 * - The signature output encoding (hex or base64) is not confirmed yet; both are accepted (hex in any case).
 * - The webhook secret is obtained by requesting it from Bizgo (it is delivered separately).
 */
final class WebhookReceiver
{
    use WebhookParsers;

    public const TIMESTAMP_HEADER = 'X-IB-Timestamp';
    public const SIGNATURE_HEADER = 'X-IB-Signature';
    public const DEFAULT_TOLERANCE_SECONDS = 300.0;
    public const MAX_BODY_BYTES = 1024 * 1024;
    /** JSON nesting limit of a webhook body (SDK-DESIGN.md §12.8). */
    public const MAX_JSON_DEPTH = 64;

    /** The secret, in a holder that no dump or export shows. */
    private readonly Secret $secret;

    /**
     * @param string $secret webhook secret (request it from Bizgo); read it from an environment variable or vault
     * @param float|null $tolerance maximum age of `X-IB-Timestamp` in seconds (a finite number above 0), to limit replay;
     *        null disables the check (not recommended)
     *
     * @throws WebhookVerificationException the secret is empty or blank, or the tolerance is not valid
     */
    public function __construct(
        #[\SensitiveParameter]
        string $secret,
        private readonly ?float $tolerance = self::DEFAULT_TOLERANCE_SECONDS,
    ) {
        self::checkSecret($secret);
        self::checkTolerance($tolerance);
        $this->secret = new Secret($secret);
    }

    /**
     * Verifies `X-IB-Signature`, or throws.
     *
     * The expected value is `HMAC-SHA256(key = secret, message = timestamp)`, compared in constant time, as lower-
     * or upper-case hex or base64. The timestamp must be 1-16 ASCII digits; 13 or
     * more digits are epoch milliseconds (the documented example), fewer are seconds.
     *
     * @param float|null $now current UNIX time, for tests
     *
     * @throws WebhookVerificationException
     */
    public static function verifySignature(
        #[\SensitiveParameter]
        string $secret,
        string $timestamp,
        #[\SensitiveParameter]
        string $signature,
        ?float $tolerance = self::DEFAULT_TOLERANCE_SECONDS,
        ?float $now = null,
    ): void {
        self::checkSecret($secret);
        self::checkTolerance($tolerance);
        $received = trim($signature);
        if ($received === '') {
            throw new WebhookVerificationException('X-IB-Signature 헤더가 없습니다');
        }
        if (preg_match('/^[0-9]{1,16}$/D', $timestamp) !== 1) {
            throw new WebhookVerificationException('X-IB-Timestamp는 1~16자리 숫자여야 합니다');
        }
        $sentAt = strlen($timestamp) >= 13 ? ((float) $timestamp) / 1000 : (float) $timestamp;
        if ($tolerance !== null && abs(($now ?? microtime(true)) - $sentAt) > $tolerance) {
            throw new WebhookVerificationException(sprintf('X-IB-Timestamp가 허용 범위(%.0f초)를 벗어났습니다', $tolerance));
        }

        $digest = hash_hmac('sha256', $timestamp, $secret, true);
        $decoded = base64_decode($received, true);
        // evaluate both comparisons so the timing does not depend on which encoding matched
        $hexOk = hash_equals(bin2hex($digest), strtolower($received));
        $base64Ok = hash_equals($digest, $decoded === false ? '' : $decoded);
        if (!$hexOk && !$base64Ok) {
            throw new WebhookVerificationException('서명이 일치하지 않습니다');
        }
    }

    /**
     * Verifies the signature headers of a request. Header names are case-insensitive; PSR-7 messages,
     * `getallheaders()` arrays, framework arrays (`['x-ib-timestamp' => ['...']]`) and `$_SERVER`
     * (`HTTP_X_IB_TIMESTAMP`) are accepted.
     *
     * @param MessageInterface|array<array-key, mixed> $headers
     *
     * @throws WebhookVerificationException
     */
    public function verify(#[\SensitiveParameter] MessageInterface|array $headers): void
    {
        self::verifySignature(
            $this->secret->reveal(),
            self::header($headers, self::TIMESTAMP_HEADER),
            self::header($headers, self::SIGNATURE_HEADER),
            $this->tolerance,
        );
    }

    /**
     * Verifies, then parses a delivery report webhook.
     *
     * @param MessageInterface|array<array-key, mixed> $headers
     *
     * @throws WebhookVerificationException
     */
    public function report(MessageInterface|array $headers, #[\SensitiveParameter] string $rawBody): ReportWebhookPayload
    {
        $this->verify($headers);

        return self::parseReport($rawBody);
    }

    /**
     * Verifies, then parses an MO webhook.
     *
     * @param MessageInterface|array<array-key, mixed> $headers
     *
     * @throws WebhookVerificationException
     */
    public function mo(MessageInterface|array $headers, #[\SensitiveParameter] string $rawBody): MoWebhookPayload
    {
        $this->verify($headers);

        return self::parseMo($rawBody);
    }

    /**
     * Parses a report webhook body. Does not verify the signature; use {@see self::report()} for that.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookVerificationException the body is not a JSON object with the required fields
     */
    public static function parseReport(#[\SensitiveParameter] string|array $body): ReportWebhookPayload
    {
        return self::parse($body, ReportWebhookPayload::class, ReportWebhookPayload::REQUIRED);
    }

    /**
     * Parses an MO webhook body. Does not verify the signature; use {@see self::mo()} for that.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookVerificationException
     */
    public static function parseMo(#[\SensitiveParameter] string|array $body): MoWebhookPayload
    {
        return self::parse($body, MoWebhookPayload::class, MoWebhookPayload::REQUIRED);
    }

    /**
     * The response body Bizgo expects for report and MO webhooks: `{"msgKey": "<received msgKey>"}`.
     *
     * @return array{msgKey: string}
     */
    public static function ack(?string $msgKey): array
    {
        return ['msgKey' => (string) $msgKey];
    }

    /**
     * The response body Bizgo expects for counsel (상담톡) webhooks: `{"code": "A000", "result": "Success"}`.
     *
     * @return array{code: string, result: string}
     */
    public static function counselAck(): array
    {
        return ['code' => 'A000', 'result' => 'Success'];
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return [
            'tolerance' => $this->tolerance,
            'secret' => '[hidden]',
        ];
    }

    /** @return never */
    public function __serialize(): array
    {
        throw new \LogicException('WebhookReceiver holds a secret and cannot be serialized');
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        throw new \LogicException('WebhookReceiver cannot be unserialized');
    }

    /** @throws WebhookVerificationException */
    private static function checkSecret(#[\SensitiveParameter] string $secret): void
    {
        if (trim($secret) === '') {
            throw new WebhookVerificationException('웹훅 secret이 비어 있습니다 (공백만 있는 값도 쓸 수 없습니다)');
        }
    }

    /** @throws WebhookVerificationException */
    private static function checkTolerance(?float $tolerance): void
    {
        if ($tolerance !== null && (!is_finite($tolerance) || $tolerance <= 0)) {
            throw new WebhookVerificationException('tolerance는 0보다 큰 유한한 초 단위 값이거나, 검사를 끄려면 null이어야 합니다');
        }
    }

    /**
     * @template T of ResponseModel
     *
     * @param string|array<array-key, mixed> $body
     * @param class-string<T> $class
     * @param list<string> $required
     *
     * @return T
     *
     * @throws WebhookPayloadException
     */
    private static function parse(#[\SensitiveParameter] string|array $body, string $class, array $required): ResponseModel
    {
        if (is_string($body)) {
            if (strlen($body) > self::MAX_BODY_BYTES) {
                throw new WebhookPayloadException('웹훅 본문이 너무 큽니다 (최대 1MB)');
            }
            try {
                $body = json_decode($body, true, self::MAX_JSON_DEPTH + 1, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
            } catch (\JsonException $e) {
                throw new WebhookPayloadException($e->getCode() === JSON_ERROR_DEPTH
                    ? '웹훅 본문 JSON의 중첩이 너무 깊습니다 (최대 ' . self::MAX_JSON_DEPTH . '단계)'
                    : '웹훅 본문이 JSON이 아닙니다');
            }
        }
        if (!is_array($body) || ($body !== [] && array_is_list($body))) {
            throw new WebhookPayloadException('웹훅 본문이 JSON 객체가 아닙니다');
        }
        $missing = array_values(array_filter($required, static fn (string $key): bool => ($body[$key] ?? null) === null));
        if ($missing !== []) {
            throw new WebhookPayloadException('웹훅 본문에 필수 필드가 없습니다: ' . implode(', ', $missing));
        }
        try {
            return $class::fromArray($body);
        } catch (InvalidResponseException $e) {
            throw new WebhookPayloadException('웹훅 본문 형식이 문서와 다릅니다: ' . $e->getMessage());
        }
    }

    /**
     * @param MessageInterface|array<array-key, mixed> $headers
     */
    private static function header(#[\SensitiveParameter] MessageInterface|array $headers, string $name): string
    {
        if ($headers instanceof MessageInterface) {
            return $headers->getHeaderLine($name);
        }
        $wanted = strtolower($name);
        foreach ($headers as $key => $value) {
            $key = strtolower(str_replace('_', '-', (string) $key));
            if (str_starts_with($key, 'http-')) {
                $key = substr($key, 5); // $_SERVER style: HTTP_X_IB_TIMESTAMP
            }
            if ($key !== $wanted) {
                continue;
            }
            if (is_array($value)) {
                $value = $value[array_key_first($value)] ?? '';
            }

            return is_string($value) ? $value : '';
        }

        return '';
    }
}
