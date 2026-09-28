<?php

declare(strict_types=1);

namespace Bizgo\Tests\Support;

use PHPUnit\Framework\Assert;
use Symfony\Component\Yaml\Yaml;

/**
 * Minimal valid values built from the spec (required fields only, placeholders only), used to call every operation.
 */
final class SpecSamples
{
    /** @var array<array-key, mixed>|null */
    private static ?array $spec = null;

    /** @return array<array-key, mixed> */
    public static function spec(): array
    {
        if (self::$spec === null) {
            $spec = Yaml::parseFile(__DIR__ . '/../../spec/openapi.yaml');
            self::$spec = is_array($spec) ? $spec : [];
        }

        return self::$spec;
    }

    /** A value inside nested arrays, or null. */
    public static function get(mixed $value, string|int ...$path): mixed
    {
        foreach ($path as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Like {@see self::get()}, asserting an array.
     *
     * @return array<array-key, mixed>
     */
    public static function arr(mixed $value, string|int ...$path): array
    {
        $found = self::get($value, ...$path);
        Assert::assertIsArray($found, implode('.', $path));

        return $found;
    }

    /** Like {@see self::get()}, asserting a string. */
    public static function str(mixed $value, string|int ...$path): string
    {
        $found = self::get($value, ...$path);
        Assert::assertIsString($found, implode('.', $path));

        return $found;
    }

    /**
     * The raw operation of the spec, with its path-level parameters merged in.
     *
     * @return array<array-key, mixed>
     */
    public static function operation(string $operationId): array
    {
        $paths = self::spec()['paths'] ?? [];
        Assert::assertIsArray($paths);
        foreach ($paths as $item) {
            Assert::assertIsArray($item);
            foreach ($item as $key => $op) {
                if ($key !== 'parameters' && is_array($op) && ($op['operationId'] ?? null) === $operationId) {
                    $common = is_array($item['parameters'] ?? null) ? $item['parameters'] : [];
                    $own = is_array($op['parameters'] ?? null) ? $op['parameters'] : [];
                    $op['parameters'] = array_map(self::resolveParameter(...), array_merge($common, $own));

                    return $op;
                }
            }
        }
        throw new \LogicException("unknown operation {$operationId}");
    }

    /** @return array<array-key, mixed> */
    private static function resolveParameter(mixed $parameter): array
    {
        Assert::assertIsArray($parameter);
        if (isset($parameter['$ref']) && is_string($parameter['$ref'])) {
            $name = substr($parameter['$ref'], strlen('#/components/parameters/'));
            return self::arr(self::spec(), 'components', 'parameters', $name);
        }

        return $parameter;
    }

    /** @return array<array-key, mixed> */
    public static function schema(string $name): array
    {
        return self::arr(self::spec(), 'components', 'schemas', $name);
    }

    /** A value that satisfies `$schema` (required fields only). */
    public static function value(mixed $schema, int $depth = 0): mixed
    {
        Assert::assertIsArray($schema);
        if ($depth > 12) {
            return null;
        }
        if (isset($schema['$ref']) && is_string($schema['$ref'])) {
            return self::value(self::schema(substr($schema['$ref'], strlen('#/components/schemas/'))), $depth + 1);
        }
        if (isset($schema['oneOf']) && is_array($schema['oneOf'])) {
            return self::value($schema['oneOf'][0], $depth + 1);
        }
        if (isset($schema['allOf']) && is_array($schema['allOf'])) {
            $out = [];
            $required = [];
            foreach ($schema['allOf'] as $part) {
                Assert::assertIsArray($part);
                if (isset($part['$ref']) || isset($part['properties'])) {
                    $value = self::value($part, $depth + 1);
                    $out = array_merge($out, is_array($value) ? $value : []);
                }
                foreach (is_array($part['required'] ?? null) ? $part['required'] : [] as $field) {
                    Assert::assertIsString($field);
                    $required[] = $field;
                }
            }
            foreach ($required as $field) {
                $out[$field] ??= 'X';
            }

            return $out;
        }
        $type = $schema['type'] ?? (isset($schema['properties']) ? 'object' : 'string');
        if (is_array($type)) {
            $type = array_values(array_filter($type, static fn (mixed $t): bool => $t !== 'null'))[0] ?? 'string';
        }

        return match ($type) {
            'object' => self::object($schema, $depth),
            'array' => self::items($schema, $depth),
            'integer' => is_int($schema['minimum'] ?? null) ? $schema['minimum'] : 1,
            'number' => 1.5,
            'boolean' => true,
            default => self::string($schema),
        };
    }

    /**
     * @param array<array-key, mixed> $schema
     *
     * @return array<string, mixed>
     */
    private static function object(array $schema, int $depth): array
    {
        $out = [];
        $properties = is_array($schema['properties'] ?? null) ? $schema['properties'] : [];
        foreach (is_array($schema['required'] ?? null) ? $schema['required'] : [] as $field) {
            Assert::assertIsString($field);
            $out[$field] = isset($properties[$field]) ? self::value($properties[$field], $depth + 1) : 'X';
        }

        return $out;
    }

    /**
     * @param array<array-key, mixed> $schema
     *
     * @return list<mixed>
     */
    private static function items(array $schema, int $depth): array
    {
        $count = max(is_int($schema['minItems'] ?? null) ? $schema['minItems'] : 1, 1);

        return array_fill(0, $count, self::value($schema['items'] ?? ['type' => 'string'], $depth + 1));
    }

    /** @param array<array-key, mixed> $schema */
    public static function string(array $schema): string
    {
        if (isset($schema['enum']) && is_array($schema['enum']) && is_string($schema['enum'][0] ?? null)) {
            return $schema['enum'][0];
        }
        $format = $schema['x-format'] ?? null;
        $candidates = match ($format) {
            'yyyy-MM-dd HH:mm:ss' => ['2026-01-01 10:00:00'],
            "yyyy-MM-dd'T'HH:mm:ss" => ['2026-01-01T10:00:00'],
            "yyyy-MM-dd'T'HH:mm:ssXXX" => ['2026-01-01T10:00:00+09:00'],
            'yyyyMMddHHmmss' => ['20260101100000'],
            'YYYYMMDD', 'yyyyMMdd' => ['20260101'],
            default => ['X', '1', '20260101', '01000000000', 'SENDER_KEY_EXAMPLE', 'https://example.com', 'Y', 'A', '2026-01-01 10:00:00'],
        };
        $min = is_int($schema['minLength'] ?? null) ? $schema['minLength'] : 0;
        $pattern = is_string($schema['pattern'] ?? null) ? '~' . str_replace('~', '\~', $schema['pattern']) . '~u' : null;
        foreach ($candidates as $candidate) {
            $candidate = str_pad($candidate, $min, 'X');
            if ($pattern === null || preg_match($pattern, $candidate) === 1) {
                return $candidate;
            }
        }

        return str_repeat('1', max($min, 1));
    }
}
