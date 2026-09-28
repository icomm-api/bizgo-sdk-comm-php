<?php

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;

/**
 * A request body model generated from the OpenAPI spec.
 *
 * - Constructing one validates it (required fields, maxLength, maxItems, enums, EUC-KR byte limits, ...),
 *   so an invalid request never leaves your server.
 * - {@see self::fromArray()} also rejects unknown keys, so typos such as `templatecode` fail fast.
 * - {@see self::toArray()} uses the API field names and emits only the fields you set.
 */
interface RequestModel extends \JsonSerializable
{
    /**
     * Builds the model from an array with API field names (for example decoded JSON).
     *
     * @param array<array-key, mixed> $data
     * @param string $path field path used in error messages
     *
     * @throws ValidationException
     */
    public static function fromArray(array $data, string $path = ''): static;

    /**
     * The request body with API field names. Only fields you set are included; spec defaults are left to the server.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
