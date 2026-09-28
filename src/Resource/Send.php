<?php

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\BulkSendException;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Retry;
use Bizgo\Http\Transport;
use Bizgo\Model\ChannelMessage;
use Bizgo\Model\Destination;
use Bizgo\Model\MessageFlowItem;
use Bizgo\Model\MmsMessage;
use Bizgo\Model\SendOmniRequest;
use Bizgo\Model\SendOmniResponse;
use Bizgo\Model\SmsMessage;
use Bizgo\Result\BulkChunkError;
use Bizgo\Result\BulkSendResult;
use Bizgo\Result\SendResult;

/**
 * Message sending: `POST /api/comm/v1/send/omni`. Access as `$client->send`.
 *
 * Sends are retried automatically only when that cannot deliver a message twice: on HTTP 429 always,
 * and after timeouts, network errors and 5xx only when `idempotencyKey` is set.
 */
final class Send
{
    public const PATH = '/api/comm/v1/send/omni';

    /** Recipients per send request (the API limit). */
    public const MAX_CHUNK = 200;

    /** Longest idempotency key the API accepts. */
    public const MAX_IDEMPOTENCY_KEY = 200;

    /**
     * Seconds (24 hours) sent as `idempotencyTtl` when an `idempotencyKey` is set without a TTL.
     *
     * Bizgo rejects an `idempotencyKey` without `idempotencyTtl` (A309), so the SDK fills this value for every
     * request body that has both fields (sends, bulk chunks, generated operations). An explicit TTL, including 0,
     * is sent as given; without a key no TTL is added.
     */
    public const DEFAULT_IDEMPOTENCY_TTL = 86400;

    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * Sends one message, with optional fallback channels, to up to 200 recipients.
     *
     * @param string|Destination|array<array-key, mixed> $to recipient phone number(s), {@see Destination} objects
     *        (for `replaceWords` substitution or a per-recipient `ref`) or equivalent arrays. Max 200.
     * @param list<ChannelMessage|MessageFlowItem|array<array-key, mixed>> $messages channel messages in fallback
     *        order: if the first fails, the next is sent. Example: `[new AlimtalkMessage(...), new SmsMessage(...)]`.
     * @param string|null $ref your reference value; returned in reports
     * @param string|null $groupKey groups messages in message insight statistics
     * @param string|null $paymentCode department code for billing
     * @param string|null $idempotencyKey up to 200 chars. A resend with the same key within `idempotencyTtl` seconds
     *        is not delivered twice: it succeeds and the recipients already accepted come back in
     *        `SendResult::duplicates()` (per-recipient A301), not in `failed()`. It also enables automatic retry
     *        after timeouts.
     * @param int|null $idempotencyTtl seconds (0-86400) the idempotency key is remembered. Null with an
     *        `idempotencyKey` sends {@see self::DEFAULT_IDEMPOTENCY_TTL} (86400); an explicit value, 0 included, is kept
     *
     * @return SendResult check `failed()` for rejected recipients (`duplicates()` holds recipients already accepted
     *         with the same idempotency key); final results arrive later as reports
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException Bizgo rejected the request
     * @throws ConnectionException network failure; the message may or may not have been accepted
     * @throws InvalidResponseException
     */
    public function omni(
        #[\SensitiveParameter]
        string|Destination|array $to,
        #[\SensitiveParameter]
        array $messages,
        ?string $ref = null,
        ?string $groupKey = null,
        ?string $paymentCode = null,
        ?string $idempotencyKey = null,
        ?int $idempotencyTtl = null,
    ): SendResult {
        if ($messages === []) {
            throw ValidationException::of('messages', '비어 있습니다. 채널 메시지를 1개 이상 넣으세요');
        }

        return $this->request(new SendOmniRequest(
            destinations: self::destinations($to),
            messageFlow: self::flow($messages),
            paymentCode: $paymentCode,
            groupKey: $groupKey,
            idempotencyKey: $idempotencyKey,
            idempotencyTtl: $idempotencyTtl,
            ref: $ref,
        ));
    }

