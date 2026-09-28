<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Model\RequestModel;
use Bizgo\Model\ResponseModel;

/**
 * Serialization helpers used by the generated models.
 *
 * @internal
 */
final class Serialize
{
    private function __construct()
    {
    }

    /**
     * Drops unset (null) fields.
     *
     * @param array<string, mixed> $fields
     *
     * @return array<string, mixed>
     */
    public static function compact(array $fields): array
    {
        return array_filter($fields, static fn (mixed $value): bool => $value !== null);
    }

    /**
     * @param list<RequestModel|ResponseModel>|null $models
     *
     * @return list<array<string, mixed>>|null
     */
    public static function models(?array $models): ?array
    {
        return $models === null ? null : array_map(static fn (RequestModel|ResponseModel $m): array => $m->toArray(), $models);
    }

    /**
     * A list of free-form objects, each encoded as a JSON object.
     *
     * @param list<array<array-key, mixed>>|null $items
     *
     * @return list<\stdClass>|null
     */
    public static function jsonObjects(?array $items): ?array
    {
        return $items === null ? null : array_map(static fn (array $item): \stdClass => (object) $item, $items);
    }

    /**
     * A map (`additionalProperties`) or free-form object, as a JSON object even when it is empty
     * or has numeric keys (a PHP array would be encoded as a JSON list in those cases).
     *
     * @param array<array-key, mixed>|null $map
     */
    public static function jsonObject(?array $map): ?\stdClass
    {
        return $map === null ? null : (object) $map;
    }
}
