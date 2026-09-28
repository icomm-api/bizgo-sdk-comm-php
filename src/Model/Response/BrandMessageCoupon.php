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
 * 브랜드메시지 쿠폰 요소입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCoupon implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'description', 'urlPc', 'urlMobile', 'schemeAndroid', 'schemeIos'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['title', 'description'];

    /**
     * @param string|null $title 쿠폰명입니다.
     * @param string|null $description 쿠폰 상세 정보입니다.
     * @param string|null $urlPc PC 클릭 URL입니다.
     * @param string|null $urlMobile 모바일 클릭 URL입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 쿠폰명입니다. `${n}원 할인`, `${n}% 할인`, `배송비 할인`, `${7자 이내} 무료`, `${7자 이내} UP` 형식을 지원합니다.
         */
        public ?string $title = null,
        /**
         * 쿠폰 상세 정보입니다. 와이드(FW)·와이드 리스트(FL)·프리미엄 동영상(FP)은 최대 18자, 그 외(캐러셀 아이템 포함)는 최대 12자입니다.
         *
         * 최대 18자
         */
        public ?string $description = null,
        /**
         * PC 클릭 URL입니다.
         */
        public ?string $urlPc = null,
        /**
         * 모바일 클릭 URL입니다.
         */
        public ?string $urlMobile = null,
        /**
         * Android 커스텀 스킴입니다.
         */
        public ?string $schemeAndroid = null,
        /**
         * iOS 커스텀 스킴입니다.
         */
        public ?string $schemeIos = null,
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
            urlPc: $in->string('urlPc'),
            urlMobile: $in->string('urlMobile'),
            schemeAndroid: $in->string('schemeAndroid'),
            schemeIos: $in->string('schemeIos'),
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
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeAndroid' => $this->schemeAndroid,
            'schemeIos' => $this->schemeIos,
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
        return Mask::describe('BrandMessageCoupon', get_object_vars($this));
    }
}
