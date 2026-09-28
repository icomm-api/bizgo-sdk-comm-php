<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Exception\AuthenticationException;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InternalServerException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Result\SendResult;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * Injected HTTP clients cannot redirect, retry or authenticate behind the SDK's back (SDK-DESIGN.md §12.6), against
 * PHP's built-in web server on 127.0.0.1 (offline). The router logs every request, so a redirect to `/stolen` or a
 * second POST would be visible.
 */
#[RequiresPhpExtension('curl')]
final class HttpClientPolicyTest extends TestCase
{
    private const KEY = 'test-api-key-not-real';

    /** @var resource|null */
    private static $server = null;
    private static int $port = 0;
    private static string $log = '';

    public static function setUpBeforeClass(): void
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertIsResource($socket);
        $name = (string) stream_socket_get_name($socket, false);
        fclose($socket);
        self::$port = (int) substr($name, (int) strrpos($name, ':') + 1);
        self::$log = (string) tempnam(sys_get_temp_dir(), 'bizgo-policy');
        $ini = php_ini_loaded_file();
        $command = [PHP_BINARY];
        if (is_string($ini)) {
            array_push($command, '-c', $ini);
        }
        array_push($command, '-S', '127.0.0.1:' . self::$port, __DIR__ . '/Support/policy_router.php');
        $null = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
        $env = getenv() + ['BIZGO_POLICY_LOG' => self::$log];
        $env['BIZGO_POLICY_LOG'] = self::$log;
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['file', $null, 'w'], 2 => ['file', $null, 'w']], $pipes, null, $env);
        if (!is_resource($process)) {
            self::markTestSkipped('could not start the PHP built-in server');
        }
        self::$server = $process;
        for ($i = 0; $i < 100; $i++) {
            $probe = @fsockopen('127.0.0.1', self::$port, $errno, $errstr, 0.1);
            if (is_resource($probe)) {
                fclose($probe);

                return;
            }
            usleep(50_000);
        }
        self::markTestSkipped('the PHP built-in server did not start');
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$server)) {
            proc_terminate(self::$server);
            proc_close(self::$server);
        }
        @unlink(self::$log);
    }

    protected function setUp(): void
    {
        file_put_contents(self::$log, '');
    }

    /** @return list<array{path: string, method: string, auth: string}> */
    private static function requests(): array
    {
        $out = [];
        foreach (explode("\n", (string) file_get_contents(self::$log)) as $line) {
            $entry = json_decode($line, true);
            if (is_array($entry) && is_string($entry['path'] ?? null) && is_string($entry['method'] ?? null) && is_string($entry['auth'] ?? null)) {
                $out[] = ['path' => $entry['path'], 'method' => $entry['method'], 'auth' => $entry['auth']];
            }
        }

        return $out;
    }

    private static function client(string $mode, ClientInterface $http, bool $trust = false): BizgoClient
    {
        $factory = new Psr17Factory();

        return new BizgoClient(
            apiKey: self::KEY,
            baseUrl: 'http://127.0.0.1:' . self::$port . '/' . $mode,
            maxRetries: 0,
            httpClient: $http,
            requestFactory: $factory,
            streamFactory: $factory,
            rateLimit: null,
            trustHttpClient: $trust,
        );
    }

    private static function send(BizgoClient $client): SendResult
    {
        return $client->send->sms(to: '01000000000', from: '01000000000', text: 'hello');
    }

    /** No request ever reaches another path, and nothing is sent twice. */
    private static function assertOnlyOneRequest(string $mode): void
    {
        $requests = self::requests();
        self::assertCount(1, $requests, 'exactly one request');
        self::assertStringStartsWith('/' . $mode . '/', $requests[0]['path']);
        self::assertSame(self::KEY, $requests[0]['auth']);
    }

    // ---------------------------------------------------------------- Guzzle

    public function testGuzzleNeverFollowsRedirectsEvenWhenConfiguredTo(): void
    {
        foreach ([new Guzzle(), new Guzzle([RequestOptions::ALLOW_REDIRECTS => ['max' => 5, 'strict' => true]])] as $guzzle) {
            file_put_contents(self::$log, '');
            try {
                self::send(self::client('redirect', $guzzle));
                self::fail('expected InvalidResponseException');
            } catch (InvalidResponseException $e) {
                self::assertSame(307, $e->httpStatus);
                self::assertStringContainsString('접수됐을 수 있으니', $e->getMessage());
            }
            self::assertOnlyOneRequest('redirect');
        }
    }

    public function testGuzzleOwnAuthIsIgnoredAndNotRetried(): void
    {
        $this->expectException(AuthenticationException::class);
        try {
            self::send(self::client('basic', new Guzzle([RequestOptions::AUTH => ['user', 'pass'], RequestOptions::COOKIES => true])));
        } finally {
            self::assertOnlyOneRequest('basic');
        }
    }

    public function testGuzzleCurlOptionsThatRedirectOrAuthenticateAreRefused(): void
    {
        $options = [
            [CURLOPT_FOLLOWLOCATION => true, CURLOPT_POSTREDIR => 7],
            [CURLOPT_HTTPAUTH => CURLAUTH_ANY, CURLOPT_USERPWD => 'user:pass'],
            [CURLOPT_COOKIEFILE => ''],
        ];
        foreach ($options as $curl) {
            try {
                self::client('ok', new Guzzle(['curl' => $curl]), trust: true);
                self::fail('expected ConfigurationException');
            } catch (ConfigurationException $e) {
                self::assertStringNotContainsString('pass', $e->getMessage());
            }
        }
        self::assertSame([], self::requests());
    }

    public function testGuzzleWithCustomMiddlewareNeedsExplicitTrust(): void
    {
        $retrying = HandlerStack::create();
        $retrying->push(Middleware::retry(static fn (int $n, RequestInterface $r, ?ResponseInterface $res): bool => $n < 2 && $res?->getStatusCode() === 503));
        try {
            self::client('unavailable', new Guzzle(['handler' => $retrying]));
            self::fail('expected ConfigurationException');
        } catch (ConfigurationException $e) {
            self::assertStringContainsString('trustHttpClient', $e->getMessage());
        }
        self::assertSame([], self::requests(), 'refused before anything was sent');
    }

    public function testGuzzleGzipBombIsCappedWhileStreaming(): void
    {
        $before = memory_get_peak_usage(true);
        try {
            self::send(self::client('gzip', new Guzzle()));
            self::fail('expected InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertStringContainsString('16MiB', $e->getMessage());
        }
        self::assertLessThan(64 * 1024 * 1024, memory_get_peak_usage(true) - $before);
    }

    public function testGuzzleWorksNormally(): void
    {
        self::assertSame(['K001'], self::send(self::client('ok', new Guzzle()))->msgKeys());
        self::assertOnlyOneRequest('ok');
    }

    // ---------------------------------------------------------------- Symfony

    public function testSymfonyNeverFollowsRedirects(): void
    {
        foreach ([new Psr18Client(), new Psr18Client(HttpClient::create(['max_redirects' => 5]))] as $symfony) {
            file_put_contents(self::$log, '');
            try {
                self::send(self::client('redirect', $symfony));
                self::fail('expected InvalidResponseException');
            } catch (InvalidResponseException $e) {
                self::assertSame(307, $e->httpStatus);
            }
            self::assertOnlyOneRequest('redirect');
        }
    }

    public function testSymfonyOwnAuthIsCleared(): void
    {
        $this->expectException(AuthenticationException::class);
        try {
            self::send(self::client('basic', new Psr18Client(HttpClient::create(['auth_basic' => 'user:pass']))));
        } finally {
            self::assertOnlyOneRequest('basic');
        }
    }

    public function testSymfonyRetryableClientIsRefused(): void
    {
        $retrying = new Psr18Client(new RetryableHttpClient(HttpClient::create(), new GenericRetryStrategy([401, 503], 0), 2));
        $this->expectException(ConfigurationException::class);
        self::client('unavailable', $retrying);
    }

    // ---------------------------------------------------------------- other PSR-18 clients

    /** @return array<string, array{ClientInterface}> */
    public static function uninspectableClients(): array
    {
        $inner = new Guzzle();

        return [
            'redirecting' => [new class ($inner) implements ClientInterface {
                public function __construct(private readonly Guzzle $inner)
                {
                }

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    $response = $this->inner->send($request, [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::HTTP_ERRORS => false]);

                    return $response;
                }
            }],
            'retrying' => [new class ($inner) implements ClientInterface {
                public function __construct(private readonly Guzzle $inner)
                {
                }

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    $response = $this->inner->send($request, [RequestOptions::HTTP_ERRORS => false]);

                    return $response->getStatusCode() >= 500 ? $this->inner->send($request, [RequestOptions::HTTP_ERRORS => false]) : $response;
                }
            }],
            'authenticating' => [new class ($inner) implements ClientInterface {
                public function __construct(private readonly Guzzle $inner)
                {
                }

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    $response = $this->inner->send($request, [RequestOptions::HTTP_ERRORS => false]);
                    if ($response->getStatusCode() === 401) {
                        return $this->inner->send($request->withHeader('Authorization', 'Basic dTpw'), [RequestOptions::HTTP_ERRORS => false]);
                    }

                    return $response;
                }
            }],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('uninspectableClients')]
    public function testUninspectableClientsAreRefusedUnlessTrusted(ClientInterface $http): void
    {
        foreach (['redirect', 'unavailable', 'basic'] as $mode) {
            try {
                self::client($mode, $http);
                self::fail('expected ConfigurationException');
            } catch (ConfigurationException $e) {
                self::assertStringContainsString('trustHttpClient', $e->getMessage());
            }
        }
        self::assertSame([], self::requests(), 'nothing is sent by a refused client');
    }

    public function testTrustedClientIsTheCallersResponsibility(): void
    {
        $http = self::uninspectableClients()['retrying'][0];
        try {
            self::send(self::client('unavailable', $http, trust: true));
            self::fail('expected InternalServerException');
        } catch (InternalServerException) {
            self::assertCount(2, self::requests(), 'documented risk: a trusted client that retries sends twice');
        }
    }

    /** M3: any exception from a client (not only PSR-18 ones) becomes a ConnectionException without the URL. */
    public function testClientExceptionsNeverLeakTheUrl(): void
    {
        $failing = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new \RuntimeException('POST ' . $request->getUri() . ' failed');
            }
        };
        try {
            $client = self::client('ok', $failing, trust: true);
            $client->messages->moHistory(occurredTime: '2026-09-01T00:00:00+09:00', from: '01000000000');
            self::fail('expected ConnectionException');
        } catch (ConnectionException $e) {
            self::assertStringNotContainsString('127.0.0.1', $e->getMessage());
            self::assertStringNotContainsString('01000000000', $e->getMessage());
            self::assertStringContainsString('RuntimeException', $e->getMessage());
            self::assertNull($e->getPrevious());
        }
    }

    public function testBuiltInTransportOptionsForProxyAndCa(): void
    {
        foreach (['ftp://proxy', 'proxy:3128', "http://proxy\r\n:1"] as $proxy) {
            try {
                new BizgoClient(apiKey: self::KEY, proxy: $proxy);
                self::fail('expected ConfigurationException');
            } catch (ConfigurationException $e) {
                self::assertStringNotContainsString('proxy:3128', $e->getMessage());
            }
        }
        $client = new BizgoClient(apiKey: self::KEY, proxy: 'http://user:secret@proxy.example:3128', caInfo: __FILE__);
        self::assertStringNotContainsString('secret', var_export($client, true) . print_r($client, true));
        $this->expectException(ConfigurationException::class);
        new BizgoClient(apiKey: self::KEY, caInfo: __DIR__ . '/no-such-ca.pem');
    }

    public function testBuiltInOptionsAreNotMixedWithAnInjectedClient(): void
    {
        $this->expectException(ConfigurationException::class);
        new BizgoClient(apiKey: self::KEY, httpClient: new Guzzle(), proxy: 'http://proxy.example:3128');
    }
}
