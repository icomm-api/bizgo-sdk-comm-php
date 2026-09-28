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
 * 기본형 변수 분리 방식에서 캐러셀 아이템 1개에 대한 변수입니다(FC, FA).
 *
 * 확인 필요(x-unverified): 각 변수 객체의 내부 구조(키·값 형식)가 문서에 없습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCarouselVariable implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'messageVariable',
        'buttonVariable',
        'couponVariable',
        'imageVariable',
        'commerceVariable',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param array<string, mixed>|null $messageVariable 캐러셀 메시지 영역 변수입니다.
     * @param array<string, mixed>|null $buttonVariable 캐러셀 버튼 영역 변수입니다.
     * @param array<string, mixed>|null $couponVariable 캐러셀 쿠폰 영역 변수입니다.
     * @param array<string, mixed>|null $imageVariable 캐러셀 이미지 영역 변수입니다.
     * @param array<string, mixed>|null $commerceVariable 캐러셀 커머스 영역 변수입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 캐러셀 메시지 영역 변수입니다. FC, FA 캐러셀 리스트에서 사용합니다.
         */
        public ?array $messageVariable = null,
        /**
         * 캐러셀 버튼 영역 변수입니다. FC, FA 타입에서 사용합니다.
         */
        public ?array $buttonVariable = null,
        /**
         * 캐러셀 쿠폰 영역 변수입니다. FC, FA 캐러셀 리스트에서 사용합니다.
         */
        public ?array $couponVariable = null,
        /**
         * 캐러셀 이미지 영역 변수입니다. FC, FA 캐러셀 리스트에서 사용합니다.
         */
        public ?array $imageVariable = null,
        /**
         * 캐러셀 커머스 영역 변수입니다. FA 캐러셀 리스트에서 사용합니다.
         */
        public ?array $commerceVariable = null,
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
            messageVariable: $in->object('messageVariable'),
            buttonVariable: $in->object('buttonVariable'),
            couponVariable: $in->object('couponVariable'),
            imageVariable: $in->object('imageVariable'),
            commerceVariable: $in->object('commerceVariable'),
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
            'messageVariable' => $this->messageVariable,
            'buttonVariable' => $this->buttonVariable,
            'couponVariable' => $this->couponVariable,
            'imageVariable' => $this->imageVariable,
            'commerceVariable' => $this->commerceVariable,
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
        return Mask::describe('BrandMessageCarouselVariable', get_object_vars($this));
    }
}
