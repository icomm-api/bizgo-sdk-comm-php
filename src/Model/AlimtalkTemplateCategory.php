<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 알림톡 템플릿 카테고리입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkTemplateCategory implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['code', 'name', 'groupName', 'inclusion', 'exclusion'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $code 카테고리 코드입니다.
     * @param string|null $name 카테고리 이름입니다.
     * @param string|null $groupName 카테고리 그룹 이름입니다.
     * @param string|null $inclusion 포함 기준 설명입니다.
     * @param string|null $exclusion 제외 기준 설명입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 카테고리 코드입니다.
         */
        public ?string $code = null,
        /**
         * 카테고리 이름입니다.
         */
        public ?string $name = null,
        /**
         * 카테고리 그룹 이름입니다.
         */
        public ?string $groupName = null,
        /**
         * 포함 기준 설명입니다.
         */
        public ?string $inclusion = null,
        /**
         * 제외 기준 설명입니다.
         */
        public ?string $exclusion = null,
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
            code: $in->string('code'),
            name: $in->string('name'),
            groupName: $in->string('groupName'),
            inclusion: $in->string('inclusion'),
            exclusion: $in->string('exclusion'),
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
            'code' => $this->code,
            'name' => $this->name,
            'groupName' => $this->groupName,
            'inclusion' => $this->inclusion,
            'exclusion' => $this->exclusion,
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
        return Mask::describe('AlimtalkTemplateCategory', get_object_vars($this));
    }
}
