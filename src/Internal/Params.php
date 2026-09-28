<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\ValidationException;
use Bizgo\Model\RequestModel;

/**
 * Query and header parameter conversion for the generated resource methods. Errors name the parameter, never
 * its value (values can be phone numbers).
 *
 * @internal
 *
 * @phpstan-import-type Error from Check
 */
final class Params
{
    private function __construct()
    {
    }

    /**
     * A date/time query value in the documented `x-format`, normalised by {@see Dates} (KST, invalid values refused).
     */
    public static function date(string $name, #[\SensitiveParameter] \DateTimeInterface|string|null $value, string $format): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($format) {
            'YYYYMMDD', 'yyyyMMdd' => Dates::yyyymmdd($value, $name),
            "yyyy-MM-dd'T'HH:mm:ss" => Dates::localTime($value, $name),
            "yyyy-MM-dd'T'HH:mm:ssXXX" => Dates::offsetTime($value, $name),
            'yyyy-MM-dd HH:mm:ss' => Dates::spaced($value, $name),
            'yyyyMMddHHmmss' => Dates::compact($value, $name),
            default => throw ValidationException::of($name, "지원하지 않는 날짜 형식입니다 ({$format})"),
        };
    }

    /** `true`/`false` as the API expects in a query string. */
    public static function bool(?bool $value): ?string
    {
        return $value === null ? null : ($value ? 'true' : 'false');
    }

    /**
     * A string array query value (`explode: false`): comma-separated.
     *
     * @param string|array<array-key, mixed>|null $value
     *
     * @throws ValidationException
     */
    public static function csv(string $name, #[\SensitiveParameter] string|array|null $value, ?int $minItems): ?string
    {
        if ($value === null) {
            return null;
        }
        $items = is_string($value) ? [$value] : $value;
        foreach ($items as $item) {
            if (!is_string($item) || $item === '' || str_contains($item, ',')) {
                throw ValidationException::of($name, '쉼표가 없는 비어 있지 않은 문자열(또는 그 목록)이어야 합니다');
            }
        }
        if ($minItems !== null && count($items) < $minItems) {
            throw ValidationException::of($name, "항목이 최소 {$minItems}개 있어야 합니다");
        }

        return implode(',', $items);
    }

    /**
     * A header parameter: printable ASCII only, so a value can never add a header line (CR/LF injection).
     *
     * @throws ValidationException
     */
    public static function header(string $name, #[\SensitiveParameter] string $value): string
    {
        if (preg_match('/^[\x20-\x7e]+$/', $value) !== 1) {
            throw ValidationException::of($name, '비어 있지 않은 한 줄 ASCII 문자열이어야 합니다 (줄바꿈·제어문자 불가)');
        }

        return $value;
    }

    /**
     * Fields a JSON part additionally requires (`allOf: [$ref, {required: [...]}]`).
     *
     * @param RequestModel|array<array-key, mixed>|null $value
     * @param list<string> $fields
     *
     * @return list<Error>
     */
    public static function requiredFields(string $name, #[\SensitiveParameter] RequestModel|array|null $value, array $fields): array
    {
        if ($value === null) {
            return [];
        }
        $data = is_array($value) ? $value : $value->toArray();
        $errors = [];
        foreach ($fields as $field) {
            if (($data[$field] ?? null) === null) {
                $errors[] = Check::error($name . '.' . $field, '필수 필드입니다');
            }
        }

        return $errors;
    }
}
