<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\TimeoutException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

/**
 * Sends through a Guzzle client with the options that keep the key and the request where they belong
 * (SDK-DESIGN.md §12.6): every request is sent with `allow_redirects: false`, `auth: null`, `cookies: false` and
 * `http_errors: false` (the SDK judges the status), through a capped sink.
 *
 * {@see self::inspect()} refuses clients whose settings cannot be overridden per request: cURL options that follow
 * redirects, authenticate or keep cookies, and handler stacks with middleware other than Guzzle's stock
 * `http_errors`/`allow_redirects`/`cookies`/`prepare_body` (a custom middleware could retry, redirect or
 * authenticate) — unless the client is explicitly trusted.
 *
 * @internal
 */
final class GuzzleSender implements HttpSender
{
    /** cURL options that would follow redirects, authenticate or keep cookies behind the SDK's back. */
    private const FORBIDDEN_CURL = [
        'CURLOPT_FOLLOWLOCATION', 'CURLOPT_MAXREDIRS', 'CURLOPT_POSTREDIR', 'CURLOPT_UNRESTRICTED_AUTH', 'CURLOPT_HTTPAUTH',
        'CURLOPT_USERPWD', 'CURLOPT_USERNAME', 'CURLOPT_PASSWORD', 'CURLOPT_XOAUTH2_BEARER', 'CURLOPT_COOKIE',
        'CURLOPT_COOKIEFILE', 'CURLOPT_COOKIEJAR', 'CURLOPT_COOKIELIST', 'CURLOPT_COOKIESESSION', 'CURLOPT_HTTPHEADER',
    ];

    private const STOCK_MIDDLEWARE = ['http_errors', 'allow_redirects', 'cookies', 'prepare_body'];

    public function __construct(private readonly ClientInterface $client)
    {
    }

    /**
     * @throws ConfigurationException the client cannot be made safe
     */
    public static function inspect(ClientInterface $client, bool $trusted): void
    {
        $config = static function (string $option) use ($client): mixed {
            try {
                return $client->getConfig($option);
            } catch (\Throwable) {
                return null;
            }
        };
        $curl = $config('curl');
        if (is_array($curl)) {
            foreach (self::FORBIDDEN_CURL as $name) {
                if (defined($name) && array_key_exists(constant($name), $curl)) {
                    throw new ConfigurationException(
                        "Guzzle의 curl 옵션 {$name}은(는) 쓸 수 없습니다. 리다이렉트·자체 인증·쿠키는 요청(발송 포함, 전화번호 포함)을 "
                        . '다른 주소나 다른 인증 정보로 다시 보낼 수 있습니다.',
                    );
                }
            }
        }
        if ($trusted) {
            return;
        }
        $handler = $config('handler');
        if (!$handler instanceof HandlerStack || !self::stockStack($handler)) {
            throw new ConfigurationException(
                'Guzzle handler에 기본 미들웨어(http_errors, allow_redirects, cookies, prepare_body) 외의 미들웨어가 있습니다. '
                . '재시도·리다이렉트·인증 미들웨어는 발송을 여러 번 또는 다른 주소로 보낼 수 있습니다. 필요하면 프록시·CA는 BizgoClient의 '
                . 'proxy/caInfo 옵션을 쓰고, 그래도 이 클라이언트를 써야 하면 위험을 이해한 뒤 trustHttpClient: true로 명시하세요.',
            );
        }
    }

    public function name(): string
    {
        return 'guzzle';
    }

    public function send(HttpRequest $request): HttpResponse
    {
        $sink = new CappedSink();
        try {
            $response = $this->client->send(new Request($request->method, $request->url, $request->headers, $request->body), [
                RequestOptions::ALLOW_REDIRECTS => false,
                RequestOptions::AUTH => null,
                RequestOptions::COOKIES => false,
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::DECODE_CONTENT => true,
                RequestOptions::SINK => $sink,
            ]);
        } catch (\Throwable $e) {
            if ($sink->exceeded) {
                throw new InvalidResponseException('응답 본문이 너무 큽니다 (최대 16MiB)');
            }
            // Guzzle messages contain the URL (and its query string): report the class only
            if (str_contains($e->getMessage(), 'cURL error 28') || stripos(ResponseBody::shortName($e), 'timeout') !== false) {
                throw new TimeoutException('요청 시간이 초과됐습니다 (' . ResponseBody::shortName($e) . ')');
            }
            throw new ConnectionException('서버에 연결하지 못했습니다 (' . ResponseBody::shortName($e) . ')');
        }
        if ($sink->exceeded) {
            throw new InvalidResponseException('응답 본문이 너무 큽니다 (최대 16MiB)', $response->getStatusCode());
        }

        return ResponseBody::read($response);
    }

    private static function stockStack(HandlerStack $stack): bool
    {
        try {
            $entries = (new \ReflectionProperty(HandlerStack::class, 'stack'))->getValue($stack);
            $base = (new \ReflectionProperty(HandlerStack::class, 'handler'))->getValue($stack);
        } catch (\ReflectionException) {
            return false;
        }
        if (!is_array($entries)) {
            return false;
        }
        $names = [];
        foreach ($entries as $entry) {
            $names[] = is_array($entry) ? ($entry[1] ?? null) : null;
        }
        if ($names !== self::STOCK_MIDDLEWARE) {
            return false;
        }
        // the transport at the bottom must be one of Guzzle's own handlers (curl, stream, proxy, mock)
        if ($base instanceof \Closure) {
            $scope = (new \ReflectionFunction($base))->getClosureScopeClass(); // Proxy::wrapSync() etc.

            return $scope !== null && str_starts_with($scope->getName(), 'GuzzleHttp\\Handler\\');
        }

        return is_object($base) && str_starts_with($base::class, 'GuzzleHttp\\Handler\\');
    }
}
