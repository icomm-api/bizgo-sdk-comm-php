<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\ClientConfig;
use Bizgo\Environment;
use Bizgo\Exception\ApiException;
use Bizgo\Exception\AuthenticationException;
use Bizgo\Exception\BadRequestException;
use Bizgo\Exception\BizgoException;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InternalServerException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\NotFoundException;
use Bizgo\Exception\PermissionDeniedException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\TimeoutException;
use Bizgo\Tests\Support\NetworkError;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\RequestInterface;

final class ErrorsAndSecurityTest extends TestCase
{
    public function testGatewayAuthFailureWithoutData(): void
    {
        // observed on sandbox: invalid key -> HTTP 401, common.authCode=A401, no data
        $this->http->push(self::reply(['common' => ['authCode' => 'A401', 'authResult' => 'Unauthorized', 'infobankTrId' => 'TR-1']], 401));
        try {
            $this->client->messages->statistics('20260101');
            self::fail('expected an AuthenticationException');
        } catch (AuthenticationException $e) {
            self::assertSame([401, 'A401', 'gateway', 'TR-1'], [$e->httpStatus, $e->errorCode, $e->layer, $e->trackingId]);
            self::assertSame('HTTP 401 | gateway code=A401 | Unauthorized | infobankTrId=TR-1', $e->getMessage());
            self::assertNull($e->description); // gateway codes are not described with the service table
            self::assertStringNotContainsString(self::API_KEY, $e->getMessage());
            self::assertStringNotContainsString(self::API_KEY, print_r($e, true));
            self::assertStringNotContainsString(self::API_KEY, $e->getTraceAsString());
        }
        self::assertSame(1, $this->http->count()); // 401 is not retried
    }

    public function testServiceErrorCodeOnHttp200UsesDocumentedStatus(): void
    {
        $this->http->push(self::reply(self::envelope(code: 'A306')));
        try {
            $this->client->messages->statistics('20260101');
            self::fail('expected a BadRequestException');
        } catch (BadRequestException $e) {
            self::assertSame('service', $e->layer);
            self::assertSame(200, $e->httpStatus);
            self::assertSame('유효하지 않거나 비어있는 필드 (필드명 : to)', $e->description);
            self::assertSame('HTTP 200 | service code=A306 | Failed | 유효하지 않거나 비어있는 필드 (필드명 : to) | infobankTrId=TR-TEST', $e->getMessage());
        }
    }

    public function testSameCodeMeansDifferentThingsPerLayer(): void
    {
        $this->http->push(self::reply(self::envelope(code: 'A401'), 400)); // service A401 = invalid paymentCode
        try {
            $this->client->messages->statistics('20260101');
            self::fail('expected a BadRequestException');
        } catch (ApiException $e) {
            self::assertInstanceOf(BadRequestException::class, $e);
        }
        $this->http->push(self::reply(['common' => ['authCode' => 'A401', 'authResult' => 'Unauthorized']], 401));
        $this->expectException(AuthenticationException::class);
        $this->client->messages->statistics('20260101');
    }

    /** @return iterable<string, array{array<string, mixed>, int, class-string<ApiException>}> */
    public static function codeMappings(): iterable
    {
        yield 'gateway A403' => [['common' => ['authCode' => 'A403', 'authResult' => 'Forbidden']], 403, PermissionDeniedException::class];
        yield 'gateway A404' => [['common' => ['authCode' => 'A404', 'authResult' => 'Not Found']], 404, NotFoundException::class];
        yield 'gateway A400' => [['common' => ['authCode' => 'A400', 'authResult' => 'Bad Request']], 400, BadRequestException::class];
        yield 'service A100' => [self::envelope(code: 'A100'), 200, AuthenticationException::class];
        yield 'service A110' => [self::envelope(code: 'A110'), 200, PermissionDeniedException::class];
        yield 'service A002' => [self::envelope(code: 'A002'), 200, AuthenticationException::class];
        yield 'service A910 (documented 500)' => [self::envelope(code: 'A910'), 200, InternalServerException::class];
        yield 'unknown service code on 409' => [self::envelope(code: 'Z999'), 409, BadRequestException::class];
        yield 'no code, HTTP 403' => [['common' => ['authCode' => 'A000']], 403, PermissionDeniedException::class];
    }

    /**
     * @param array<string, mixed> $body
     * @param class-string<ApiException> $class
     */
    #[DataProvider('codeMappings')]
    public function testCodeMapping(array $body, int $status, string $class): void
    {
        $this->http->always(self::reply($body, $status));
        $this->expectException($class);
        $this->client->messages->statistics('20260101');
    }

