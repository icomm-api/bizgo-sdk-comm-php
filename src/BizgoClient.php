<?php

declare(strict_types=1);

namespace Bizgo;

use Bizgo\Exception\ConfigurationException;
use Bizgo\Http\CurlSender;
use Bizgo\Http\GuzzleSender;
use Bizgo\Http\HttpSender;
use Bizgo\Http\Psr18Sender;
use Bizgo\Http\Transport;
use Bizgo\Internal\RateLimiter;
use Bizgo\Observability\Hook;
use Bizgo\Resource\Files;
use Bizgo\Resource\Messages;
use Bizgo\Resource\Reports;
use Bizgo\Resource\Send;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\Psr18Client;

/**
 * Bizgo Communication API client.
 *
 * ```php
 * use Bizgo\BizgoClient;
 * use Bizgo\Environment;
 *
 * $client = new BizgoClient(environment: Environment::Sandbox); // API key from BIZGO_API_KEY
 * $result = $client->send->sms(to: '01000000000', from: '01000000000', text: '인증번호는 123456 입니다.');
 * $result->msgKeys(); // accepted message keys
 * $result->failed();  // recipients rejected at acceptance
 * $result->duplicates(); // recipients already accepted earlier with the same idempotencyKey (A301)
 *
 * $client->alimtalk->templates->list(senderKey: 'SENDER_KEY_EXAMPLE'); // every other API: generated from the spec
 * ```
 *
 * Resources: `send`, `files`, `reports`, `messages` (hand-written convenience methods plus generated ones) and the
 * generated `reservations`, `insights`, `kakao`, `alimtalk`, `brandMessage`, `rcs`, `counsel`.
 *
 * The key and settings belong to this instance only; nothing is static or global. The key is never part of
 * `var_dump()`, `print_r()`, string conversion or stack traces, and the client cannot be serialized.
 */
final class BizgoClient
{
    use GeneratedResources;

    /** Message sending. */
    public readonly Send $send;

    /** File uploads (MMS, RCS, brand message, AlimTalk template images). */
    public readonly Files $files;

    /** Delivery reports (polling, inquiry). */
    public readonly Reports $reports;

    /** Message status, history and statistics. */
    public readonly Messages $messages;

    private readonly ClientConfig $config;

    private readonly Transport $transport;

    /**
     * @param string|null $apiKey integrated API key from the console. Defaults to the `BIZGO_API_KEY` environment
     *        variable. Pass the key only, without `Bearer`/`ApiKey` prefixes or surrounding whitespace. Never hard-code it.
     * @param Environment|string $environment `Environment::Production` (default, real sends) or `Environment::Sandbox`
     * @param string|null $baseUrl override the server URL (must be https, except localhost for tests)
     * @param float $timeout total seconds per attempt (default 30), for the built-in cURL transport
     * @param float $connectTimeout connect seconds (default 5), for the built-in cURL transport
     * @param int $maxRetries retries for rate limits and transient errors (default 2). Sends are retried after a
     *        timeout only when `idempotencyKey` is set.
     * @param ClientInterface|null $httpClient your own HTTP client instead of the built-in cURL transport. Prefer
     *        `$proxy`/`$caInfo`/`$caPath` for proxies and a company CA. Guzzle: sent with `allow_redirects: false`,
     *        `auth: null`, `cookies: false`; refused when its cURL options redirect/authenticate/keep cookies or its
     *        handler stack has non-stock middleware. Symfony `Psr18Client`: `max_redirects: 0`, auth cleared, a
     *        `RetryableHttpClient` is refused. Any other PSR-18 client is refused unless `$trustHttpClient` is true.
     *        The client's TLS settings apply as they are. Needs `$requestFactory`/`$streamFactory` (PSR-17) unless
     *        the client implements them itself (not needed for Guzzle).
     * @param LoggerInterface|null $logger receives one debug line per attempt: `METHOD path-template -> status (ms,
     *        attempt n)`. Keys, bodies, query strings, path values and phone numbers are never logged.
     * @param (\Closure(float): void)|null $sleep waits between retries and for the rate limiter (seconds); for tests.
     *        Default: usleep().
     * @param AppInfo|null $appInfo your application's name and version, added to the User-Agent (`app/<name>-<version>`)
     * @param RateLimit|null $rateLimit client-side limits (default 200 messages/s for sends, 5 requests/s otherwise);
     *        null turns them off. Per client instance and process only, see {@see RateLimit}.
     * @param list<Hook> $hooks request start/end callbacks for metrics and tracing, see {@see Hook}
     * @param (\Closure(): float)|null $clock monotonic seconds for the rate limiter and hook durations; for tests
     * @param HttpSender|null $sender internal: replaces the HTTP layer (used by {@see \Bizgo\Testing\FakeTransport})
     * @param string|null $proxy proxy URL for the built-in cURL transport (`http://proxy:3128`, `socks5h://...`;
     *        credentials in the URL are allowed and never logged). The `https_proxy` environment variable also works.
     * @param string|null $caInfo CA bundle file (PEM) for the built-in cURL transport, for example a company CA
     * @param string|null $caPath directory of CA certificates for the built-in cURL transport
     * @param bool $trustHttpClient accept a PSR-18 client the SDK cannot inspect (not Guzzle/Symfony). It must not
     *        redirect, retry or authenticate by itself; you accept the risk of duplicate sends and of requests (with
     *        the key and phone numbers) going to another host. Also skips the Guzzle middleware check.
     *
     * @throws ConfigurationException
     */
    public function __construct(
        #[\SensitiveParameter]
        ?string $apiKey = null,
        Environment|string $environment = Environment::Production,
        ?string $baseUrl = null,
        float $timeout = ClientConfig::DEFAULT_TIMEOUT,
        float $connectTimeout = ClientConfig::DEFAULT_CONNECT_TIMEOUT,
        int $maxRetries = ClientConfig::DEFAULT_MAX_RETRIES,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?LoggerInterface $logger = null,
        ?\Closure $sleep = null,
        ?AppInfo $appInfo = null,
        ?RateLimit $rateLimit = new RateLimit(),
        array $hooks = [],
        ?\Closure $clock = null,
        ?HttpSender $sender = null,
        #[\SensitiveParameter]
        ?string $proxy = null,
        ?string $caInfo = null,
        ?string $caPath = null,
        bool $trustHttpClient = false,
    ) {
        $this->config = ClientConfig::create($apiKey, $environment, $baseUrl, $timeout, $connectTimeout, $maxRetries);
        foreach ($hooks as $hook) {
            if (!$hook instanceof Hook) {
                throw new ConfigurationException('hooks에는 Bizgo\\Observability\\Hook 구현만 넣을 수 있습니다');
            }
        }
        $sleep ??= static function (float $seconds): void {
            usleep((int) round($seconds * 1_000_000));
        };
        $clock ??= static fn (): float => hrtime(true) / 1e9;
        $sender ??= self::sender($this->config, $httpClient, $requestFactory, $streamFactory, $trustHttpClient, $proxy, $caInfo, $caPath);
        $this->transport = new Transport(
            $this->config,
            $sender,
            $logger ?? new NullLogger(),
            $sleep,
            $clock,
            $rateLimit === null ? null : new RateLimiter($rateLimit, $clock, $sleep),
            array_values($hooks),
            $appInfo,
        );
        $this->send = new Send($this->transport);
        $this->files = new Files($this->transport);
        $this->reports = new Reports($this->transport);
        $this->messages = new Messages($this->transport);
        $this->initGeneratedResources($this->transport);
    }

