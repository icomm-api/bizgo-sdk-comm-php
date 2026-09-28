<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\ValidationException;

/**
 * Date formats of the query parameters (SDK-DESIGN.md §3). `DateTimeInterface` values are converted to Asia/Seoul
 * (KST). Strings are parsed and normalised the same way the other SDKs do: an invalid date is refused before
 * sending, a time with an offset or `Z` is converted to KST, and a time without an offset is taken as KST.
 *
 * A PHP `DateTime` always has a time zone: one created without an explicit zone uses `date.timezone`, so
 * `new DateTime('2026-09-23 09:00')` on a server set to UTC means 18:00 KST. Pass
 * `new DateTimeImmutable('...', new DateTimeZone('Asia/Seoul'))` or a string when you mean Korean time.
 *
 * @internal
 */
final class Dates
{
    private function __construct()
    {
    }

    /** `YYYYMMDD` (statistics, insights). A string must be a real date in that form (or `yyyy-MM-dd`). */
    public static function yyyymmdd(\DateTimeInterface|string $value, string $field = 'date'): string
    {
        if (!is_string($value)) {
            return self::kst($value)->format('Ymd');
        }
        $digits = preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value) === 1 ? str_replace('-', '', $value) : $value;
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/D', $digits, $m) !== 1 || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            throw ValidationException::of($field, 'YYYYMMDD 형식의 날짜여야 합니다');
        }

        return $digits;
    }

    /** `yyyy-MM-dd'T'HH:mm:ss` in KST, without offset (send history `requestTime`). */
    public static function localTime(\DateTimeInterface|string $value, string $field = 'requestTime'): string
    {
        return self::kst(self::parse($value, $field))->format('Y-m-d\TH:i:s');
    }

    /** `yyyy-MM-dd'T'HH:mm:ss+09:00` (MO history `occurredTime`); a time without an offset is taken as KST. */
    public static function offsetTime(\DateTimeInterface|string $value, string $field = 'occurredTime'): string
    {
        return self::kst(self::parse($value, $field))->format('Y-m-d\TH:i:sP');
    }

    /** `yyyy-MM-dd HH:mm:ss` in KST. */
    public static function spaced(\DateTimeInterface|string $value, string $field): string
    {
        return self::kst(self::parse($value, $field))->format('Y-m-d H:i:s');
    }

    /** `yyyyMMddHHmmss` in KST. */
    public static function compact(\DateTimeInterface|string $value, string $field): string
    {
        if (is_string($value) && preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/D', $value, $m) === 1) {
            $value = "{$m[1]}-{$m[2]}-{$m[3]}T{$m[4]}:{$m[5]}:{$m[6]}";
        }

        return self::kst(self::parse($value, $field))->format('YmdHis');
    }

    /** The same instant in Asia/Seoul. */
    public static function kst(\DateTimeInterface $value): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromInterface($value)->setTimezone(new \DateTimeZone('Asia/Seoul'));
    }

    /**
     * An ISO-8601 local date-time (`T` or space, optional fraction), with an optional offset or `Z`. Without an
     * offset the time is KST.
     *
     * @throws ValidationException
     */
    private static function parse(\DateTimeInterface|string $value, string $field): \DateTimeInterface
    {
        if (!is_string($value)) {
            return $value;
        }
        $pattern = '/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2}):(\d{2})(\.\d{1,6})?(Z|[+-]\d{2}:?\d{2})?$/D';
        if (
            preg_match($pattern, $value, $m) !== 1 || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])
            || (int) $m[4] > 23 || (int) $m[5] > 59 || (int) $m[6] > 59
        ) {
            throw ValidationException::of($field, "yyyy-MM-dd'T'HH:mm:ss 형식의 날짜·시각이어야 합니다 (시간대 없으면 KST)");
        }
        $zone = ($m[8] ?? '') === '' ? 'Asia/Seoul' : ($m[8] === 'Z' ? 'UTC' : $m[8]);
        try {
            $timezone = new \DateTimeZone($zone);
        } catch (\Exception) {
            throw ValidationException::of($field, '시간대 오프셋이 올바르지 않습니다');
        }
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}:{$m[5]}:{$m[6]}", $timezone);
        if ($parsed === false) {
            throw ValidationException::of($field, '날짜·시각을 해석할 수 없습니다');
        }

        return $parsed;
    }
}