    public function testServerErrorWithHtmlBodyIsRetriedThenRaised(): void
    {
        $this->http->always(self::reply('<html>bad gateway</html>', 502));
        try {
            $this->client->messages->statistics('20260101');
            self::fail('expected an InternalServerException');
        } catch (InternalServerException $e) {
            self::assertNull($e->errorCode);
            self::assertStringContainsString('JSON이 아닙니다', $e->getMessage());
        }
        self::assertSame(3, $this->http->count());
    }

    public function testSuccessStatusWithNonJsonBody(): void
    {
        $this->http->push(self::reply('ok'));
        $this->expectException(InvalidResponseException::class);
        $this->client->messages->statistics('20260101');
    }

    public function testRedirectsAreNotFollowed(): void
    {
        $this->http->push(new Response(302, ['Location' => 'https://attacker.example/steal']));
        try {
            $this->client->messages->statistics('20260101');
            self::fail('expected an InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertSame(302, $e->httpStatus);
        }
        self::assertSame(1, $this->http->count());
    }

    public function testConnectionErrorHidesRequestDetails(): void
    {
        $this->http->always(static fn (RequestInterface $request) => throw new NetworkError($request));
        try {
            $this->client->messages->moHistory('2026-04-23T14:11:01+09:00', from: '01000001234');
            self::fail('expected a ConnectionException');
        } catch (ConnectionException $e) {
            self::assertNotInstanceOf(TimeoutException::class, $e);
            self::assertStringNotContainsString('01000001234', $e->getMessage());
            self::assertStringNotContainsString('sandbox-mars', $e->getMessage());
            self::assertNull($e->getPrevious()); // the client exception (with the full URL) is not chained
        }
        self::assertSame(3, $this->http->count()); // lookups are retried
    }

    public function testDebugLogHasNoKeyQueryOrBody(): void
    {
        $this->http->push(self::reply(self::envelope(['messages' => [], 'hasNext' => false])));
        $this->client->messages->moHistory('2026-04-23T14:11:01+09:00', from: '01000001234');
        $this->http->push(self::reply(self::accepted('A000')));
        $this->client->send->sms(to: '01000001234', from: '01000000000', text: '비밀 본문');
        $text = $this->logger->text();
        self::assertMatchesRegularExpression('~^debug GET /api/comm/v1/message/history/mo -> 200 \(\d+ ms, attempt 1\)$~m', $text);
        self::assertMatchesRegularExpression('~^debug POST /api/comm/v1/send/omni -> 200 \(\d+ ms, attempt 1\)$~m', $text);
        self::assertStringNotContainsString(self::API_KEY, $text);
        self::assertStringNotContainsString('01000001234', $text);
        self::assertStringNotContainsString('비밀', $text);
        self::assertStringNotContainsString('occurredTime', $text);
    }

    public function testClientRepresentationsHideKey(): void
    {
        $client = new BizgoClient(apiKey: self::API_KEY);
        self::assertStringNotContainsString(self::API_KEY, (string) $client);
        self::assertStringNotContainsString(self::API_KEY, print_r($client, true));
        self::assertStringNotContainsString(self::API_KEY, json_encode($client, JSON_THROW_ON_ERROR));
        ob_start();
        var_dump($client);
        self::assertStringNotContainsString(self::API_KEY, (string) ob_get_clean());
        $config = ClientConfig::create(self::API_KEY);
        self::assertStringNotContainsString(self::API_KEY, print_r($config, true) . $config);
        self::assertSame('BizgoClient(baseUrl=https://mars.ibapi.kr)', (string) $client);
    }

    public function testClientCannotBeSerialized(): void
    {
        $this->expectException(\LogicException::class);
        serialize(new BizgoClient(apiKey: self::API_KEY));
    }

    public function testKeyIsNotInStackTraces(): void
    {
        try {
            new BizgoClient(apiKey: self::API_KEY, baseUrl: 'http://mars.ibapi.kr');
            self::fail('expected a ConfigurationException');
        } catch (ConfigurationException $e) {
            self::assertStringNotContainsString(self::API_KEY, $e->getTraceAsString());
            self::assertStringNotContainsString(self::API_KEY, print_r($e->getTrace(), true));
        }
    }

    /** @return iterable<string, array{string}> */
    public static function badKeys(): iterable
    {
        yield 'Bearer prefix' => ['Bearer abc'];
        yield 'ApiKey prefix' => ['ApiKey abc'];
        yield 'blank' => ['  '];
        yield 'newline (header injection)' => ["abc\r\nX-Evil: 1"];
    }

    #[DataProvider('badKeys')]
    public function testPrefixedOrBlankKeysAreRejected(string $key): void
    {
        $this->expectException(ConfigurationException::class);
        new BizgoClient(apiKey: $key);
    }

    public function testApiKeyFromEnvironment(): void
    {
        putenv('BIZGO_API_KEY=' . self::API_KEY);
        try {
            self::assertSame(Environment::Production->value, (new BizgoClient())->baseUrl());
            self::assertSame(Environment::Sandbox->value, (new BizgoClient(environment: 'sandbox'))->baseUrl());
        } finally {
            putenv('BIZGO_API_KEY');
        }
    }

    public function testMissingKeyIsAConfigurationError(): void
    {
        putenv('BIZGO_API_KEY');
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('BIZGO_API_KEY');
        new BizgoClient();
    }

    /** @return iterable<string, array{string}> */
    public static function badBaseUrls(): iterable
    {
        yield 'plain http' => ['http://mars.ibapi.kr'];
        yield 'ftp' => ['ftp://example.com'];
        yield 'no host' => ['https://'];
        yield 'credentials' => ['https://user:pass@mars.ibapi.kr'];
        yield 'query' => ['https://mars.ibapi.kr?x=1'];
        yield 'http look-alike host' => ['http://localhost.attacker.example'];
    }

    #[DataProvider('badBaseUrls')]
    public function testInsecureBaseUrlsAreRejected(string $url): void
    {
        $this->expectException(ConfigurationException::class);
        new BizgoClient(apiKey: self::API_KEY, baseUrl: $url);
    }

    public function testLocalhostHttpIsAllowedForMockServers(): void
    {
        self::assertSame('http://localhost:4010', (new BizgoClient(apiKey: self::API_KEY, baseUrl: 'http://localhost:4010/'))->baseUrl());
        self::assertSame('http://127.0.0.1:4010', (new BizgoClient(apiKey: self::API_KEY, baseUrl: 'http://127.0.0.1:4010'))->baseUrl());
    }

    public function testInvalidSettingsAreRejected(): void
    {
        $this->expectException(ConfigurationException::class);
        new BizgoClient(apiKey: self::API_KEY, maxRetries: -1);
    }

    public function testUnknownEnvironmentIsRejected(): void
    {
        $this->expectException(ConfigurationException::class);
        new BizgoClient(apiKey: self::API_KEY, environment: 'staging');
    }

    public function testPsr18ClientNeedsFactories(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('requestFactory');
        new BizgoClient(apiKey: self::API_KEY, httpClient: $this->http, trustHttpClient: true);
    }

    public function testApiExceptionDebugInfoHidesBody(): void
    {
        $this->http->push(self::reply(self::envelope(['destinations' => [['to' => '01000001234']]], code: 'A306')));
        try {
            $this->client->messages->statistics('20260101');
            self::fail('expected a BadRequestException');
        } catch (BadRequestException $e) {
            self::assertIsArray($e->body); // still available to the caller
            ob_start();
            var_dump($e);
            $dump = (string) ob_get_clean();
            self::assertStringNotContainsString('01000001234', $dump);
            self::assertStringNotContainsString('01000001234', print_r($e, true));
        }
    }

    public function testOversizedResponseIsRejected(): void
    {
        $this->http->push(new Response(200, [], str_repeat(' ', 16 * 1024 * 1024 + 1)));
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('너무 큽니다');
        $this->client->messages->statistics('20260101');
    }

    public function testEveryExceptionImplementsTheMarkerInterface(): void
    {
        $this->http->push(self::reply(['data' => ['code' => 'A020', 'result' => 'Ratelimit']], 429));
        $client = $this->newClient(maxRetries: 0);
        try {
            $client->messages->statistics('20260101');
            self::fail('expected a RateLimitException');
        } catch (BizgoException $e) {
            self::assertInstanceOf(RateLimitException::class, $e);
        }
    }

    public function testPsr18TransportUsesGivenFactories(): void
    {
        $factory = new Psr17Factory();
        $this->http->push(self::reply(self::envelope(['statistics' => []])));
        $client = new BizgoClient(apiKey: self::API_KEY, httpClient: $this->http, requestFactory: $factory, streamFactory: $factory, trustHttpClient: true);
        self::assertSame([], $client->messages->statistics('20260101'));
        self::assertMatchesRegularExpression('~^bizgo-sdk-comm-php/[0-9.]+ php/[0-9.]+ \([a-z]+; [a-z0-9]+\)$~', $this->http->last()->getHeaderLine('User-Agent'));
        self::assertSame('bizgo-sdk-comm-php/' . \Bizgo\Version::SDK, $this->http->last()->getHeaderLine('X-Bizgo-Client'));
    }
}