    /**
     * Sends a prepared {@see SendOmniRequest}, or an equivalent array with API field names.
     *
     * With `idempotencyKey` and no `idempotencyTtl`, `idempotencyTtl` {@see self::DEFAULT_IDEMPOTENCY_TTL} is sent
     * (your model or array is not changed).
     *
     * @param SendOmniRequest|array<array-key, mixed> $request
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function request(#[\SensitiveParameter] SendOmniRequest|array $request): SendResult
    {
        if (is_array($request)) {
            $request = SendOmniRequest::fromArray($request);
        }
        // Without an idempotency key a retried send after a timeout could deliver twice.
        $retry = $request->idempotencyKey !== null && $request->idempotencyKey !== '' ? Retry::Safe : Retry::RateLimitOnly;
        $response = $this->transport->call('sendOmni', SendOmniResponse::class, json: $request, retry: $retry);

        return new SendResult(
            destinations: $response->data->data->destinations ?? [],
            ref: $response->data?->ref,
            trackingId: $response->common?->infobankTrId,
        );
    }

    /**
     * Sends an SMS: text up to 90 bytes in EUC-KR (about 45 Korean characters).
     *
     * @param string|Destination|array<array-key, mixed> $to
     * @param string $from sender number registered in the Bizgo console
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function sms(
        #[\SensitiveParameter]
        string|Destination|array $to,
        #[\SensitiveParameter]
        string $from,
        #[\SensitiveParameter]
        string $text,
        ?string $ref = null,
        ?string $idempotencyKey = null,
    ): SendResult {
        return $this->omni(to: $to, messages: [new SmsMessage(from: $from, text: $text)], ref: $ref, idempotencyKey: $idempotencyKey);
    }

    /**
     * Sends an LMS (text up to 2,000 bytes in EUC-KR).
     *
     * @param string|Destination|array<array-key, mixed> $to
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function lms(
        #[\SensitiveParameter]
        string|Destination|array $to,
        #[\SensitiveParameter]
        string $from,
        #[\SensitiveParameter]
        string $text,
        ?string $title = null,
        ?string $ref = null,
        ?string $idempotencyKey = null,
    ): SendResult {
        $message = new MmsMessage(from: $from, text: $text, title: $title === '' ? null : $title);

        return $this->omni(to: $to, messages: [$message], ref: $ref, idempotencyKey: $idempotencyKey);
    }

    /**
     * Sends an MMS. Get `fileKeys` (max 3) from `$client->files->uploadMms()` first.
     *
     * @param string|Destination|array<array-key, mixed> $to
     * @param list<string> $fileKeys
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function mms(
        #[\SensitiveParameter]
        string|Destination|array $to,
        #[\SensitiveParameter]
        string $from,
        #[\SensitiveParameter]
        string $text,
        array $fileKeys,
        ?string $title = null,
        ?string $ref = null,
        ?string $idempotencyKey = null,
    ): SendResult {
        $message = new MmsMessage(from: $from, text: $text, title: $title === '' ? null : $title, fileKey: array_values($fileKeys));

        return $this->omni(to: $to, messages: [$message], ref: $ref, idempotencyKey: $idempotencyKey);
    }

    /**
     * Sends one message to any number of recipients, `$chunkSize` (1-200) at a time (SDK-DESIGN.md §11.1).
     *
     * Every chunk is validated before anything is sent. A chunk that fails (API error, network error, invalid
     * response) is recorded in `errors` with its chunk number and recipient index range, and the other chunks are
     * still sent. The rate limiter paces the chunks (200 recipients per second by default, so one full chunk per
     * second). PHP sends the chunks one after another, so `$concurrency` only caps the parallelism.
     *
     * With `$idempotencyKeyPrefix` each chunk gets the key `<prefix>-<chunkSize>-<startIndex>-<hash8>`, where
     * `hash8` is the first 8 hex digits of SHA-256 over the chunk's phone numbers joined with "\n". Re-running the
     * same list with the same `$chunkSize` reuses the keys, so accepted chunks are not delivered twice (their recipients
     * come back in `duplicates()`, per-recipient A301, not in `failed()` or `errors`; a request-level A301 is still
     * recorded as DuplicateRequestException in `errors`) and a key is never reused for other recipients. To resend only the failed chunks,
     * use the chunk numbers in `errors`. The keys also make timeouts safe to retry. Each keyed chunk is sent with
     * `$idempotencyTtl`, or {@see self::DEFAULT_IDEMPOTENCY_TTL} (86400) when it is null.
     *
     * @param iterable<string|Destination|array<array-key, mixed>> $to recipients (no limit)
     * @param list<ChannelMessage|MessageFlowItem|array<array-key, mixed>> $messages as for {@see self::omni()}
     * @param int $concurrency accepted for parity with the other SDKs; PHP sends one request at a time
     * @param int|null $idempotencyTtl seconds (0-86400) each chunk key is remembered; null sends 86400 with a prefix
     *
     * @throws ValidationException nothing was sent: an option, a recipient or a message is invalid (indexes only)
     * @throws BulkSendException a configuration error or PHP Error stopped the send; `partial` has the answered chunks
     */
    public function bulk(
        #[\SensitiveParameter]
        iterable $to,
        #[\SensitiveParameter]
        array $messages,
        int $chunkSize = self::MAX_CHUNK,
        int $concurrency = 4,
        ?string $idempotencyKeyPrefix = null,
        ?string $ref = null,
        ?string $groupKey = null,
        ?string $paymentCode = null,
        ?int $idempotencyTtl = null,
    ): BulkSendResult {
        if ($chunkSize < 1 || $chunkSize > self::MAX_CHUNK) {
            throw ValidationException::of('chunkSize', '1~' . self::MAX_CHUNK . '이어야 합니다');
        }
        if ($concurrency < 1) {
            throw ValidationException::of('concurrency', '1 이상이어야 합니다');
        }
        if ($idempotencyKeyPrefix !== null && trim($idempotencyKeyPrefix) === '') {
            throw ValidationException::of('idempotencyKeyPrefix', '비어 있지 않은 문자열이어야 합니다');
        }
        if ($messages === []) {
            throw ValidationException::of('messages', '비어 있습니다. 채널 메시지를 1개 이상 넣으세요');
        }
        $flow = self::flow($messages);
        $list = is_array($to) ? array_values($to) : iterator_to_array($to, false);
        if ($list === []) {
            throw ValidationException::of('to', '수신자가 없습니다');
        }
        try {
            $recipients = self::destinations($list);
        } catch (ValidationException $e) {
            throw self::renamePaths($e, 0);
        }
        // build (and validate) every chunk first: nothing is sent when one of them is invalid
        $chunks = [];
        $errors = [];
        foreach (array_chunk($recipients, $chunkSize) as $index => $chunk) {
            $start = $index * $chunkSize;
            $key = null;
            if ($idempotencyKeyPrefix !== null) {
                $key = self::bulkIdempotencyKey($idempotencyKeyPrefix, $chunkSize, $start, $chunk);
                if (mb_strlen($key) > self::MAX_IDEMPOTENCY_KEY) {
                    throw ValidationException::of(
                        'idempotencyKeyPrefix',
                        '너무 깁니다 (생성된 멱등성 키가 ' . self::MAX_IDEMPOTENCY_KEY . '자를 넘음)',
                    );
                }
            }
            try {
                $chunks[] = [$index, $start, $start + count($chunk), new SendOmniRequest(
                    destinations: $chunk,
                    messageFlow: $flow,
                    paymentCode: $paymentCode,
                    groupKey: $groupKey,
                    idempotencyKey: $key,
                    idempotencyTtl: $idempotencyTtl,
                    ref: $ref,
                )];
            } catch (ValidationException $e) {
                array_push($errors, ...self::renamePaths($e, $start)->errors);
            }
        }
        if ($errors !== []) {
            throw new ValidationException(array_values(array_unique($errors, SORT_REGULAR)));
        }

        $results = [];
        $failures = [];
        foreach ($chunks as [$index, $start, $end, $request]) {
            try {
                $results[$index] = $this->request($request);
            } catch (\Throwable $e) {
                if ($e instanceof ConfigurationException || $e instanceof \Error) {
                    // stop, but hand back what was already accepted so it is never sent again
                    throw new BulkSendException(new BulkSendResult($results, $failures), $index, $e);
                }
                // every other per-chunk failure is reported; it never loses the other chunks or their results
                $results[$index] = null;
                $failures[] = new BulkChunkError($index, $start, $end, $e);
            }
        }

        return new BulkSendResult($results, $failures);
    }

