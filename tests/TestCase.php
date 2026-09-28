<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Environment;
use Bizgo\Tests\Support\MemoryLogger;
use Bizgo\Tests\Support\MockHttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public const API_KEY = 'test-api-key-not-real';
    public const BASE = 'https://sandbox-mars.ibapi.kr';

    protected MockHttpClient $http;
    protected MemoryLogger $logger;
    protected BizgoClient $client;

    /** @var list<float> requested retry delays (retries do not really sleep in tests) */
    protected array $sleeps = [];

    protected function setUp(): void
    {
        $this->http = new MockHttpClient();
        $this->logger = new MemoryLogger();
        $this->client = $this->newClient();
    }

    protected function newClient(int $maxRetries = 2): BizgoClient
    {
        $factory = new Psr17Factory();

        return new BizgoClient(
            apiKey: self::API_KEY,
            environment: Environment::Sandbox,
            maxRetries: $maxRetries,
            httpClient: $this->http,
            requestFactory: $factory,
            streamFactory: $factory,
            logger: $this->logger,
            rateLimit: null,
            trustHttpClient: true, // the offline mock client of the tests
            sleep: function (float $seconds): void {
                $this->sleeps[] = $seconds;
            },
        );
    }

    /**
     * The documented `{common, data}` envelope.
     *
     * @return array<string, mixed>
     */
    public static function envelope(mixed $data = null, string $code = 'A000', ?string $ref = null): array
    {
        $service = ['code' => $code, 'result' => $code === 'A000' ? 'Success' : 'Failed'];
        if ($data !== null) {
            $service['data'] = $data;
        }
        if ($ref !== null) {
            $service['ref'] = $ref;
        }

        return [
            'common' => ['authCode' => 'A000', 'authResult' => 'Success', 'infobankTrId' => 'TR-TEST'],
            'data' => $service,
        ];
    }

    /**
     * @param array<array-key, mixed>|string $body
     * @param array<string, string> $headers
     */
    public static function reply(array|string $body, int $status = 200, array $headers = []): ResponseInterface
    {
        return MockHttpClient::json($body, $status, $headers);
    }

    /** @return array<string, mixed> */
    public static function accepted(string ...$codes): array
    {
        $destinations = [];
        foreach ($codes as $i => $code) {
            $destinations[] = ['to' => '01000000000', 'msgKey' => sprintf('KEY%03d', $i), 'code' => $code, 'result' => 'r'];
        }

        return self::envelope(['destinations' => $destinations], ref: 'ref-1');
    }
}
