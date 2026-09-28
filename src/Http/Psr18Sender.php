<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\TimeoutException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * Sends requests through a PSR-18 client: Symfony's `Psr18Client` (hardened by {@see self::symfony()}) or any other
 * client that the caller explicitly trusted (`trustHttpClient: true`). Guzzle has its own {@see GuzzleSender}.
 *
 * A generic PSR-18 client cannot be inspected: it could follow redirects (re-sending a send with phone numbers to
 * another host), retry (delivering twice) or authenticate by itself (replacing the key). That is why such clients are
 * refused unless trusted (SDK-DESIGN.md §12.6). A 3xx response that reaches the SDK is never followed or retried.
 *
 * Client exceptions are not chained into the SDK exception because their messages usually contain the full request
 * URL (with the query string); only the exception class name is reported.
 *
 * @internal
 */
final class Psr18Sender implements HttpSender
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
    ) {
    }

    /**
     * A Symfony PSR-18 client with redirects and its own authentication turned off (`max_redirects: 0`,
     * `auth_basic`/`auth_bearer` cleared). A `RetryableHttpClient` anywhere in the chain is refused unless trusted:
     * it would send a request again (a send could be delivered twice).
     *
     * @throws ConfigurationException
     */
    public static function symfony(Psr18Client $client, bool $trusted): Psr18Client
    {
        if (!$trusted) {
            $inner = self::property($client, 'client');
            for ($depth = 0; is_object($inner) && $depth < 16; $depth++) {
                if ($inner instanceof RetryableHttpClient) {
                    throw new ConfigurationException(
                        'Symfony RetryableHttpClient는 쓸 수 없습니다. SDK가 재시도 정책(발송 중복 방지)을 직접 관리하므로, 클라이언트의 재시도는 '
                        . '발송을 여러 번 보낼 수 있습니다. 꼭 필요하면 위험을 이해한 뒤 trustHttpClient: true로 명시하세요.',
                    );
                }
                $inner = self::property($inner, 'client');
            }
        }

        return $client->withOptions(['max_redirects' => 0, 'auth_basic' => null, 'auth_bearer' => null]);
    }

    public function name(): string
    {
        return 'psr18/' . ResponseBody::shortName($this->client);
    }

    public function send(HttpRequest $request): HttpResponse
    {
        try {
            $message = $this->requests->createRequest($request->method, $request->url);
            foreach ($request->headers as $name => $value) {
                $message = $message->withHeader($name, $value);
            }
            if ($request->body !== null) {
                $message = $message->withBody($this->streams->createStream($request->body));
            }
            $response = $this->client->sendRequest($message);
        } catch (\Throwable $e) {
            // any client failure (PSR-18 exceptions, RuntimeException, Error): the class name only, never the URL
            $short = ResponseBody::shortName($e);
            $message = $e->getMessage(); // inspected only, never reported
            $timeout = str_contains($message, 'cURL error 28') || stripos($message, 'timed out') !== false;
            if ($timeout || stripos($short, 'timeout') !== false) {
                throw new TimeoutException('요청 시간이 초과됐습니다 (' . $short . ')');
            }
            throw new ConnectionException('서버에 연결하지 못했습니다 (' . $short . ')');
        }

        return ResponseBody::read($response);
    }

    private static function property(object $object, string $name): mixed
    {
        for ($class = new \ReflectionClass($object); $class !== false; $class = $class->getParentClass()) {
            if ($class->hasProperty($name)) {
                $property = $class->getProperty($name);

                return $property->isInitialized($object) ? $property->getValue($object) : null;
            }
        }

        return null;
    }
}
