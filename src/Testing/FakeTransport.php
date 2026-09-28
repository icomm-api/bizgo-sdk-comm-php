<?php

declare(strict_types=1);

namespace Bizgo\Testing;

use Bizgo\AppInfo;
use Bizgo\BizgoClient;
use Bizgo\Environment;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Http\HttpRequest;
use Bizgo\Http\HttpResponse;
use Bizgo\Http\HttpSender;
use Bizgo\Internal\Operations;
use Bizgo\Observability\Hook;
use Bizgo\RateLimit;
use Psr\Log\LoggerInterface;

/**
 * An in-memory stand-in for the Bizgo API, to test your own code without a network or an API key
 * (SDK-DESIGN.md §11.3). Part of the package but loaded only when you use it (PSR-4 autoloading is lazy).
 *
 * ```php
 * use Bizgo\Testing\FakeTransport;
 *
 * $fake = new FakeTransport();
 * $client = $fake->client();          // placeholder key, sandbox URL, no rate limit, never connects
 *
 * $client->send->sms(to: '01000000000', from: '01000000000', text: 'hello'); // default: every recipient A000
 * $fake->lastRequest()->operationId;  // 'sendOmni'
 * $fake->lastRequest()->json;         // the JSON body that would have been sent
 *
 * $fake->on('sendOmni')->fail('service', 200, 'A020');                       // next call: RateLimitException
 * $fake->on('sendOmni')->sendResult('A000', 'A306');                         // second recipient rejected
 * $fake->on('sendOmni')->sendResult('A301', 'A301');                         // resend: both in duplicates()
 * $fake->on('GET', '/api/comm/v1/report/inquiry/{msgKey}')->data(['report' => []]);
 *
 * $signed = FakeTransport::signWebhook('test-webhook-secret', ['msgKey' => 'KEY001']); // report/MO WebhookReceiver tests
 * ```
 *
 * Every request is recorded with its operation, method, path, query and JSON body (or multipart field names and file
 * sizes). Header values, including the API key, are never recorded. Without a stub an operation answers with a
 * success envelope: sends (`sendOmni`, `createReservation`, `addReservationRecipients`) accept every recipient with a
 * fake `msgKey` (`createReservation` also returns a fake `resvKey`), MMS/RCS uploads return a fake `fileKey`/`media`,
 * other operations return `data.data = {}`. A path that is not in the spec answers HTTP 404 (gateway `A404`).
 */
final class FakeTransport implements HttpSender
{
    public const API_KEY = 'test-api-key-not-real';

    /** @var list<RecordedRequest> */
    private array $requests = [];

    /** @var array<string, list<\Closure(RecordedRequest): HttpResponse>> */
    private array $stubs = [];

    private int $msgKeys = 0;

    /**
     * A {@see BizgoClient} that talks to this fake: placeholder key, sandbox URL, rate limit off (unless given), no
     * real sleeping (unless `$sleep` is given).
     *
     * @param list<Hook> $hooks
     * @param (\Closure(float): void)|null $sleep
     * @param (\Closure(): float)|null $clock
     *
     * @throws ConfigurationException
     */
    public function client(
        array $hooks = [],
        int $maxRetries = 2,
        ?LoggerInterface $logger = null,
        ?AppInfo $appInfo = null,
        ?RateLimit $rateLimit = null,
        ?\Closure $sleep = null,
        ?\Closure $clock = null,
    ): BizgoClient {
        return new BizgoClient(
            apiKey: self::API_KEY,
            environment: Environment::Sandbox,
            maxRetries: $maxRetries,
            logger: $logger,
            sleep: $sleep ?? static function (float $seconds): void {
            },
            appInfo: $appInfo,
            rateLimit: $rateLimit,
            hooks: $hooks,
            clock: $clock,
            sender: $this,
        );
    }

    /**
     * Stubs an operation by `operationId`, or by HTTP method and path template.
     *
     * @throws \InvalidArgumentException unknown operation
     */
    public function on(string $operationOrMethod, ?string $pathTemplate = null): Stub
    {
        $id = null;
        if ($pathTemplate === null) {
            $id = isset(Operations::ALL[$operationOrMethod]) ? $operationOrMethod : null;
        } else {
            foreach (Operations::ALL as $opId => $op) {
                if ($op['method'] === strtoupper($operationOrMethod) && $op['path'] === $pathTemplate) {
                    $id = $opId;
                }
            }
        }
        if ($id === null) {
            throw new \InvalidArgumentException(trim("unknown operation: {$operationOrMethod} " . ($pathTemplate ?? '')));
        }

        return new Stub($this, function (\Closure $reply) use ($id): void {
            $this->stubs[$id][] = $reply;
        });
    }

    /** @return list<RecordedRequest> every recorded request, oldest first */
    public function requests(): array
    {
        return $this->requests;
    }

