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
 * 브랜드메시지 와이드 아이템 목록의 항목 1개입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageItemListEntry implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'imgUrl', 'urlMobile', 'urlPc', 'schemeIos', 'schemeAndroid'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $title 아이템 제목입니다.
     * @param string|null $imgUrl 아이템 이미지 URL입니다.
     * @param string|null $urlMobile 모바일 클릭 URL입니다.
     * @param string|null $urlPc PC 클릭 URL입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 아이템 제목입니다. 첫 번째 아이템은 최대 25자(줄바꿈 1회), 2~4번째 아이템은 최대 30자(줄바꿈 1회)입니다.
         *
         * 최대 30자
         */
        public ?string $title = null,
        /**
         * 아이템 이미지 URL입니다. 첫 번째 아이템은 와이드 리스트 첫번째 이미지 업로드, 2~4번째는 와이드 리스트 이미지 업로드로 발급받은 URL을 씁니다.
         */
        public ?string $imgUrl = null,
        /**
         * 모바일 클릭 URL입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        public ?string $urlMobile = null,
        /**
         * PC 클릭 URL입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        public ?string $urlPc = null,
        /**
         * iOS 커스텀 스킴입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        public ?string $schemeIos = null,
        /**
         * Android 커스텀 스킴입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        public ?string $schemeAndroid = null,
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
            imgUrl: $in->string('imgUrl'),
            urlMobile: $in->string('urlMobile'),
            urlPc: $in->string('urlPc'),
            schemeIos: $in->string('schemeIos'),
            schemeAndroid: $in->string('schemeAndroid'),
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
            'imgUrl' => $this->imgUrl,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
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
        return Mask::describe('BrandMessageItemListEntry', get_object_vars($this));
    }
}
