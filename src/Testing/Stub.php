<?php

declare(strict_types=1);

namespace Bizgo\Testing;

use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\TimeoutException;
use Bizgo\Http\HttpResponse;

/**
 * Stubbed responses of one operation, created by {@see FakeTransport::on()}. Each call queues one response; the last
 * one keeps answering once the others are used.
 */
final class Stub
{
    /**
     * @param \Closure(\Closure(RecordedRequest): HttpResponse): void $add
     *
     * @internal
     */
    public function __construct(
        private readonly FakeTransport $fake,
        private readonly \Closure $add,
    ) {
    }

    /**
     * Answers with a raw status and JSON body (and optional headers such as `Retry-After`).
     *
     * @param array<array-key, mixed>|string $body
     * @param array<string, string> $headers
     */
    public function respond(int $status, array|string $body, array $headers = []): self
    {
        $response = FakeTransport::response($status, $body, $headers);
        ($this->add)(static fn (RecordedRequest $r): HttpResponse => $response);

        return $this;
    }

    /**
     * Answers with a success envelope whose `data.data` is `$data` (`$extra` goes next to it, for example `resvKey`).
     *
     * @param array<array-key, mixed>|null $data
     * @param array<string, mixed> $extra
     */
    public function data(?array $data, array $extra = []): self
    {
        return $this->respond(200, FakeTransport::success($data, $extra));
    }

    /**
     * Answers a send with one destination result per code: `A000` accepted, `A301` already accepted with the same
     * idempotency key (in `duplicates()`), anything else rejected (e.g. `A306`).
     */
    public function sendResult(string ...$codes): self
    {
        $fake = $this->fake;
        ($this->add)(static fn (RecordedRequest $r): HttpResponse => FakeTransport::response(200, $fake->sendBody($r->json, $codes)));

        return $this;
    }

    /**
     * Answers with an API error, e.g. `fail('service', 200, 'A020')`, `fail('gateway', 401, 'A401')`.
     *
     * Pass `retryAfter: 0` for retried errors (429, 5xx) so your tests do not wait for the backoff.
     *
     * @param 'gateway'|'service' $layer
     */
    public function fail(string $layer, int $status, string $code, ?float $retryAfter = null): self
    {
        $headers = $retryAfter === null ? [] : ['retry-after' => (string) $retryAfter];

        return $this->respond($status, FakeTransport::error($layer, $code), $headers);
    }

    /** Fails like a refused connection ({@see ConnectionException}). */
    public function networkError(): self
    {
        ($this->add)(static fn (RecordedRequest $r): HttpResponse => throw new ConnectionException('서버에 연결하지 못했습니다 (fake)'));

        return $this;
    }

    /** Fails like a timeout ({@see TimeoutException}). */
    public function timeout(): self
    {
        ($this->add)(static fn (RecordedRequest $r): HttpResponse => throw new TimeoutException('요청 시간이 초과됐습니다 (fake)'));

        return $this;
    }

    /** Answers with the default success response of the operation. */
    public function default(): self
    {
        $fake = $this->fake;
        ($this->add)(static fn (RecordedRequest $r): HttpResponse => $fake->defaultResponse($r));

        return $this;
    }
}
