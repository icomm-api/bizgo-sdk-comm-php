<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 동영상 목록 데이터입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageVideoListResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['offset', 'limit', 'totalCount', 'videos'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param int|null $offset 조회 시작 위치입니다.
     * @param int|null $limit 조회 건수입니다.
     * @param int|null $totalCount 전체 동영상 건수입니다.
     * @param list<BrandMessageVideoListEntry>|null $videos 동영상 이력 배열입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 조회 시작 위치입니다.
         */
        public ?int $offset = null,
        /**
         * 조회 건수입니다.
         */
        public ?int $limit = null,
        /**
         * 전체 동영상 건수입니다.
         */
        public ?int $totalCount = null,
        /**
         * 동영상 이력 배열입니다.
         */
        public ?array $videos = null,
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
            offset: $in->int('offset'),
            limit: $in->int('limit'),
            totalCount: $in->int('totalCount'),
            videos: $in->modelList('videos', BrandMessageVideoListEntry::class),
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
            'totalCount' => $this->totalCount,
            'videos' => Serialize::models($this->videos),
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
        return Mask::describe('BrandMessageVideoListResult', get_object_vars($this));
    }
}
