<?php

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * Holds a secret (API key, webhook secret, proxy credentials) so that no dump shows it: the value lives in a closure
 * (var_export() prints closures without their variables), and var_dump()/print_r() use {@see self::__debugInfo()}.
 * Serializing is refused.
 *
 * @internal
 */
final class Secret
{
    /** @var \Closure(): string */
    private readonly \Closure $value;

    public function __construct(#[\SensitiveParameter] string $value)
    {
        $this->value = static fn (): string => $value;
    }

    public function reveal(): string
    {
        return ($this->value)();
    }

    /** @return array<string, string> */
    public function __debugInfo(): array
    {
        return ['value' => '[hidden]'];
    }

    public function __toString(): string
    {
        return '[hidden]';
    }

    /** @return never */
    public function __serialize(): array
    {
        throw new \LogicException('a secret cannot be serialized');
    }

    /** @param array<array-key, mixed> $data */
    public function __unserialize(array $data): void
    {
        throw new \LogicException('a secret cannot be unserialized');
    }
}
