<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Model\ResponseModel;

/**
 * Reads a decoded JSON object for a generated response model's `fromArray()`.
 *
 * Lenient on purpose so a server change never breaks parsing: missing fields are null, lossless scalar
 * conversions are accepted (`1` for a string field, `"10"` for an integer field) and unknown keys are kept.
 * Only an incompatible type (an object where a string is documented, ...) raises an
 * {@see InvalidResponseException}, whose message names the field path but not the value.
 *
 * @internal
 */
final class ResponseData
{
    /**
     * @param array<array-key, mixed> $data
     * @param list<string> $known API field names of the model
     */
    public function __construct(
        #[\SensitiveParameter]
        private readonly array $data,
        private readonly string $path,
        private readonly array $known,
    ) {
        if ($data !== [] && array_is_list($data)) {
            throw $this->invalid('', '객체');
        }
    }

    public function string(string $key): ?string
    {
        $value = $this->data[$key] ?? null;

        return match (true) {
            $value === null, is_string($value) => $value,
            is_int($value), is_float($value) => (string) $value,
            is_bool($value) => $value ? 'true' : 'false',
            default => throw $this->invalid($key, '문자열'),
        };
    }

    public function int(string $key): ?int
    {
        $value = $this->data[$key] ?? null;
        if ($value === null || is_int($value)) {
            return $value;
        }
        if (is_string($value) && preg_match('/^-?[0-9]{1,19}$/', $value) === 1) {
            // int64 up to PHP_INT_MAX (JSON_BIGINT_AS_STRING keeps larger numbers as strings, which are refused)
            $int = filter_var($value, FILTER_VALIDATE_INT);
            if (is_int($int)) {
                return $int;
            }
        }
        if (is_float($value) && floor($value) === $value && abs($value) < 9.0e15) {
            return (int) $value;
        }
        throw $this->invalid($key, '정수');
    }

    public function number(string $key): int|float|null
    {
        $value = $this->data[$key] ?? null;
        if ($value === null || is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return $value + 0;
        }
        throw $this->invalid($key, '숫자');
    }

    public function bool(string $key): ?bool
    {
        $value = $this->data[$key] ?? null;

        return match ($value) {
            null => null,
            true, 'true', 1 => true,
            false, 'false', 0 => false,
            default => throw $this->invalid($key, 'true/false'),
        };
    }

    /** @return list<string>|null */
    public function stringList(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            throw $this->invalid($key, '목록');
        }
        $out = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                throw $this->invalid($key, '문자열 목록');
            }
            $out[] = is_bool($item) ? ($item ? 'true' : 'false') : (string) $item;
        }

        return $out;
    }

    /** @return array<string, string>|null */
    public function stringMap(string $key): ?array
    {
        $value = $this->object($key);
        if ($value === null) {
            return null;
        }
        $out = [];
        foreach ($value as $name => $item) {
            if (!is_scalar($item) && $item !== null) {
                throw $this->invalid($key . '.' . $name, '문자열');
            }
            $out[$name] = is_bool($item) ? ($item ? 'true' : 'false') : (string) $item;
        }

        return $out;
    }

    /** @return array<string, mixed>|null */
    public function object(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            throw $this->invalid($key, '객체');
        }
        $out = [];
        foreach ($value as $name => $item) {
            $out[(string) $name] = $item;
        }

        return $out;
    }

    /** @return list<array<string, mixed>>|null */
    public function objectList(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            throw $this->invalid($key, '목록');
        }
        $out = [];
        foreach ($value as $i => $item) {
            if (!is_array($item) || ($item !== [] && array_is_list($item))) {
                throw $this->invalid("{$key}[{$i}]", '객체');
            }
            $object = [];
            foreach ($item as $name => $field) {
                $object[(string) $name] = $field;
            }
            $out[] = $object;
        }

        return $out;
    }

    /**
     * @template T of ResponseModel
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function model(string $key, string $class): ?ResponseModel
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            throw $this->invalid($key, '객체');
        }

        return $class::fromArray($value, $this->at($key));
    }

    /**
     * @template T of ResponseModel
     *
     * @param class-string<T> $class
     *
     * @return list<T>|null
     */
    public function modelList(string $key, string $class): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            throw $this->invalid($key, '목록');
        }
        $out = [];
        foreach ($value as $i => $item) {
            if (!is_array($item)) {
                throw $this->invalid("{$key}[{$i}]", '객체');
            }
            $out[] = $class::fromArray($item, $this->at($key) . "[{$i}]");
        }

        return $out;
    }

    /**
     * Fields the spec does not describe (new server fields), keyed by their API name.
     *
     * @return array<string, mixed>
     */
    public function extra(): array
    {
        $extra = [];
        foreach ($this->data as $key => $value) {
            if (!in_array((string) $key, $this->known, true)) {
                $extra[(string) $key] = $value;
            }
        }

        return $extra;
    }

    private function invalid(string $key, string $expected): InvalidResponseException
    {
        $path = $this->at($key);

        return new InvalidResponseException(
            '응답 형식이 문서와 다릅니다: ' . ($path === '' ? '(본문)' : $path) . "은(는) {$expected}여야 합니다",
        );
    }

    private function at(string $key): string
    {
        if ($this->path === '') {
            return $key;
        }
        if ($key === '') {
            return $this->path;
        }

        return str_starts_with($key, '[') ? $this->path . $key : $this->path . '.' . $key;
    }
}
