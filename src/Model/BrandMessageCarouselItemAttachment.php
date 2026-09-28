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
 * 캐러셀 아이템 첨부 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCarouselItemAttachment implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['button', 'image', 'coupon', 'commerce'];

    /**
     * @param list<BrandMessageButton>|null $button 캐러셀 아이템 버튼 목록입니다.
     * @param BrandMessageImage|null $image
     * @param BrandMessageCoupon|null $coupon
     * @param BrandMessageCommerce|null $commerce
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 캐러셀 아이템 버튼 목록입니다. 최대 5개이며, FC/FA 타입에 쿠폰을 함께 쓰면 최대 4개입니다.
         *
         * 항목 최대 5개
         */
        #[\SensitiveParameter]
        public ?array $button = null,
        #[\SensitiveParameter]
        public ?BrandMessageImage $image = null,
        #[\SensitiveParameter]
        public ?BrandMessageCoupon $coupon = null,
        #[\SensitiveParameter]
        public ?BrandMessageCommerce $commerce = null,
    ) {
        Check::all(
            Check::items('button', $button, null, 5),
            Check::listOf('button', $button, [BrandMessageButton::class]),
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
        $button = $in->modelList('button', BrandMessageButton::class);
        $image = $in->model('image', BrandMessageImage::class);
        $coupon = $in->model('coupon', BrandMessageCoupon::class);
        $commerce = $in->model('commerce', BrandMessageCommerce::class);

        return $in->create(static fn (): static => new static(
            button: $button,
            image: $image,
            coupon: $coupon,
            commerce: $commerce,
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
            'button' => Serialize::models($this->button),
            'image' => $this->image?->toArray(),
            'coupon' => $this->coupon?->toArray(),
            'commerce' => $this->commerce?->toArray(),
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
            'button' => $this->button,
            'image' => $this->image,
            'coupon' => $this->coupon,
            'commerce' => $this->commerce,
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
        return Mask::describe('BrandMessageCarouselItemAttachment', get_object_vars($this));
    }
}
