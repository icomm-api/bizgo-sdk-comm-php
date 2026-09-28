<?php

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;

/**
 * A response model generated from the OpenAPI spec.
 *
 * Every field is optional so a missing field never breaks parsing, and fields the spec does not know
 * (new server fields) are kept in the `extra` property.
 */
interface ResponseModel
{
    /**
     * @param array<array-key, mixed> $data decoded JSON object
     * @param string $path field path used in error messages
     *
     * @throws InvalidResponseException when a field has an incompatible type
     */
    public static function fromArray(array $data, string $path = ''): static;

    /**
     * The fields with API names, including unknown fields from `extra`. Unset fields are omitted.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