    /** @return list<RecordedRequest> recorded requests of one operation */
    public function requestsFor(string $operationId): array
    {
        return array_values(array_filter($this->requests, static fn (RecordedRequest $r): bool => $r->operationId === $operationId));
    }

    /** The most recent request. */
    public function lastRequest(): RecordedRequest
    {
        return $this->requests[count($this->requests) - 1] ?? throw new \LogicException('no request was recorded');
    }

    /** Forgets recorded requests and stubs. */
    public function reset(): void
    {
        $this->requests = [];
        $this->stubs = [];
    }

    /** @internal */
    public function send(HttpRequest $request): HttpResponse
    {
        $recorded = self::record($request);
        $this->requests[] = $recorded;
        if ($recorded->operationId === null) {
            return self::response(404, self::error('gateway', 'A404', 'Not Found'));
        }
        $queue = $this->stubs[$recorded->operationId] ?? [];
        if ($queue === []) {
            return $this->defaultResponse($recorded);
        }
        $reply = $queue[0];
        if (count($queue) > 1) {
            $this->stubs[$recorded->operationId] = array_slice($queue, 1); // the last stub keeps answering
        }

        return $reply($recorded);
    }

    /** @internal */
    public function name(): string
    {
        return 'fake';
    }

    /**
     * Builds a signed webhook request for testing your {@see \Bizgo\Webhook\WebhookReceiver} handler:
     * `X-IB-Timestamp` (default now, epoch milliseconds as Bizgo sends it), `X-IB-Signature` =
     * HmacSHA256(secret, timestamp) in hex (or base64) and the JSON body. For report and MO webhooks; counsel webhooks
     * are not signed, so test those with the plain JSON body (`WebhookReceiver::parseCounselMessage($body)` etc.).
     *
     * @param array<array-key, mixed>|string $payload a JSON string or an array to encode
     * @param 'hex'|'base64' $encoding
     */
    public static function signWebhook(
        #[\SensitiveParameter]
        string $secret,
        array|string $payload,
        string|int|null $timestamp = null,
        string $encoding = 'hex',
    ): SignedWebhook {
        $stamp = (string) ($timestamp ?? (int) floor(microtime(true) * 1000));
        $digest = hash_hmac('sha256', $stamp, $secret, true);
        $signature = $encoding === 'base64' ? base64_encode($digest) : bin2hex($digest);
        $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $body = is_string($payload) ? $payload : json_encode($payload, $flags);

        return new SignedWebhook(['Content-Type' => 'application/json', 'X-IB-Timestamp' => $stamp, 'X-IB-Signature' => $signature], $body);
    }

    /**
     * `{"common": {"authCode": "A000", ...}, "data": {"code": "A000", "result": "Success", "data": <data>}}`
     *
     * @param array<array-key, mixed>|null $data
     * @param array<string, mixed> $extra fields next to `data.data`
     *
     * @return array<string, mixed>
     */
    public static function success(?array $data = null, array $extra = []): array
    {
        $inner = ['code' => 'A000', 'result' => 'Success'] + $extra;
        if ($data !== null) {
            $inner['data'] = $data === [] ? new \stdClass() : $data;
        }

        return ['common' => ['authCode' => 'A000', 'authResult' => 'Success', 'infobankTrId' => 'FAKE-TR-ID'], 'data' => $inner];
    }

    /**
     * An error envelope: gateway errors set `common.authCode`, service errors set `data.code`.
     *
     * @param 'gateway'|'service' $layer
     *
     * @return array<string, mixed>
     */
    public static function error(string $layer, string $code, string $message = 'Failed'): array
    {
        if ($layer === 'gateway') {
            return ['common' => ['authCode' => $code, 'authResult' => $message, 'infobankTrId' => 'FAKE-TR-ID']];
        }

        return [
            'common' => ['authCode' => 'A000', 'authResult' => 'Success', 'infobankTrId' => 'FAKE-TR-ID'],
            'data' => ['code' => $code, 'result' => $message],
        ];
    }

    /**
     * @param array<array-key, mixed>|string $body
     * @param array<string, string> $headers
     *
     * @internal
     */
    public static function response(int $status, array|string $body, array $headers = []): HttpResponse
    {
        $raw = is_string($body) ? $body : json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $lower = ['content-type' => 'application/json'];
        foreach ($headers as $name => $value) {
            $lower[strtolower($name)] = $value;
        }

        return new HttpResponse($status, $lower, $raw);
    }

