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
 * 브랜드메시지 카탈로그 아이템 1개입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCatalogItem implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'type',
        'imgUrl',
        'imgLink',
        'title',
        'description',
        'regularPrice',
        'discountPrice',
        'discountRate',
        'discountFixed',
        'regularPriceName',
        'discountPriceName',
        'regularPriceVariableName',
        'discountPriceVariableName',
        'discountRateVariableName',
        'discountFixedVariableName',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['type'];

    /**
     * @param string|null $type 카탈로그 아이템 타입입니다.
     * @param string|null $imgUrl 아이템 이미지 URL입니다.
     * @param string|null $imgLink 이미지 클릭 시 이동할 URL입니다.
     * @param string|null $title 상품명입니다.
     * @param string|null $description 상품 설명입니다.
     * @param int|null $regularPrice 정상 가격입니다.
     * @param int|null $discountPrice 할인 후 가격입니다.
     * @param int|null $discountRate 할인율입니다.
     * @param int|null $discountFixed 정액 할인금액입니다.
     * @param string|null $regularPriceName 정상 가격명입니다.
     * @param string|null $discountPriceName 할인 후 가격명입니다.
     * @param string|null $regularPriceVariableName 정상 가격 고정변수명입니다.
     * @param string|null $discountPriceVariableName 할인 후 가격 고정변수명입니다.
     * @param string|null $discountRateVariableName 할인율 고정변수명입니다.
     * @param string|null $discountFixedVariableName 정액 할인금액 고정변수명입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 카탈로그 아이템 타입입니다.
         *
         * 확인 필요(x-unverified): 카탈로그 아이템 타입 값 목록이 문서에 없습니다.
         */
        public ?string $type = null,
        /**
         * 아이템 이미지 URL입니다.
         */
        public ?string $imgUrl = null,
        /**
         * 이미지 클릭 시 이동할 URL입니다.
         */
        public ?string $imgLink = null,
        /**
         * 상품명입니다.
         */
        public ?string $title = null,
        /**
         * 상품 설명입니다.
         */
        public ?string $description = null,
        /**
         * 정상 가격입니다.
         */
        public ?int $regularPrice = null,
        /**
         * 할인 후 가격입니다.
         */
        public ?int $discountPrice = null,
        /**
         * 할인율입니다.
         */
        public ?int $discountRate = null,
        /**
         * 정액 할인금액입니다.
         */
        public ?int $discountFixed = null,
        /**
         * 정상 가격명입니다.
         */
        public ?string $regularPriceName = null,
        /**
         * 할인 후 가격명입니다.
         */
        public ?string $discountPriceName = null,
        /**
         * 정상 가격 고정변수명입니다.
         */
        public ?string $regularPriceVariableName = null,
        /**
         * 할인 후 가격 고정변수명입니다.
         */
        public ?string $discountPriceVariableName = null,
        /**
         * 할인율 고정변수명입니다.
         */
        public ?string $discountRateVariableName = null,
        /**
         * 정액 할인금액 고정변수명입니다.
         */
        public ?string $discountFixedVariableName = null,
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
            type: $in->string('type'),
            imgUrl: $in->string('imgUrl'),
            imgLink: $in->string('imgLink'),
            title: $in->string('title'),
            description: $in->string('description'),
            regularPrice: $in->int('regularPrice'),
            discountPrice: $in->int('discountPrice'),
            discountRate: $in->int('discountRate'),
            discountFixed: $in->int('discountFixed'),
            regularPriceName: $in->string('regularPriceName'),
            discountPriceName: $in->string('discountPriceName'),
            regularPriceVariableName: $in->string('regularPriceVariableName'),
            discountPriceVariableName: $in->string('discountPriceVariableName'),
            discountRateVariableName: $in->string('discountRateVariableName'),
            discountFixedVariableName: $in->string('discountFixedVariableName'),
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
            'type' => $this->type,
            'imgUrl' => $this->imgUrl,
            'imgLink' => $this->imgLink,
            'title' => $this->title,
            'description' => $this->description,
            'regularPrice' => $this->regularPrice,
            'discountPrice' => $this->discountPrice,
            'discountRate' => $this->discountRate,
            'discountFixed' => $this->discountFixed,
            'regularPriceName' => $this->regularPriceName,
            'discountPriceName' => $this->discountPriceName,
            'regularPriceVariableName' => $this->regularPriceVariableName,
            'discountPriceVariableName' => $this->discountPriceVariableName,
            'discountRateVariableName' => $this->discountRateVariableName,
            'discountFixedVariableName' => $this->discountFixedVariableName,
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
        return Mask::describe('BrandMessageCatalogItem', get_object_vars($this));
    }
}
