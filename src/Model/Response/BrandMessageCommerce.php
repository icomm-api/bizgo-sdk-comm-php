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
 * 브랜드메시지 커머스 요소입니다(FM, 캐러셀 커머스 FA).
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCommerce implements ResponseModel
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

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['title', 'regularPrice'];

    /**
     * @param string|null $title 상품명입니다.
     * @param int|null $regularPrice 정가입니다.
     * @param int|null $discountPrice 할인가입니다.
     * @param int|null $discountRate 할인율(%)입니다.
     * @param int|null $discountFixed 정액 할인금액입니다.
     * @param string|null $regularPriceName 정상 가격명입니다.
     * @param string|null $discountPriceName 할인 후 가격명입니다.
     * @param string|null $discountRateName 할인율명입니다.
     * @param string|null $discountFixedName 정액 할인금액명입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 상품명입니다. 최대 30자입니다.
         *
         * 최대 30자
         */
        public ?string $title = null,
        /**
         * 정가입니다. 0~99,999,999입니다.
         *
         * 범위 0~99999999
         */
        public ?int $regularPrice = null,
        /**
         * 할인가입니다. 0~99,999,999입니다.
         *
         * 범위 0~99999999
         */
        public ?int $discountPrice = null,
        /**
         * 할인율(%)입니다. `discountPrice`와 함께 씁니다. 허용 범위는 1~100입니다.
         * 2026년 8월 4일부터 `0`이면 템플릿 등록·발송 요청이 실패하므로, 할인율을 쓰지 않을 때는 `0` 대신 `null`로 보냅니다.
         *
         * 범위 1~100
         *
         * 확인 필요(x-unverified): Part B 필드 표는 0~100으로 적혀 있으나 Part A 안내(1~100, 0 금지)를 따릅니다.
         */
        public ?int $discountRate = null,
        /**
         * 정액 할인금액입니다. `discountPrice`와 함께 씁니다. 0~999,999입니다.
         *
         * 범위 0~999999
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
         * 할인율명입니다.
         */
        public ?string $discountRateName = null,
        /**
         * 정액 할인금액명입니다.
         */
        public ?string $discountFixedName = null,
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
            regularPrice: $in->int('regularPrice'),
            discountPrice: $in->int('discountPrice'),
            discountRate: $in->int('discountRate'),
            discountFixed: $in->int('discountFixed'),
            regularPriceName: $in->string('regularPriceName'),
            discountPriceName: $in->string('discountPriceName'),
            discountRateName: $in->string('discountRateName'),
            discountFixedName: $in->string('discountFixedName'),
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
            'regularPrice' => $this->regularPrice,
            'discountPrice' => $this->discountPrice,
            'discountRate' => $this->discountRate,
            'discountFixed' => $this->discountFixed,
            'regularPriceName' => $this->regularPriceName,
            'discountPriceName' => $this->discountPriceName,
            'discountRateName' => $this->discountRateName,
            'discountFixedName' => $this->discountFixedName,
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
        return Mask::describe('BrandMessageCommerce', get_object_vars($this));
    }
}
