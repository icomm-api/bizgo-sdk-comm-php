<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\ValidationException;
use Bizgo\Model\ChannelMessage;
use Bizgo\Model\MessageFlowItem;

/**
 * Constraint checks used by the generated request model constructors.
 *
 * Every check returns a (possibly empty) list of errors with the field path and a reason.
 * Reasons never contain the checked value: it can be a phone number or message text.
 *
 * @internal
 *
 * @phpstan-type Error array{path: string, reason: string}
 */
final class Check
{
    private function __construct()
    {
    }

    /**
     * Throws a {@see ValidationException} with every error from the given checks.
     *
     * @param list<Error> ...$results
     *
     * @throws ValidationException
     */
    public static function all(array ...$results): void
    {
        $errors = array_merge(...$results);
        if ($errors !== []) {
            throw new ValidationException(array_values($errors));
        }
    }

    /** @return list<Error> */
    public static function maxLength(string $path, #[\SensitiveParameter] ?string $value, int $max): array
    {
        if ($value === null) {
            return [];
        }
        if (!mb_check_encoding($value, 'UTF-8')) {
            return [self::error($path, 'UTF-8 문자열이 아닙니다')];
        }
        $length = mb_strlen($value, 'UTF-8');

        return $length > $max ? [self::error($path, "최대 {$max}자인데 {$length}자입니다")] : [];
    }

    /** @return list<Error> */
    public static function minLength(string $path, #[\SensitiveParameter] ?string $value, int $min): array
    {
        if ($value === null) {
            return [];
        }
        $length = mb_strlen($value, 'UTF-8');

        return $length < $min ? [self::error($path, "최소 {$min}자여야 합니다")] : [];
    }

    /**
     * Byte limit (`x-max-bytes`). For `x-charset: EUC-KR` the value is counted in CP949, a superset of EUC-KR
     * that uses the same 2 bytes per Hangul syllable, the way carriers count it. Characters CP949 cannot
     * represent (emoji, for example) are rejected because the carrier rejects them.
     *
     * @return list<Error>
     */
    public static function maxBytes(string $path, #[\SensitiveParameter] ?string $value, int $max, ?string $charset): array
    {
        if ($value === null) {
            return [];
        }
        $size = Text::byteLength($value, $charset);
        if ($size === null) {
            return [self::error($path, Text::isUtf8($value)
                ? 'EUC-KR로 표현할 수 없는 문자가 있습니다 (이모지 등은 문자메시지에 쓸 수 없습니다)'
                : 'UTF-8 문자열이 아닙니다')];
        }

        return $size > $max ? [self::error($path, "최대 {$max}byte인데 {$size}byte입니다")] : [];
    }

    /**
     * @param string $pattern ECMA-262 style pattern from the spec, for example `^[0-9]+$`
     *
     * @return list<Error>
     */
    public static function pattern(string $path, #[\SensitiveParameter] ?string $value, string $pattern): array
    {
        if ($value === null) {
            return [];
        }
        $regex = '~' . str_replace('~', '\~', $pattern) . '~u';

        return preg_match($regex, $value) === 1 ? [] : [self::error($path, "형식이 맞지 않습니다 ({$pattern})")];
    }

    /**
     * @param list<string> $allowed
     *
     * @return list<Error>
     */
    public static function enum(string $path, #[\SensitiveParameter] ?string $value, array $allowed): array
    {
        if ($value === null || in_array($value, $allowed, true)) {
            return [];
        }

        return [self::error($path, '허용되는 값이 아닙니다 (' . implode(', ', $allowed) . ')')];
    }

    /** @return list<Error> */
    public static function range(
        string $path,
        #[\SensitiveParameter]
        int|float|null $value,
        int|float|null $min,
        int|float|null $max,
    ): array {
        if ($value === null) {
            return [];
        }
        if (is_float($value) && !is_finite($value)) {
            return [self::error($path, '유한한 숫자여야 합니다')];
        }
        if ($min !== null && $value < $min) {
            return [self::error($path, "{$min} 이상이어야 합니다")];
        }
        if ($max !== null && $value > $max) {
            return [self::error($path, "{$max} 이하여야 합니다")];
        }

        return [];
    }

    /**
     * @param array<array-key, mixed>|null $value
     *
     * @return list<Error>
     */
    public static function items(string $path, #[\SensitiveParameter] ?array $value, ?int $min, ?int $max): array
    {
        if ($value === null) {
            return [];
        }
        $count = count($value);
        if ($min !== null && $count < $min) {
            return [self::error($path, "항목이 최소 {$min}개 있어야 합니다")];
        }
        if ($max !== null && $count > $max) {
            return [self::error($path, "항목은 최대 {$max}개인데 {$count}개입니다")];
        }

        return [];
    }

    /**
     * Checks that `$value` is a list whose items are instances of one of `$classes`.
     *
     * @param array<array-key, mixed>|null $value
     * @param list<class-string> $classes
     *
     * @return list<Error>
     */
    public static function listOf(string $path, #[\SensitiveParameter] ?array $value, array $classes): array
    {
        if ($value === null) {
            return [];
        }
        if (!array_is_list($value)) {
            return [self::error($path, '목록(list)이어야 합니다')];
        }
        $names = implode(' 또는 ', array_map(static fn (string $c): string => self::shortName($c), $classes));
        $errors = [];
        foreach ($value as $i => $item) {
            $ok = false;
            foreach ($classes as $class) {
                if ($item instanceof $class) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                $errors[] = self::error("{$path}[{$i}]", "{$names} 객체여야 합니다");
            }
        }

        return $errors;
    }

    /**
     * @param array<array-key, mixed>|null $value
     *
     * @return list<Error>
     */
    public static function stringList(string $path, #[\SensitiveParameter] ?array $value): array
    {
        if ($value === null) {
            return [];
        }
        if (!array_is_list($value)) {
            return [self::error($path, '목록(list)이어야 합니다')];
        }
        $errors = [];
        foreach ($value as $i => $item) {
            if (!is_string($item)) {
                $errors[] = self::error("{$path}[{$i}]", '문자열이어야 합니다');
            }
        }

        return $errors;
    }

    /**
     * @param array<array-key, mixed>|null $value
     *
     * @return list<Error>
     */
    public static function stringMap(string $path, #[\SensitiveParameter] ?array $value): array
    {
        if ($value === null) {
            return [];
        }
        $errors = [];
        foreach ($value as $key => $item) {
            if (!is_string($item)) {
                $errors[] = self::error("{$path}.{$key}", '문자열이어야 합니다');
            }
        }

        return $errors;
    }

    /**
     * A list of free-form objects (`type: array, items: {type: object}` without properties).
     *
     * @param array<array-key, mixed>|null $value
     *
     * @return list<Error>
     */
    public static function objectList(string $path, #[\SensitiveParameter] ?array $value): array
    {
        if ($value === null) {
            return [];
        }
        if (!array_is_list($value)) {
            return [self::error($path, '목록(list)이어야 합니다')];
        }
        $errors = [];
        foreach ($value as $i => $item) {
            if (!is_array($item) || ($item !== [] && array_is_list($item))) {
                $errors[] = self::error("{$path}[{$i}]", '객체(키-값 배열)여야 합니다');
            }
        }

        return $errors;
    }

    /**
     * Channels allowed in a message flow that accepts only some of them (reservations have no `navertalk`).
     *
     * @param array<array-key, mixed>|null $value
     * @param list<string> $allowed
     *
     * @return list<Error>
     */
    public static function channels(string $path, #[\SensitiveParameter] ?array $value, array $allowed): array
    {
        $errors = [];
        foreach ($value ?? [] as $i => $item) {
            $key = $item instanceof ChannelMessage || $item instanceof MessageFlowItem ? $item->channelKey() : null;
            if ($key !== null && !in_array($key, $allowed, true)) {
                $errors[] = self::error("{$path}[{$i}]", "여기에 쓸 수 없는 채널입니다 ({$key}); 허용: " . implode(', ', $allowed));
            }
        }

        return $errors;
    }

    /** @return Error */
    public static function error(string $path, string $reason): array
    {
        return ['path' => $path, 'reason' => $reason];
    }

    private static function shortName(string $class): string
    {
        $pos = strrpos($class, '\\');

        return $pos === false ? $class : substr($class, $pos + 1);
    }
}
