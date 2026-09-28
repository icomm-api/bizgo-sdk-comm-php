<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드메시지 커머스 요소입니다(FM, 캐러셀 커머스 FA).
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCommerce implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'title',
        'regularPrice',
        'discountPrice',
        'discountRate',
        'discountFixed',
        'regularPriceName',
        'discountPriceName',
        'discountRateName',
        'discountFixedName',
    ];

    /**
     * @param string $title 상품명입니다.
     * @param int $regularPrice 정가입니다.
     * @param int|null $discountPrice 할인가입니다.
     * @param int|null $discountRate 할인율(%)입니다.
     * @param int|null $discountFixed 정액 할인금액입니다.
     * @param string|null $regularPriceName 정상 가격명입니다.
     * @param string|null $discountPriceName 할인 후 가격명입니다.
     * @param string|null $discountRateName 할인율명입니다.
     * @param string|null $discountFixedName 정액 할인금액명입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 상품명입니다. 최대 30자입니다.
         *
         * 필수 · 최대 30자
         */
        #[\SensitiveParameter]
        public string $title,
        /**
         * 정가입니다. 0~99,999,999입니다.
         *
         * 필수 · 범위 0~99999999
         */
        #[\SensitiveParameter]
        public int $regularPrice,
        /**
         * 할인가입니다. 0~99,999,999입니다.
         *
         * 범위 0~99999999
         */
        #[\SensitiveParameter]
        public ?int $discountPrice = null,
        /**
         * 할인율(%)입니다. `discountPrice`와 함께 씁니다. 허용 범위는 1~100입니다.
         * 2026년 8월 4일부터 `0`이면 템플릿 등록·발송 요청이 실패하므로, 할인율을 쓰지 않을 때는 `0` 대신 `null`로 보냅니다.
         *
         * 범위 1~100
         *
         * 확인 필요(x-unverified): Part B 필드 표는 0~100으로 적혀 있으나 Part A 안내(1~100, 0 금지)를 따릅니다.
         */
        #[\SensitiveParameter]
        public ?int $discountRate = null,
        /**
         * 정액 할인금액입니다. `discountPrice`와 함께 씁니다. 0~999,999입니다.
         *
         * 범위 0~999999
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
         * 할인율명입니다.
         */
        #[\SensitiveParameter]
        public ?string $discountRateName = null,
        /**
         * 정액 할인금액명입니다.
         */
        #[\SensitiveParameter]
        public ?string $discountFixedName = null,
    ) {
        Check::all(
            Check::maxLength('title', $title, 30),
            Check::range('regularPrice', $regularPrice, 0, 99999999),
            Check::range('discountPrice', $discountPrice, 0, 99999999),
            Check::range('discountRate', $discountRate, 1, 100),
            Check::range('discountFixed', $discountFixed, 0, 999999),
        );
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
        $title = $in->requiredString('title');
        $regularPrice = $in->requiredInt('regularPrice');
        $discountPrice = $in->int('discountPrice');
        $discountRate = $in->int('discountRate');
        $discountFixed = $in->int('discountFixed');
        $regularPriceName = $in->string('regularPriceName');
        $discountPriceName = $in->string('discountPriceName');
        $discountRateName = $in->string('discountRateName');
        $discountFixedName = $in->string('discountFixedName');

        return $in->create(static fn (): static => new static(
            title: $title,
            regularPrice: $regularPrice,
            discountPrice: $discountPrice,
            discountRate: $discountRate,
            discountFixed: $discountFixed,
            regularPriceName: $regularPriceName,
            discountPriceName: $discountPriceName,
            discountRateName: $discountRateName,
            discountFixedName: $discountFixedName,
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
            'title' => $this->title,
            'regularPrice' => $this->regularPrice,
            'discountPrice' => $this->discountPrice,
            'discountRate' => $this->discountRate,
            'discountFixed' => $this->discountFixed,
            'regularPriceName' => $this->regularPriceName,
            'discountPriceName' => $this->discountPriceName,
            'discountRateName' => $this->discountRateName,
            'discountFixedName' => $this->discountFixedName,
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
            'title' => $this->title,
            'regularPrice' => $this->regularPrice,
            'discountPrice' => $this->discountPrice,
            'discountRate' => $this->discountRate,
            'discountFixed' => $this->discountFixed,
            'regularPriceName' => $this->regularPriceName,
            'discountPriceName' => $this->discountPriceName,
            'discountRateName' => $this->discountRateName,
            'discountFixedName' => $this->discountFixedName,
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
        return Mask::describe('BrandMessageCommerce', get_object_vars($this));
    }
}
