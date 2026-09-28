<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * A request failed validation before anything was sent.
 *
 * The message and {@see self::$errors} contain field paths and reasons only, never the input values
 * (they can be phone numbers or message text).
 */
final class ValidationException extends \InvalidArgumentException implements BizgoException
{
    /**
     * @param list<array{path: string, reason: string}> $errors field path (for example `messageFlow[0].sms.text`) and reason
     */
    public function __construct(
        public readonly array $errors,
    ) {
        $parts = [];
        foreach ($errors as $error) {
            $parts[] = $error['path'] === '' ? $error['reason'] : $error['path'] . ': ' . $error['reason'];
        }
        parent::__construct('요청 검증 실패: ' . implode('; ', $parts));
    }

    /** A single error. */
    public static function of(string $path, string $reason): self
    {
        return new self([['path' => $path, 'reason' => $reason]]);
    }

    /** The same errors with `$prefix` prepended to every path (used for nested models). */
    public function withPrefix(string $prefix): self
    {
        if ($prefix === '') {
            return $this;
        }
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[] = ['path' => self::join($prefix, $error['path']), 'reason' => $error['reason']];
        }

        return new self($errors);
    }

    /** @internal */
    public static function join(string $prefix, string $path): string
    {
        if ($prefix === '') {
            return $path;
        }
        if ($path === '') {
            return $prefix;
        }

        return str_starts_with($path, '[') ? $prefix . $path : $prefix . '.' . $path;
    }

    /**
     * Message and errors (field paths and reasons, never values) for var_dump()/print_r(); the stack trace is left out.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['message' => $this->getMessage(), 'errors' => $this->errors];
    }
}
