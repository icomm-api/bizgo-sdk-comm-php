<?php

declare(strict_types=1);

namespace Bizgo\Tests\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * An offline PSR-18 client: answers from a queue (or a callback per request) and records every request.
 */
final class MockHttpClient implements ClientInterface
{
    /** @var list<RequestInterface> */
    public array $requests = [];

    /** @var list<ResponseInterface|ClientExceptionInterface|\Closure(RequestInterface): ResponseInterface> */
    private array $queue = [];

    /** @var (ResponseInterface|ClientExceptionInterface|\Closure(RequestInterface): ResponseInterface)|null */
    private mixed $fallback = null;

    /** Queue one response (or exception) for the next request. */
    public function push(ResponseInterface|ClientExceptionInterface|\Closure $answer): self
    {
        $this->queue[] = $answer;

        return $this;
    }

    /** Answer every request (after the queue is empty) with this. */
    public function always(ResponseInterface|ClientExceptionInterface|\Closure $answer): self
    {
        $this->fallback = $answer;

        return $this;
    }

    /**
     * @param array<array-key, mixed>|string $body
     * @param array<string, string> $headers
     */
    public static function json(array|string $body, int $status = 200, array $headers = []): ResponseInterface
    {
        $raw = is_string($body) ? $body : json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        return new Response($status, $headers + ['Content-Type' => 'application/json'], $raw);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        $answer = $this->queue === [] ? $this->fallback : array_shift($this->queue);
        if ($answer === null) {
            throw new \LogicException('no mock response for ' . $request->getMethod() . ' ' . $request->getUri()->getPath());
        }
        if ($answer instanceof ClientExceptionInterface) {
            throw $answer;
        }
        $response = $answer instanceof \Closure ? $answer($request) : $answer;
        $response->getBody()->rewind(); // the same response object can be answered more than once

        return $response;
    }

    public function last(): RequestInterface
    {
        return $this->requests[count($this->requests) - 1] ?? throw new \LogicException('no request was sent');
    }

    public function count(): int
    {
        return count($this->requests);
    }

    /** @return array<array-key, mixed> */
    public function lastJson(): array
    {
        $decoded = json_decode((string) $this->last()->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : throw new \LogicException('request body is not a JSON object');
    }

    /** A value inside the last JSON request body, by dotted path (`messageFlow.0.sms`). */
    public function lastAt(string $path): mixed
    {
        $value = $this->lastJson();
        foreach (explode('.', $path) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /** @return array<string, string> */
    public function lastQuery(): array
    {
        return self::query($this->last());
    }

    /** @return array<string, string> */
    public static function query(RequestInterface $request): array
    {
        parse_str($request->getUri()->getQuery(), $query);
        $out = [];
        foreach ($query as $key => $value) {
            $out[(string) $key] = is_string($value) ? $value : '';
        }

        return $out;
    }
}
