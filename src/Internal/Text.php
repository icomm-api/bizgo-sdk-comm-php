<?php

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * Text helpers.
 *
 * @internal
 */
final class Text
{
    private function __construct()
    {
    }

    public static function isUtf8(string $value): bool
    {
        return mb_check_encoding($value, 'UTF-8');
    }

    /**
     * Encoded length of `$value` the way the carrier counts it, or null if it cannot be encoded.
     *
     * For `EUC-KR` the value is encoded as CP949 (a superset of EUC-KR with the same 2 bytes per Hangul syllable,
     * which also covers every modern Hangul syllable). A character CP949 cannot represent, such as an emoji,
     * would be replaced silently by mbstring, so the result is decoded again and compared with the input.
     */
    public static function byteLength(string $value, ?string $charset): ?int
    {
        if (!self::isUtf8($value)) {
            return null;
        }
        if ($charset === null || strtoupper($charset) !== 'EUC-KR') {
            return strlen($value);
        }
        $encoded = mb_convert_encoding($value, 'CP949', 'UTF-8');
        if (!is_string($encoded) || mb_convert_encoding($encoded, 'UTF-8', 'CP949') !== $value) {
            return null;
        }

        return strlen($encoded);
    }
}
