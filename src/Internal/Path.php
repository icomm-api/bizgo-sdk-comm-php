<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\ValidationException;

/**
 * Path parameter encoding.
 *
 * @internal
 */
final class Path
{
    private function __construct()
    {
    }

    /**
     * Encodes one path segment (`/`, `?`, `#`, `%` and everything else outside unreserved characters) so a value
     * can never change the request path. Empty values and the dot segments `.` / `..` are refused.
     *
     * @throws ValidationException
     */
    public static function segment(string $name, #[\SensitiveParameter] string $value): string
    {
        if ($value === '' || $value === '.' || $value === '..') {
            throw ValidationException::of($name, '경로에 쓸 수 없는 값입니다 (빈 값, ".", "..")');
        }

        return rawurlencode($value);
    }
}