    /** The server URL in use, for example `https://sandbox-mars.ibapi.kr`. */
    public function baseUrl(): string
    {
        return $this->config->baseUrl;
    }

    /** The `User-Agent` sent with every request (SDK, PHP, OS/architecture and your {@see AppInfo}). */
    public function userAgent(): string
    {
        return $this->transport->userAgent();
    }

    public function __toString(): string
    {
        return 'BizgoClient(baseUrl=' . $this->config->baseUrl . ')';
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return ['baseUrl' => $this->config->baseUrl, 'apiKey' => '[hidden]'];
    }

    /** @return never */
    public function __serialize(): array
    {
        throw new \LogicException('BizgoClient holds an API key and cannot be serialized');
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        throw new \LogicException('BizgoClient cannot be unserialized');
    }

    private static function sender(
        ClientConfig $config,
        ?ClientInterface $httpClient,
        ?RequestFactoryInterface $requestFactory,
        ?StreamFactoryInterface $streamFactory,
        bool $trusted,
        #[\SensitiveParameter]
        ?string $proxy,
        ?string $caInfo,
        ?string $caPath,
    ): HttpSender {
        if ($httpClient === null) {
            if (!extension_loaded('curl')) {
                throw new ConfigurationException('ext-curl이 필요합니다. 설치하거나 httpClient(PSR-18)를 넘기세요.');
            }

            return new CurlSender(
                $config->timeout,
                $config->connectTimeout,
                CurlSender::proxy($proxy),
                CurlSender::file('caInfo', $caInfo, false),
                CurlSender::file('caPath', $caPath, true),
            );
        }
        if ($proxy !== null || $caInfo !== null || $caPath !== null) {
            throw new ConfigurationException('proxy/caInfo/caPath는 내장 cURL 전송의 옵션입니다. httpClient를 넘겼다면 그 클라이언트에서 설정하세요.');
        }
        if ($httpClient instanceof GuzzleClientInterface) {
            GuzzleSender::inspect($httpClient, $trusted);

            return new GuzzleSender($httpClient);
        }
        if ($httpClient instanceof Psr18Client) {
            $httpClient = Psr18Sender::symfony($httpClient, $trusted);
        } elseif (!$trusted) {
            throw new ConfigurationException(
                'SDK가 설정을 점검할 수 없는 PSR-18 클라이언트는 기본적으로 받지 않습니다(리다이렉트·재시도·자체 인증이 발송을 다른 주소로 또는 여러 번 '
                . '보낼 수 있음). 프록시·사내 CA는 BizgoClient의 proxy/caInfo/caPath 옵션을 쓰세요. Guzzle과 Symfony HttpClient는 SDK가 안전한 '
                . '옵션을 강제합니다. 그래도 이 클라이언트를 써야 하면, 리다이렉트·재시도·자체 인증을 하지 않도록 설정했는지 확인하고 '
                . 'trustHttpClient: true로 명시하세요(중복 발송·데이터 유출 위험은 사용자 책임).',
            );
        }
        $requestFactory ??= $httpClient instanceof RequestFactoryInterface ? $httpClient : null;
        $streamFactory ??= $httpClient instanceof StreamFactoryInterface ? $httpClient : null;
        if ($requestFactory === null || $streamFactory === null) {
            throw new ConfigurationException(
                'httpClient를 쓰려면 requestFactory와 streamFactory(PSR-17)도 넘기세요. '
                . '예: $f = new \Nyholm\Psr7\Factory\Psr17Factory(); new BizgoClient(httpClient: $c, requestFactory: $f, streamFactory: $f)',
            );
        }

        return new Psr18Sender($httpClient, $requestFactory, $streamFactory);
    }
}