    /** @internal */
    public function defaultResponse(RecordedRequest $request): HttpResponse
    {
        $id = $request->operationId;
        if (in_array($id, ['sendOmni', 'createReservation', 'addReservationRecipients'], true)) {
            $count = max(count(self::destinations($request->json)), 1);
            $body = $this->sendBody($request->json, array_fill(0, $count, 'A000'));
            if ($id === 'createReservation') {
                /** @var array<string, mixed> $data */
                $data = $body['data'];
                $data['resvKey'] = 'FAKE-RESVKEY-000001'; // next to data.data (x-sdk-result: data)
                $body['data'] = $data;
            }

            return self::response(200, $body);
        }
        if ($id === 'uploadMmsFile') {
            return self::response(200, self::success(['fileKey' => 'FAKE-FILEKEY-000001']));
        }
        if ($id === 'uploadRcsFile') {
            return self::response(200, self::success(['media' => 'FAKE-MEDIA-000001']));
        }

        return self::response(200, self::success([]));
    }

    /**
     * A send response with one destination result per code.
     *
     * @param array<array-key, string> $codes
     *
     * @return array<string, mixed>
     *
     * @internal
     */
    public function sendBody(mixed $request, array $codes): array
    {
        $given = self::destinations($request);
        $destinations = [];
        foreach (array_values($codes) as $i => $code) {
            $entry = [];
            if (is_string($given[$i]['to'] ?? null)) {
                $entry['to'] = $given[$i]['to'];
            }
            if (is_string($given[$i]['ref'] ?? null)) {
                $entry['ref'] = $given[$i]['ref'];
            }
            $this->msgKeys++;
            $entry['msgKey'] = sprintf('FAKE-MSGKEY-%06d', $this->msgKeys);
            $entry['code'] = $code;
            $entry['result'] = $code === 'A000' ? 'Success' : 'Failed';
            $destinations[] = $entry;
        }
        $ref = is_array($request) && is_string($request['ref'] ?? null) ? ['ref' => $request['ref']] : [];

        return self::success(['destinations' => $destinations], $ref);
    }

    /** @return list<array<array-key, mixed>> */
    private static function destinations(mixed $request): array
    {
        $items = is_array($request) ? ($request['destinations'] ?? null) : null;

        return is_array($items) ? array_values(array_filter($items, 'is_array')) : [];
    }

    private static function record(HttpRequest $request): RecordedRequest
    {
        $parts = parse_url($request->url);
        $path = is_array($parts) ? ($parts['path'] ?? '/') : '/';
        parse_str(is_array($parts) ? ($parts['query'] ?? '') : '', $query);
        $flatQuery = [];
        foreach ($query as $key => $value) {
            $flatQuery[(string) $key] = is_string($value) ? $value : '';
        }
        $match = null;
        $best = PHP_INT_MAX;
        foreach (Operations::ALL as $id => $op) {
            $pattern = '~^' . preg_replace('/\\\\\{[A-Za-z0-9_]+\\\\\}/', '[^/]+', preg_quote($op['path'], '~')) . '$~';
            $vars = substr_count($op['path'], '{');
            if ($op['method'] === $request->method && preg_match($pattern, $path) === 1 && $vars < $best) {
                $match = $id; // literal paths win over templates
                $best = $vars;
            }
        }
        $headers = array_change_key_case($request->headers, CASE_LOWER);
        $type = $headers['content-type'] ?? '';
        $json = null;
        $form = null;
        if ($request->body !== null && str_starts_with($type, 'application/json')) {
            try {
                $json = json_decode($request->body, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $json = null;
            }
        } elseif ($request->body !== null && preg_match('/boundary=(\S+)/', $type, $m) === 1) {
            $form = self::form($request->body, $m[1]);
        }
        $op = $match === null ? null : Operations::ALL[$match];

        return new RecordedRequest(
            $match,
            $op['name'] ?? null,
            $request->method,
            $path,
            $op['path'] ?? null,
            $flatQuery,
            $json,
            $form,
            array_keys($headers),
        );
    }

    /** @return array<string, string|RecordedFile|list<string|RecordedFile>> */
    private static function form(string $body, string $boundary): array
    {
        $out = [];
        foreach (explode('--' . $boundary, $body) as $part) {
            $split = strpos($part, "\r\n\r\n");
            if ($split === false) {
                continue;
            }
            $head = substr($part, 0, $split);
            $content = substr($part, $split + 4, -2);
            if (preg_match('/name="([^"]*)"/', $head, $name) !== 1) {
                continue;
            }
            $value = $content;
            if (preg_match('/filename="([^"]*)"/', $head, $file) === 1) {
                $contentType = preg_match('/Content-Type: ([^\r\n]+)/', $head, $t) === 1 ? $t[1] : 'application/octet-stream';
                $value = new RecordedFile($file[1], $contentType, strlen($content));
            }
            $existing = $out[$name[1]] ?? null;
            $out[$name[1]] = match (true) {
                $existing === null => $value,
                is_array($existing) => [...$existing, $value],
                default => [$existing, $value],
            };
        }

        return $out;
    }
}
