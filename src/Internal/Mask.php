<?php

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * Log-safe views of models and results (SDK-DESIGN.md §12.11), used by their `__debugInfo()` and `__toString()`.
 *
 * - Phone number fields (`to`, `from`, `phoneNumber`, `callback`, ...): the middle is masked (`010****0000`); short
 *   numbers keep the first 3 characters and at least half is masked (`158****0`), 7 characters or fewer are all masked.
 * - Message content and free text (`content`, `text`, `comment`, `*Message`, ...): only the length is shown.
 * - Personal data (`userName`, `nickname`, `email`, addresses): the first character only.
 *
 * Only the representation changes; the properties keep the real values. `var_export()` and `serialize()` do not use
 * these methods, so do not log their output.
 *
 * @internal
 */
final class Mask
{
    /** API names of fields that hold phone numbers. */
    public const PHONE_FIELDS = [
        'to', 'from', 'phoneNumber', 'phoneNumbers', 'phone_number', 'originator', 'callback', 'dialPhoneNumber',
        'telNumber', 'tel', 'unsubscribePhoneNumber', 'mdn', 'originCID',
    ];

    /** API names of free-text fields: only the length is shown (also any field ending in Message/Content/Text/Comment). */
    public const CONTENT_FIELDS = [
        'content', 'contents', 'text', 'comment', 'comments', 'message', 'extra', 'extraField', 'chatExtra', 'title',
        'additionalContent', 'reportText', 'certResult', 'unsubscribeAuthNumber', 'token', 'body',
    ];

    /** API names of fields about a person: the first character only. */
    public const PERSON_FIELDS = ['userName', 'nickname', 'nickName', 'email', 'roadAddress', 'detailAddress', 'personalInfo'];

    private const CONTENT_SUFFIXES = ['Message', 'Content', 'Text', 'Comment'];

    private function __construct()
    {
    }

    /**
     * `01000000000` -> `010****0000`, `15880000` -> `158****0`, `1234567` -> `*******`.
     */
    public static function phone(#[\SensitiveParameter] string $value): string
    {
        $chars = mb_str_split($value, 1, 'UTF-8');
        $n = count($chars);
        if ($n === 0) {
            return '';
        }
        if ($n <= 7) {
            return str_repeat('*', $n);
        }
        if ($n >= 11) {
            return implode('', array_slice($chars, 0, 3)) . str_repeat('*', $n - 7) . implode('', array_slice($chars, -4));
        }
        $masked = intdiv($n + 1, 2);

        return implode('', array_slice($chars, 0, 3)) . str_repeat('*', $masked) . implode('', array_slice($chars, 3 + $masked));
    }

    /**
     * Masked copy of the properties of a model, for `__debugInfo()`.
     *
     * @param array<array-key, mixed> $fields
     *
     * @return array<string, mixed>
     */
    public static function fields(#[\SensitiveParameter] array $fields): array
    {
        $out = [];
        foreach ($fields as $name => $value) {
            $out[(string) $name] = self::value((string) $name, $value);
        }

        return $out;
    }

    /**
     * `Name{field=value, ...}` with masked values; unset (null) fields are left out.
     *
     * @param array<array-key, mixed> $fields
     */
    public static function describe(string $class, #[\SensitiveParameter] array $fields): string
    {
        $parts = [];
        foreach ($fields as $name => $value) {
            if ($value === null || $value === []) {
                continue;
            }
            $parts[] = $name . '=' . self::text(self::value((string) $name, $value));
        }

        return $class . '{' . implode(', ', $parts) . '}';
    }

    private static function value(string $name, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }
        if (in_array($name, self::PHONE_FIELDS, true)) {
            return self::phones($value);
        }
        if (is_array($value) && self::isSubstitution($name)) {
            // substitution values are personal data (names, amounts): keys and value lengths only
            $out = [];
            foreach ($value as $key => $item) {
                $out[$key] = self::size($item);
            }

            return $out;
        }
        if (in_array($name, self::PERSON_FIELDS, true)) {
            return is_string($value) ? self::person($value) : self::size($value);
        }
        if (self::isContent($name)) {
            return self::size($value);
        }
        if (is_array($value)) {
            $out = [];
            foreach ($value as $key => $item) {
                $out[$key] = is_string($key) ? self::value($key, $item) : self::value($name, $item);
            }

            return $out;
        }

        return $value; // nested models mask themselves in var_dump()/string conversion
    }

    private static function phones(mixed $value): mixed
    {
        if (is_string($value)) {
            return self::phone($value);
        }
        if (is_array($value)) {
            return array_map(self::phones(...), $value);
        }

        return is_scalar($value) ? self::phone((string) $value) : '[hidden]';
    }

    private static function person(string $value): string
    {
        $chars = mb_str_split($value, 1, 'UTF-8');

        return $chars === [] ? '' : $chars[0] . str_repeat('*', max(count($chars) - 1, 2));
    }

    private static function size(mixed $value): string
    {
        if (is_string($value)) {
            return '(' . mb_strlen($value, 'UTF-8') . '자)';
        }
        if (is_array($value)) {
            return '(' . count($value) . '개)';
        }

        return '(hidden)';
    }

    /** Substitution maps: `replaceWords`, `templateParams` and the brand message `*Variable` maps. */
    private static function isSubstitution(string $name): bool
    {
        return in_array($name, ['replaceWords', 'templateParams', 'variables'], true)
            || str_ends_with($name, 'Variable') || str_ends_with($name, 'Variables');
    }

    private static function isContent(string $name): bool
    {
        if (in_array($name, self::CONTENT_FIELDS, true)) {
            return true;
        }
        foreach (self::CONTENT_SUFFIXES as $suffix) {
            if (str_ends_with($name, $suffix) && $name !== $suffix) {
                return true;
            }
        }

        return false;
    }

    private static function text(mixed $value): string
    {
        return match (true) {
            is_string($value) => $value,
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value), is_float($value) => (string) $value,
            $value instanceof \Stringable => (string) $value,
            is_array($value) => '[' . implode(', ', array_map(self::text(...), $value)) . ']',
            is_object($value) => $value::class,
            default => '',
        };
    }
}
