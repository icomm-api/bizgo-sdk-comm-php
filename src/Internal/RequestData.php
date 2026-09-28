<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\ValidationException;
use Bizgo\Model\RequestModel;

/**
 * Reads an input array for a generated request model's `fromArray()`.
 *
 * Collects every problem (unknown key, missing required field, wrong type) with its field path, then
 * {@see self::done()} throws them together. Values never appear in the errors.
 *
 * @internal
 *
 * @phpstan-import-type Error from Check
 */
final class RequestData
{
    /** @var list<Error> */
    private array $errors = [];

    /**
     * @param array<array-key, mixed> $data
     * @param list<string> $known API field names of the model
     */
    public function __construct(
        #[\SensitiveParameter]
        private readonly array $data,
        private readonly string $path,
        array $known,
    ) {
        if ($data !== [] && array_is_list($data)) {
            $this->errors[] = Check::error($path, '객체(키-값 배열)여야 합니다');

            return;
        }
        foreach (array_keys($data) as $key) {
            if (!in_array((string) $key, $known, true)) {
                $this->errors[] = Check::error($this->at((string) $key), '알 수 없는 필드입니다');
            }
        }
    }

    public function string(string $key): ?string
    {
        $value = $this->data[$key] ?? null;
        if ($value === null || is_string($value)) {
            return $value;
        }
        $this->errors[] = Check::error($this->at($key), '문자열이어야 합니다');

        return null;
    }

    public function requiredString(string $key): string
    {
        return $this->required($key) ? ($this->string($key) ?? '') : '';
    }

    public function int(string $key): ?int
    {
        $value = $this->data[$key] ?? null;
        if ($value === null || is_int($value)) {
            return $value;
        }
        $this->errors[] = Check::error($this->at($key), '정수여야 합니다');

        return null;
    }

    public function requiredInt(string $key): int
    {
        return $this->required($key) ? ($this->int($key) ?? 0) : 0;
    }

    public function number(string $key): int|float|null
    {
        $value = $this->data[$key] ?? null;
        if ($value === null || is_int($value) || is_float($value)) {
            return $value;
        }
        $this->errors[] = Check::error($this->at($key), '숫자여야 합니다');

        return null;
    }

    public function requiredNumber(string $key): int|float
    {
        return $this->required($key) ? ($this->number($key) ?? 0) : 0;
    }

    public function bool(string $key): ?bool
    {
        $value = $this->data[$key] ?? null;
        if ($value === null || is_bool($value)) {
            return $value;
        }
        $this->errors[] = Check::error($this->at($key), 'true 또는 false여야 합니다');

        return null;
    }

    public function requiredBool(string $key): bool
    {
        return $this->required($key) && ($this->bool($key) ?? false);
    }

    /** @return list<string>|null */
    public function stringList(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            $this->errors[] = Check::error($this->at($key), '목록(list)이어야 합니다');

            return null;
        }
        $out = [];
        foreach ($value as $i => $item) {
            if (!is_string($item)) {
                $this->errors[] = Check::error($this->at($key) . "[{$i}]", '문자열이어야 합니다');
                continue;
            }
            $out[] = $item;
        }

        return $out;
    }

    /** @return list<string> */
    public function requiredStringList(string $key): array
    {
        return $this->required($key) ? ($this->stringList($key) ?? []) : [];
    }

    /** @return array<string, string>|null */
    public function stringMap(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            $this->errors[] = Check::error($this->at($key), '객체(키-값 배열)여야 합니다');

            return null;
        }
        $out = [];
        foreach ($value as $name => $item) {
            if (!is_string($item)) {
                $this->errors[] = Check::error($this->at($key) . '.' . $name, '문자열이어야 합니다');
                continue;
            }
            $out[(string) $name] = $item;
        }

        return $out;
    }

    /** @return array<string, string> */
    public function requiredStringMap(string $key): array
    {
        return $this->required($key) ? ($this->stringMap($key) ?? []) : [];
    }

    /**
     * A free-form object (`type: object` without properties). Passed through as is.
     *
     * @return array<string, mixed>|null
     */
    public function object(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            $this->errors[] = Check::error($this->at($key), '객체(키-값 배열)여야 합니다');

            return null;
        }
        $out = [];
        foreach ($value as $name => $item) {
            $out[(string) $name] = $item;
        }

        return $out;
    }

    /** @return array<string, mixed> */
    public function requiredObject(string $key): array
    {
        return $this->required($key) ? ($this->object($key) ?? []) : [];
    }

    /**
     * A list of free-form objects. Passed through as is.
     *
     * @return list<array<string, mixed>>|null
     */
    public function objectList(string $key): ?array
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            $this->errors[] = Check::error($this->at($key), '목록(list)이어야 합니다');

            return null;
        }
        $out = [];
        foreach ($value as $i => $item) {
            if (!is_array($item) || ($item !== [] && array_is_list($item))) {
                $this->errors[] = Check::error($this->at($key) . "[{$i}]", '객체(키-값 배열)여야 합니다');
                continue;
            }
            $object = [];
            foreach ($item as $name => $field) {
                $object[(string) $name] = $field;
            }
            $out[] = $object;
        }

        return $out;
    }

    /** @return list<array<string, mixed>> */
    public function requiredObjectList(string $key): array
    {
        return $this->required($key) ? ($this->objectList($key) ?? []) : [];
    }

    /**
     * @template T of RequestModel
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function model(string $key, string $class, bool $required = false): ?RequestModel
    {
        if ($required && !$this->required($key)) {
            return null;
        }
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            $this->errors[] = Check::error($this->at($key), '객체(키-값 배열)여야 합니다');

            return null;
        }
        try {
            return $class::fromArray($value, $this->at($key));
        } catch (ValidationException $e) {
            array_push($this->errors, ...$e->errors);

            return null;
        }
    }

    /**
     * @template T of RequestModel
     *
     * @param class-string<T> $class
     *
     * @return list<T>|null
     */
    public function modelList(string $key, string $class, bool $required = false): ?array
    {
        if ($required && !$this->required($key)) {
            return null;
        }
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            $this->errors[] = Check::error($this->at($key), '목록(list)이어야 합니다');

            return null;
        }
        $out = [];
        foreach ($value as $i => $item) {
            $path = $this->at($key) . "[{$i}]";
            if (!is_array($item)) {
                $this->errors[] = Check::error($path, '객체(키-값 배열)여야 합니다');
                continue;
            }
            try {
                $out[] = $class::fromArray($item, $path);
            } catch (ValidationException $e) {
                array_push($this->errors, ...$e->errors);
            }
        }

        return $out;
    }

    /**
     * Throws every collected error.
     *
     * @throws ValidationException
     */
    public function done(): void
    {
        if ($this->errors !== []) {
            throw new ValidationException($this->errors);
        }
    }

    /**
     * Runs the model constructor, prefixing the paths of its validation errors.
     *
     * @template T
     *
     * @param \Closure(): T $create
     *
     * @return T
     *
     * @throws ValidationException
     */
    public function create(\Closure $create): mixed
    {
        $this->done();
        try {
            return $create();
        } catch (ValidationException $e) {
            throw $e->withPrefix($this->path);
        }
    }

    private function required(string $key): bool
    {
        if (($this->data[$key] ?? null) !== null) {
            return true;
        }
        $this->errors[] = Check::error($this->at($key), '필수 필드입니다');

        return false;
    }

    private function at(string $key): string
    {
        return ValidationException::join($this->path, $key);
    }
}
