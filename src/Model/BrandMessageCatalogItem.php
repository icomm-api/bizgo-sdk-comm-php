<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드메시지 카탈로그 아이템 1개입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCatalogItem implements RequestModel
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

    /**
     * @param string $type 카탈로그 아이템 타입입니다.
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
     */
    public function __construct(
        /**
         * 카탈로그 아이템 타입입니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): 카탈로그 아이템 타입 값 목록이 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $type,
        /**
         * 아이템 이미지 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $imgUrl = null,
        /**
         * 이미지 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $imgLink = null,
        /**
         * 상품명입니다.
         */
        #[\SensitiveParameter]
        public ?string $title = null,
        /**
         * 상품 설명입니다.
         */
        #[\SensitiveParameter]
        public ?string $description = null,
        /**
         * 정상 가격입니다.
         */
        #[\SensitiveParameter]
        public ?int $regularPrice = null,
        /**
         * 할인 후 가격입니다.
         */
        #[\SensitiveParameter]
        public ?int $discountPrice = null,
        /**
         * 할인율입니다.
         */
        #[\SensitiveParameter]
        public ?int $discountRate = null,
        /**
         * 정액 할인금액입니다.
         */
        #[\SensitiveParameter]
        public ?int $discountFixed = null,
        /**
         * 정상 가격명입니다.
         */
        #[\SensitiveParameter]
        public ?string $regularPriceName = null,
        /**
         * 할인 후 가격명입니다.
         */
        #[\SensitiveParameter]
        public ?string $discountPriceName = null,
        /**
         * 정상 가격 고정변수명입니다.
         */
        #[\SensitiveParameter]
        public ?string $regularPriceVariableName = null,
        /**
         * 할인 후 가격 고정변수명입니다.
         */
        #[\SensitiveParameter]
        public ?string $discountPriceVariableName = null,
        /**
         * 할인율 고정변수명입니다.
         */
        #[\SensitiveParameter]
        public ?string $discountRateVariableName = null,
        /**
         * 정액 할인금액 고정변수명입니다.
         */
        #[\SensitiveParameter]
        public ?string $discountFixedVariableName = null,
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
        $type = $in->requiredString('type');
        $imgUrl = $in->string('imgUrl');
        $imgLink = $in->string('imgLink');
        $title = $in->string('title');
        $description = $in->string('description');
        $regularPrice = $in->int('regularPrice');
        $discountPrice = $in->int('discountPrice');
        $discountRate = $in->int('discountRate');
        $discountFixed = $in->int('discountFixed');
        $regularPriceName = $in->string('regularPriceName');
        $discountPriceName = $in->string('discountPriceName');
        $regularPriceVariableName = $in->string('regularPriceVariableName');
        $discountPriceVariableName = $in->string('discountPriceVariableName');
        $discountRateVariableName = $in->string('discountRateVariableName');
        $discountFixedVariableName = $in->string('discountFixedVariableName');

        return $in->create(static fn (): static => new static(
            type: $type,
            imgUrl: $imgUrl,
            imgLink: $imgLink,
            title: $title,
            description: $description,
            regularPrice: $regularPrice,
            discountPrice: $discountPrice,
            discountRate: $discountRate,
            discountFixed: $discountFixed,
            regularPriceName: $regularPriceName,
            discountPriceName: $discountPriceName,
            regularPriceVariableName: $regularPriceVariableName,
            discountPriceVariableName: $discountPriceVariableName,
            discountRateVariableName: $discountRateVariableName,
            discountFixedVariableName: $discountFixedVariableName,
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
        return Mask::describe('BrandMessageCatalogItem', get_object_vars($this));
    }
}
