<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Exception\ConfigurationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\TimeoutException;
use Bizgo\Internal\Secret;

/**
 * The default transport, built on ext-curl.
 *
 * - Redirects are never followed (`CURLOPT_FOLLOWLOCATION` off, `MAXREDIRS 0`), so the `Authorization` header and
 *   request bodies never leave the configured base URL. No HTTP authentication or cookie options are set.
 * - TLS peer and host verification are always on; there is no option to turn them off. A CA bundle (`caInfo`) or
 *   directory (`caPath`) can be given for a company CA, and a proxy (`proxy`) for outbound proxies.
 * - Only http(s) is allowed (plain http only reaches this point for localhost, see {@see \Bizgo\ClientConfig}).
 * - The response body is capped at {@see HttpSender::MAX_RESPONSE_BYTES} after decompression.
 * - Errors are reported with the cURL error number and its generic description only: `curl_error()` can
 *   contain the host or URL, and the URL can contain phone numbers in the query string.
 * - One cURL handle is reused per client (`curl_reset()` between requests keeps the connection cache), so bulk
 *   sends reuse the TLS connection.
 *
 * Proxies can also be set with the standard `https_proxy` / `no_proxy` environment variables that libcurl reads.
 *
 * @internal
 */
final class CurlSender implements HttpSender
{
    private const PROXY_SCHEMES = ['http', 'https', 'socks4', 'socks4a', 'socks5', 'socks5h'];

    private ?\CurlHandle $handle = null;

    /** The proxy URL (may hold credentials), in a holder that no dump or export shows. */
    private readonly ?Secret $proxy;

    public function __construct(
        private readonly float $timeout,
        private readonly float $connectTimeout,
        #[\SensitiveParameter]
        ?string $proxy = null,
        private readonly ?string $caInfo = null,
        private readonly ?string $caPath = null,
    ) {
        $this->proxy = $proxy === null ? null : new Secret($proxy);
    }

    /**
     * Validates a proxy URL.
     *
     * @throws ConfigurationException
     */
    public static function proxy(#[\SensitiveParameter] ?string $proxy): ?string
    {
        if ($proxy === null) {
            return null;
        }
        $parts = parse_url($proxy);
        $scheme = is_array($parts) ? strtolower($parts['scheme'] ?? '') : '';
        if (
            !is_array($parts) || ($parts['host'] ?? '') === '' || !in_array($scheme, self::PROXY_SCHEMES, true)
            || preg_match('/[\s\x00-\x1F\x7F]/', $proxy) === 1
        ) {
            throw new ConfigurationException('proxy는 http://host:port, https://, socks5h:// 형식이어야 합니다');
        }

        return $proxy;
    }

    /**
     * Validates a CA file or directory (the error names the option, not the path).
     *
     * @throws ConfigurationException
     */
    public static function file(string $option, ?string $path, bool $directory): ?string
    {
        if ($path === null) {
            return null;
        }
        set_error_handler(static fn (): bool => true);
        try {
            $ok = $path !== '' && !str_contains($path, "\0") && preg_match('~^[a-z][a-z0-9+.-]*://~i', $path) !== 1
                && ($directory ? is_dir($path) : is_file($path)) && is_readable($path);
        } finally {
            restore_error_handler();
        }
        if (!$ok) {
            throw new ConfigurationException("{$option}: 읽을 수 있는 로컬 " . ($directory ? '디렉터리' : '파일') . '이어야 합니다');
        }

        return $path;
    }

    public function name(): string
    {
        $info = curl_version();
        $version = is_array($info) ? ($info['version'] ?? null) : null;

        return 'curl/' . (is_string($version) ? $version : 'unknown');
    }

    public function send(HttpRequest $request): HttpResponse
    {
        if ($this->handle === null) {
            $handle = curl_init();
            if ($handle === false) {
                throw new ConnectionException('cURL을 초기화하지 못했습니다');
            }
            $this->handle = $handle;
        } else {
            curl_reset($this->handle); // clears the options, keeps the connection cache
        }
        $handle = $this->handle;
        $body = '';
        $tooLarge = false;
        /** @var array<string, string> $headers */
        $headers = [];
        $headerLines = ['Expect:']; // no "100-continue" round trip for larger bodies
        foreach ($request->headers as $name => $value) {
            $headerLines[] = $name . ': ' . $value;
        }
        $options = [
            CURLOPT_URL => $request->url,
            CURLOPT_CUSTOMREQUEST => $request->method,
            CURLOPT_HTTPHEADER => $headerLines,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT_MS => (int) ceil($this->connectTimeout * 1000),
            CURLOPT_TIMEOUT_MS => (int) ceil($this->timeout * 1000),
            CURLOPT_NOSIGNAL => true,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_WRITEFUNCTION => static function (mixed $ch, string $chunk) use (&$body, &$tooLarge): int {
                if (strlen($body) + strlen($chunk) > self::MAX_RESPONSE_BYTES) {
                    $tooLarge = true;

                    return 0; // aborts the transfer
                }
                $body .= $chunk;

                return strlen($chunk);
            },
            CURLOPT_HEADERFUNCTION => static function (mixed $ch, string $line) use (&$headers): int {
                if (str_starts_with($line, 'HTTP/')) {
                    $headers = []; // a new response (for example after 100 Continue)
                } elseif (str_contains($line, ':')) {
                    [$name, $value] = explode(':', $line, 2);
                    $headers[strtolower(trim($name))] = trim($value);
                }

                return strlen($line);
            },
        ];
        $proxy = $this->proxy?->reveal();
        if ($proxy !== null && $proxy !== '') {
            $options[CURLOPT_PROXY] = $proxy;
        }
        if ($this->caInfo !== null && $this->caInfo !== '') {
            $options[CURLOPT_CAINFO] = $this->caInfo;
        }
        if ($this->caPath !== null && $this->caPath !== '') {
            $options[CURLOPT_CAPATH] = $this->caPath;
        }
        if ($request->body !== null) {
            // a string (never an array) so cURL does not interpret "@file" values
            $options[CURLOPT_POSTFIELDS] = $request->body;
        }
        if (!curl_setopt_array($handle, $options)) {
            throw new ConnectionException('cURL 옵션을 설정하지 못했습니다');
        }
        curl_exec($handle);
        $errno = curl_errno($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        if ($tooLarge) {
            throw new InvalidResponseException('응답 본문이 너무 큽니다 (최대 16MiB)', $status > 0 ? $status : null);
        }
        if ($errno === CURLE_OPERATION_TIMEOUTED) {
            throw new TimeoutException('요청 시간이 초과됐습니다 (cURL error 28)');
        }
        if ($errno !== 0) {
            throw new ConnectionException(sprintf('서버에 연결하지 못했습니다 (cURL error %d: %s)', $errno, curl_strerror($errno) ?? ''));
        }

        return new HttpResponse($status, $headers, $body);
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return [
            'proxy' => $this->proxy === null ? null : '[hidden]',
            'caInfo' => $this->caInfo !== null,
            'caPath' => $this->caPath !== null,
        ];
    }
}
