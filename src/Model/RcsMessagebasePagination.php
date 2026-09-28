<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 전체 건수와 페이지 조건 정보입니다. 문서상 모든 값이 문자열입니다.
 *
 * 확인 필요(x-unverified): offset·limit·total이 문자열(String)로 표기되어 있으나 숫자일 가능성이 있습니다. 문서 응답 예시에는 pagination이
 * 없습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsMessagebasePagination implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['offset', 'limit', 'total'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $offset 조회 기준 위치입니다.
     * @param string|null $limit 페이지당 조회 건수입니다.
     * @param string|null $total 전체 건수입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 조회 기준 위치입니다.
         */
        public ?string $offset = null,
        /**
         * 페이지당 조회 건수입니다.
         */
        public ?string $limit = null,
        /**
         * 전체 건수입니다.
         */
        public ?string $total = null,
        /** Fields the spec does not describe (new server fields), by API name. */
        public array $extra = [],
    ) {
    }

    /**
     * @param array<array-key, mixed> $data decoded JSON object
     *
     * @throws InvalidResponseException when a field has an incompatible type
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new ResponseData($data, $path, self::FIELDS);

        return new static(
            offset: $in->string('offset'),
            limit: $in->string('limit'),
            total: $in->string('total'),
            extra: $in->extra(),
        );
    }

    /**
     * The fields with API names, followed by the unknown fields from `$extra`. Unset fields are omitted.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Serialize::compact([
            'offset' => $this->offset,
            'limit' => $this->limit,
            'total' => $this->total,
        ]) + $this->extra;
    }

    /**
     * The fields for var_dump()/print_r(): phone numbers masked (`010****0000`), message content and personal
     * data shown as their length only. The properties keep the real values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return Mask::fields(get_object_vars($this));
    }

    /** A log-safe description, masked like {@see self::__debugInfo()}. */
    public function __toString(): string
    {
        return Mask::describe('RcsMessagebasePagination', get_object_vars($this));
    }
}
