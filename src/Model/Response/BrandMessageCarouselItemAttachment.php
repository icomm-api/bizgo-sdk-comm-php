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
 * 캐러셀 아이템 첨부 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCarouselItemAttachment implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['button', 'image', 'coupon', 'commerce'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param list<BrandMessageButton>|null $button 캐러셀 아이템 버튼 목록입니다.
     * @param BrandMessageImage|null $image
     * @param BrandMessageCoupon|null $coupon
     * @param BrandMessageCommerce|null $commerce
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 캐러셀 아이템 버튼 목록입니다. 최대 5개이며, FC/FA 타입에 쿠폰을 함께 쓰면 최대 4개입니다.
         *
         * 항목 최대 5개
         */
        public ?array $button = null,
        public ?BrandMessageImage $image = null,
        public ?BrandMessageCoupon $coupon = null,
        public ?BrandMessageCommerce $commerce = null,
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
            button: $in->modelList('button', BrandMessageButton::class),
            image: $in->model('image', BrandMessageImage::class),
            coupon: $in->model('coupon', BrandMessageCoupon::class),
            commerce: $in->model('commerce', BrandMessageCommerce::class),
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
            'button' => Serialize::models($this->button),
            'image' => $this->image?->toArray(),
            'coupon' => $this->coupon?->toArray(),
            'commerce' => $this->commerce?->toArray(),
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
        return Mask::describe('BrandMessageCarouselItemAttachment', get_object_vars($this));
    }
}
