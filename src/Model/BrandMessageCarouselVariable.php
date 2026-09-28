<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 기본형 변수 분리 방식에서 캐러셀 아이템 1개에 대한 변수입니다(FC, FA).
 *
 * 확인 필요(x-unverified): 각 변수 객체의 내부 구조(키·값 형식)가 문서에 없습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCarouselVariable implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'messageVariable',
        'buttonVariable',
        'couponVariable',
        'imageVariable',
        'commerceVariable',
    ];

    /**
     * @param array<string, mixed>|null $messageVariable 캐러셀 메시지 영역 변수입니다.
     * @param array<string, mixed>|null $buttonVariable 캐러셀 버튼 영역 변수입니다.
     * @param array<string, mixed>|null $couponVariable 캐러셀 쿠폰 영역 변수입니다.
     * @param array<string, mixed>|null $imageVariable 캐러셀 이미지 영역 변수입니다.
     * @param array<string, mixed>|null $commerceVariable 캐러셀 커머스 영역 변수입니다.
     */
    public function __construct(
        /**
         * 캐러셀 메시지 영역 변수입니다. FC, FA 캐러셀 리스트에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?array $messageVariable = null,
        /**
         * 캐러셀 버튼 영역 변수입니다. FC, FA 타입에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?array $buttonVariable = null,
        /**
         * 캐러셀 쿠폰 영역 변수입니다. FC, FA 캐러셀 리스트에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?array $couponVariable = null,
        /**
         * 캐러셀 이미지 영역 변수입니다. FC, FA 캐러셀 리스트에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?array $imageVariable = null,
        /**
         * 캐러셀 커머스 영역 변수입니다. FA 캐러셀 리스트에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?array $commerceVariable = null,
    ) {
    }

    /**
     * Builds the model from an array with API field names (for example decoded JSON).
     * Unknown keys, missing required fields and wrong types are reported together, by field path.
     *
     * @param array<array-key, mixed> $data
     *
     * @throws ValidationException
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new RequestData($data, $path, self::FIELDS);
        $messageVariable = $in->object('messageVariable');
        $buttonVariable = $in->object('buttonVariable');
        $couponVariable = $in->object('couponVariable');
        $imageVariable = $in->object('imageVariable');
        $commerceVariable = $in->object('commerceVariable');

        return $in->create(static fn (): static => new static(
            messageVariable: $messageVariable,
            buttonVariable: $buttonVariable,
            couponVariable: $couponVariable,
            imageVariable: $imageVariable,
            commerceVariable: $commerceVariable,
        ));
    }

    /**
     * The request body with API field names. Only fields you set are included.
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
        ]);
    }

    /**
     * The value for json_encode(): like toArray(), with maps as JSON objects even when empty.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return Serialize::compact([
            'messageVariable' => Serialize::jsonObject($this->messageVariable),
            'buttonVariable' => Serialize::jsonObject($this->buttonVariable),
            'couponVariable' => Serialize::jsonObject($this->couponVariable),
            'imageVariable' => Serialize::jsonObject($this->imageVariable),
            'commerceVariable' => Serialize::jsonObject($this->commerceVariable),
        ]);
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
