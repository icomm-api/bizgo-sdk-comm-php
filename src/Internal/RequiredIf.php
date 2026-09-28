<?php

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * Conditionally required fields (`x-sdk-required-if` in the spec), checked before anything is sent.
 *
 * The rules are generated into {@see RequiredIfRules} from the spec; nothing here knows a field name. Request model
 * constructors call {@see self::check()} with their own array form:
 * - rules of the model itself (`required` and relative `requiredPaths`), evaluated against the model;
 * - for a request body root (for example `SendOmniRequest`), every rule below it with `$.` paths, evaluated against
 *   the whole body, because those paths (for example `$.destinations[].replaceWords`) need the root.
 *
 * Errors name field paths and the condition (field, operator and the spec value), never an input value.
 *
 * @internal
 *
 * @phpstan-import-type Error from Check
 * @phpstan-import-type Condition from RequiredIfRules
 */
final class RequiredIf
{
    private const OPERATORS = ['equals' => '==', 'notEquals' => '!=', 'in' => 'in', 'notIn' => 'not in'];

    private function __construct()
    {
    }

    /**
     * Every rule violation of the model `$schema` whose array form is `$data`.
     *
     * @param array<array-key, mixed> $data the model's toArray()
     *
     * @return list<Error>
     */
    public static function check(string $schema, #[\SensitiveParameter] array $data): array
    {
        $errors = [];
        foreach (RequiredIfRules::MODELS[$schema] ?? [] as $rule) {
            if (!self::matches($rule['when'], $data)) {
                continue;
            }
            $reason = self::reason('', $rule['when']);
            foreach ($rule['required'] as $field) {
                if (($data[$field] ?? null) === null) {
                    $errors[] = Check::error($field, $reason);
                }
            }
            foreach ($rule['paths'] as $path) {
                foreach (self::missing($data, self::segments($path), '') as $missing) {
                    $errors[] = Check::error($missing, $reason);
                }
            }
        }
        foreach (RequiredIfRules::ROOTS[$schema] ?? [] as $entry) {
            foreach (self::locate($data, self::segments($entry['at']), '') as [$at, $object]) {
                if (!self::matches($entry['when'], $object)) {
                    continue;
                }
                $reason = self::reason($at, $entry['when']);
                foreach ($entry['paths'] as $path) {
                    foreach (self::missing($data, self::segments($path), '') as $missing) {
                        $errors[] = Check::error($missing, $reason);
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @param Condition $when
     * @param array<array-key, mixed> $object
     */
    private static function matches(array $when, #[\SensitiveParameter] array $object): bool
    {
        $value = self::text($object[$when['field']] ?? null);
        $in = $value !== null && in_array($value, $when['values'], true);

        return match ($when['op']) {
            'equals', 'in' => $in,
            'notEquals', 'notIn' => !$in,
        };
    }

    /** Values are compared as strings; null (missing) and arrays never equal a spec value. */
    private static function text(#[\SensitiveParameter] mixed $value): ?string
    {
        return match (true) {
            is_string($value) => $value,
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value), is_float($value) => (string) $value,
            default => null,
        };
    }

    /** @param Condition $when */
    private static function reason(string $at, array $when): string
    {
        $values = $when['values'];
        $shown = in_array($when['op'], ['in', 'notIn'], true) ? '[' . implode(', ', $values) . ']' : $values[0];
        $field = $at === '' ? $when['field'] : $at . '.' . $when['field'];

        return "필수 필드입니다 ({$field} " . self::OPERATORS[$when['op']] . " {$shown}일 때)";
    }

    /** @return list<string> */
    private static function segments(string $path): array
    {
        return $path === '' ? [] : explode('.', $path);
    }

    /**
     * The objects at a location pattern (`messageFlow[].alimtalk`), with their concrete paths.
     *
     * @param list<string> $segments
     *
     * @return list<array{0: string, 1: array<array-key, mixed>}>
     */
    private static function locate(#[\SensitiveParameter] mixed $node, array $segments, string $path): array
    {
        if (!is_array($node)) {
            return [];
        }
        if ($segments === []) {
            return [[$path, $node]];
        }
        $segment = array_shift($segments);
        if (str_ends_with($segment, '[]')) {
            $name = substr($segment, 0, -2);
            $list = $node[$name] ?? null;
            if (!is_array($list) || !array_is_list($list)) {
                return [];
            }
            $out = [];
            foreach ($list as $i => $item) {
                $out = array_merge($out, self::locate($item, $segments, self::join($path, $name) . "[{$i}]"));
            }

            return $out;
        }

        return self::locate($node[$segment] ?? null, $segments, self::join($path, $segment));
    }

    /**
     * Concrete paths of a required path (`destinations[].replaceWords`, `attachment.file.fileName`) that are missing
     * or null. `name[]` means every element: a missing or empty list has no element to check. A missing object in
     * the middle makes the whole path missing.
     *
     * @param list<string> $segments
     *
     * @return list<string>
     */
    private static function missing(#[\SensitiveParameter] mixed $node, array $segments, string $path): array
    {
        if ($segments === []) {
            return $node === null ? [$path] : [];
        }
        if (!is_array($node)) {
            // a missing parent: every element of a list below it is vacuously present
            foreach ($segments as $segment) {
                if (str_ends_with($segment, '[]')) {
                    return [];
                }
            }

            return [self::join($path, implode('.', $segments))];
        }
        $segment = array_shift($segments);
        if (str_ends_with($segment, '[]')) {
            $name = substr($segment, 0, -2);
            $list = $node[$name] ?? null;
            if (!is_array($list) || !array_is_list($list)) {
                return [];
            }
            $out = [];
            foreach ($list as $i => $item) {
                $out = array_merge($out, self::missing($item, $segments, self::join($path, $name) . "[{$i}]"));
            }

            return $out;
        }

        return self::missing($node[$segment] ?? null, $segments, self::join($path, $segment));
    }

    private static function join(string $prefix, string $name): string
    {
        return $prefix === '' ? $name : $prefix . '.' . $name;
    }
}
