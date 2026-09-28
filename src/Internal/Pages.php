<?php

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * Helpers for the generated page iterators.
 *
 * @internal
 */
final class Pages
{
    private function __construct()
    {
    }

    /**
     * The last item of a list (cursor taken from the last item of a page), or null.
     *
     * @template T
     *
     * @param list<T>|null $items
     *
     * @return T|null
     */
    public static function last(?array $items): mixed
    {
        return $items === null || $items === [] ? null : $items[count($items) - 1];
    }

    /** A count documented as a string (`"120"`), as an integer; null when missing or not numeric. */
    public static function int(int|string|null $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        return $value !== null && preg_match('/^[0-9]{1,18}$/', $value) === 1 ? (int) $value : null;
    }
}
