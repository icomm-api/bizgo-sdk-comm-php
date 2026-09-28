<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\NotFoundException;
use Bizgo\Exception\TimeoutException;
use Bizgo\Http\CurlSender;
use Bizgo\Http\Multipart;
use Bizgo\Http\HttpRequest;
use Bizgo\Http\Transport;
use Bizgo\Upload;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;

/**
 * The built-in cURL transport against PHP's built-in web server on 127.0.0.1 (offline).
 */
#[RequiresPhpExtension('curl')]
final class CurlSenderTest extends TestCase
{
    /** @var resource|null */
    private static $server = null;
    private const API_KEY = 'test-api-key-not-real';
    private static int $port = 0;

    public static function setUpBeforeClass(): void
    {
        self::$port = self::freePort();
        $ini = php_ini_loaded_file();
        $command = [PHP_BINARY];
        if (is_string($ini)) {
            array_push($command, '-c', $ini);
        }
        array_push($command, '-S', '127.0.0.1:' . self::$port, __DIR__ . '/Support/router.php');
        $null = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['file', $null, 'w'], 2 => ['file', $null, 'w']], $pipes);
        if (!is_resource($process)) {
            self::markTestSkipped('could not start the PHP built-in server');
        }
        self::$server = $process;
        for ($i = 0; $i < 100; $i++) {
            $socket = @fsockopen('127.0.0.1', self::$port, $errno, $errstr, 0.1);
            if (is_resource($socket)) {
                fclose($socket);

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
    }

    private static function freePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertIsResource($socket);
        $name = (string) stream_socket_get_name($socket, false);
        fclose($socket);

        return (int) substr($name, (int) strrpos($name, ':') + 1);
    }

    /**
     * One attempt through the built-in cURL sender, judged like the client does (Transport::parse).
     *
     * @param array<string, string|null> $query
     * @param array<string, mixed>|null $json
     *
     * @return array<array-key, mixed>
     */
    private function request(
        string $method,
        string $path,
        array $query = [],
        ?array $json = null,
        ?Multipart $multipart = null,
        float $timeout = 10.0,
        ?int $port = null,
    ): array {
        $headers = ['Authorization' => self::API_KEY, 'Accept' => 'application/json'];
        $body = null;
        if ($json !== null) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
            $body = json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } elseif ($multipart !== null) {
            $headers['Content-Type'] = $multipart->contentType();
            $body = $multipart->body;
        }
        $url = 'http://127.0.0.1:' . ($port ?? self::$port) . $path . ($query === [] ? '' : '?' . http_build_query($query));
        $method = $method === '' ? 'GET' : $method;

        return Transport::parse((new CurlSender($timeout, min($timeout, 5.0)))->send(new HttpRequest($method, $url, $headers, $body)));
    }

    /** @return array<array-key, mixed> */
    private static function data(mixed $body): array
    {
        self::assertIsArray($body);
        self::assertIsArray($body['data']);
        self::assertIsArray($body['data']['data']);

        return $body['data']['data'];
    }

    public function testJsonRequest(): void
    {
        $body = $this->request('POST', '/echo', ['from' => '01000000000', 'x' => null], json: ['a' => '한글']);
        $data = self::data($body);
        self::assertSame('POST', $data['method']);
        self::assertSame(self::API_KEY, $data['authorization']);
        self::assertSame('application/json; charset=utf-8', $data['contentType']);
        self::assertSame('from=01000000000', $data['query']);
        self::assertSame('{"a":"한글"}', $data['body']);
    }

    public function testMultipartUpload(): void
    {
        $form = Multipart::build('file', Upload::fromContents("\xff\xd8jpeg", 'a.jpg'), ['imageName' => 'banner', 'fileKey' => null]);
        $data = self::data($this->request('POST', '/echo', multipart: $form));
        self::assertSame(['file' => ['name' => 'a.jpg', 'type' => 'image/jpeg', 'size' => 6]], $data['files']);
        self::assertSame(['imageName' => 'banner'], $data['post']);
    }

    public function testRedirectsAreNotFollowed(): void
    {
        try {
            $this->request('GET', '/redirect');
            self::fail('expected an InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertSame(302, $e->httpStatus);
        }
    }

    public function testErrorStatusWithoutJson(): void
    {
        $this->expectException(NotFoundException::class);
        $this->request('GET', '/missing');
    }

    public function testResponseSizeIsCapped(): void
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('너무 큽니다');
        $this->request('GET', '/big');
    }

    /** L8: one cURL handle per client is reused (connection cache kept) and reset between requests. */
    public function testHandleIsReusedAndReset(): void
    {
        $sender = new CurlSender(10.0, 5.0);
        $url = 'http://127.0.0.1:' . self::$port . '/echo';
        $sender->send(new HttpRequest('POST', $url, ['Authorization' => self::API_KEY], '{"a":1}'));
        $handle = (new \ReflectionProperty(CurlSender::class, 'handle'))->getValue($sender);
        self::assertInstanceOf(\CurlHandle::class, $handle);
        $second = Transport::parse($sender->send(new HttpRequest('GET', $url, ['Authorization' => self::API_KEY])));
        self::assertSame($handle, (new \ReflectionProperty(CurlSender::class, 'handle'))->getValue($sender));
        $data = self::data($second);
        self::assertSame('GET', $data['method']);
        self::assertSame('', $data['body'], 'the previous POST body is not reused');
    }

    /** SDK-DESIGN.md §12.8: the 16 MiB cap counts the decompressed body (gzip bomb). */
    public function testDecompressedSizeIsCapped(): void
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('너무 큽니다');
        $this->request('GET', '/gzip');
    }

    public function testTimeout(): void
    {
        $this->expectException(TimeoutException::class);
        $this->request('GET', '/slow', timeout: 0.5);
    }

    public function testConnectionRefusedHidesTheUrl(): void
    {
        try {
            $this->request('GET', '/echo', ['from' => '01000001234'], port: self::freePort());
            self::fail('expected a ConnectionException');
        } catch (ConnectionException $e) {
            self::assertStringContainsString('cURL error', $e->getMessage());
            self::assertStringNotContainsString('01000001234', $e->getMessage());
            self::assertStringNotContainsString('127.0.0.1', $e->getMessage());
            self::assertNull($e->getPrevious());
        }
    }
}
