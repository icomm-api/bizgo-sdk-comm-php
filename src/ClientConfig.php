<?php

declare(strict_types=1);

namespace Bizgo;

use Bizgo\Exception\ConfigurationException;
use Bizgo\Internal\Secret;

/**
 * Validated, immutable client settings. Each {@see BizgoClient} has its own; nothing is shared or global.
 *
 * The API key is private, marked `#[\SensitiveParameter]` (so it is not in stack traces) and left out of
 * `var_dump()`, `print_r()` and string conversion. Serializing the config is refused.
 */
final readonly class ClientConfig
{
    public const API_KEY_ENV = 'BIZGO_API_KEY';
    public const DEFAULT_TIMEOUT = 30.0;
    public const DEFAULT_CONNECT_TIMEOUT = 5.0;
    public const DEFAULT_MAX_RETRIES = 2;

    private const LOCAL_HOSTS = ['localhost', '127.0.0.1', '[::1]', '::1'];

    /** The key, in a holder that no dump or export shows. */
    private Secret $apiKey;

    private function __construct(
        #[\SensitiveParameter]
        string $apiKey,
        /** Server URL without a trailing slash, for example `https://mars.ibapi.kr`. */
        public string $baseUrl,
        /** Total timeout of one attempt, in seconds. */
        public float $timeout,
        /** Connect timeout, in seconds. */
        public float $connectTimeout,
        /** Retries after the first attempt. */
        public int $maxRetries,
    ) {
        $this->apiKey = new Secret($apiKey);
    }

    /**
     * @throws ConfigurationException
     */
    public static function create(
        #[\SensitiveParameter]
        ?string $apiKey = null,
        Environment|string $environment = Environment::Production,
        ?string $baseUrl = null,
        float $timeout = self::DEFAULT_TIMEOUT,
        float $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT,
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
    ): self {
        if (!is_finite($timeout) || $timeout <= 0 || !is_finite($connectTimeout) || $connectTimeout <= 0) {
            throw new ConfigurationException('timeout과 connectTimeout은 0보다 커야 합니다');
        }
        if ($maxRetries < 0 || $maxRetries > 10) {
            throw new ConfigurationException('maxRetries는 0~10입니다');
        }

        return new self(
            self::resolveApiKey($apiKey),
            self::resolveBaseUrl($environment, $baseUrl),
            $timeout,
            min($connectTimeout, $timeout),
            $maxRetries,
        );
    }

    /**
     * The API key, for the `Authorization` header only.
     *
     * @internal
     */
    public function apiKey(): string
    {
        return $this->apiKey->reveal();
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return [
            'baseUrl' => $this->baseUrl,
            'timeout' => $this->timeout,
            'connectTimeout' => $this->connectTimeout,
            'maxRetries' => $this->maxRetries,
            'apiKey' => '[hidden]',
        ];
    }

    public function __toString(): string
    {
        return 'ClientConfig(baseUrl=' . $this->baseUrl . ')';
    }

    /** @return never */
    public function __serialize(): array
    {
        throw new \LogicException('ClientConfig holds an API key and cannot be serialized');
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        throw new \LogicException('ClientConfig cannot be unserialized');
    }

    private static function resolveApiKey(#[\SensitiveParameter] ?string $apiKey): string
    {
        if ($apiKey === null) {
            $env = getenv(self::API_KEY_ENV);
            $apiKey = is_string($env) ? $env : '';
        }
        if ($apiKey === '') {
            throw new ConfigurationException(
                'API Key가 없습니다. apiKey 인자로 넘기거나 환경변수 ' . self::API_KEY_ENV . '에 설정하세요. '
                . '키는 코드에 직접 쓰지 말고 환경변수나 시크릿 저장소에서 읽어 오세요.',
            );
        }
        // printable ASCII without spaces only; surrounding whitespace or a newline is reported, never trimmed silently
        if (preg_match('/^[\x21-\x7e]+$/D', $apiKey) !== 1) {
            throw new ConfigurationException(
                "API Key에 공백·줄바꿈·제어 문자나 ASCII가 아닌 문자가 있습니다. 앞뒤 공백을 지우고, 'Bearer '나 'ApiKey ' 같은 "
                . '접두어 없이 키만 넣으세요.',
            );
        }

        return $apiKey;
    }

    private static function resolveBaseUrl(Environment|string $environment, ?string $baseUrl): string
    {
        if ($baseUrl === null) {
            $env = $environment instanceof Environment ? $environment : self::environment($environment);

            return $env->value;
        }
        $url = rtrim(trim($baseUrl), '/');
        $parts = parse_url($url);
        $scheme = is_array($parts) ? strtolower($parts['scheme'] ?? '') : '';
        $host = is_array($parts) ? strtolower($parts['host'] ?? '') : '';
        if ($host === '' || !in_array($scheme, ['https', 'http'], true)) {
            throw new ConfigurationException('baseUrl은 https://host 형식이어야 합니다');
        }
        if ($scheme !== 'https' && !in_array($host, self::LOCAL_HOSTS, true)) {
            // The API key travels in a header, so it must never be sent over plain HTTP.
            throw new ConfigurationException("baseUrl은 https여야 합니다: {$scheme}://{$host}");
        }
        if (isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            throw new ConfigurationException('baseUrl에는 사용자 정보, 쿼리, 프래그먼트를 넣을 수 없습니다');
        }

        return $url;
    }

    private static function environment(string $name): Environment
    {
        foreach (Environment::cases() as $case) {
            if (strcasecmp($case->name, $name) === 0 || $case->value === rtrim($name, '/')) {
                return $case;
            }
        }
        throw new ConfigurationException('environment는 Environment::Production 또는 Environment::Sandbox입니다');
    }
}
