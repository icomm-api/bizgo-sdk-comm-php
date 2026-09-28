<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model\Response;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;
use Bizgo\Model\ResponseModel;

/**
 * 알림톡 아이템 하이라이트 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkItemHighlight implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'description'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['title', 'description'];

    /**
     * @param string|null $title 아이템 하이라이트 타이틀입니다.
     * @param string|null $description 아이템 하이라이트 부가정보입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 아이템 하이라이트 타이틀입니다. 최대 30자이며, 이미지가 있으면 최대 21자입니다.
         *
         * 최대 30자
         */
        public ?string $title = null,
        /**
         * 아이템 하이라이트 부가정보입니다. 최대 19자이며, 이미지가 있으면 최대 13자입니다.
         *
         * 최대 19자
         */
        public ?string $description = null,
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
            title: $in->string('title'),
            description: $in->string('description'),
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
            'title' => $this->title,
            'description' => $this->description,
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
        return Mask::describe('AlimtalkItemHighlight', get_object_vars($this));
    }
}