    /**
     * `<prefix>-<chunkSize>-<startIndex>-<hash8>` (SDK-DESIGN.md §12.3): `hash8` is the first 8 hex digits of
     * SHA-256 over the chunk's phone numbers, in order, joined with "\n" (UTF-8).
     *
     * @param list<Destination> $chunk
     */
    public static function bulkIdempotencyKey(string $prefix, int $chunkSize, int $start, #[\SensitiveParameter] array $chunk): string
    {
        $numbers = array_map(static fn (Destination $d): string => $d->to, $chunk);

        return sprintf('%s-%d-%d-%s', $prefix, $chunkSize, $start, substr(hash('sha256', implode("\n", $numbers)), 0, 8));
    }

    /** `destinations[i]` paths of a chunk as `to[start + i]`, the index in the caller's list. */
    private static function renamePaths(ValidationException $e, int $start): ValidationException
    {
        $errors = [];
        foreach ($e->errors as $error) {
            $path = preg_replace_callback(
                '/^destinations\[(\d+)\]/',
                static fn (array $m): string => 'to[' . ((int) $m[1] + $start) . ']',
                $error['path'],
            );
            $errors[] = ['path' => $path ?? $error['path'], 'reason' => $error['reason']];
        }

        return new ValidationException($errors);
    }

    /**
     * @param string|Destination|array<array-key, mixed> $to
     *
     * @return list<Destination>
     *
     * @throws ValidationException
     */
    private static function destinations(#[\SensitiveParameter] string|Destination|array $to): array
    {
        if (is_string($to) || $to instanceof Destination) {
            $to = [$to];
        } elseif ($to !== [] && !array_is_list($to)) {
            $to = [$to]; // a single destination given as an array: ['to' => ..., 'replaceWords' => [...]]
        }
        $out = [];
        $errors = [];
        foreach ($to as $i => $item) {
            try {
                $out[] = match (true) {
                    is_string($item) => new Destination(to: $item),
                    $item instanceof Destination => $item,
                    is_array($item) => Destination::fromArray($item, "destinations[{$i}]"),
                    default => throw ValidationException::of("destinations[{$i}]", '전화번호 문자열, Destination 또는 배열이어야 합니다'),
                };
            } catch (ValidationException $e) {
                array_push($errors, ...$e->errors);
            }
        }
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $out;
    }

    /**
     * @param array<array-key, mixed> $messages
     *
     * @return list<MessageFlowItem>
     *
     * @throws ValidationException
     */
    private static function flow(#[\SensitiveParameter] array $messages): array
    {
        $out = [];
        $errors = [];
        foreach ($messages as $i => $message) {
            try {
                $out[] = match (true) {
                    $message instanceof ChannelMessage, $message instanceof MessageFlowItem => MessageFlowItem::of($message),
                    is_array($message) => MessageFlowItem::fromArray($message, "messageFlow[{$i}]"),
                    default => throw ValidationException::of("messageFlow[{$i}]", '채널 메시지 모델(SmsMessage 등)이어야 합니다'),
                };
            } catch (ValidationException $e) {
                array_push($errors, ...$e->errors);
            }
        }
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $out;
    }
}
