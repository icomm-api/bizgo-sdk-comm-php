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
final readonly class CounselCarouselItemAttachment implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['buttons', 'image', 'coupon'];

    /**
     * @param CounselImage $image
     * @param list<CounselButton>|null $buttons 캐러셀 아이템 버튼 목록입니다.
     * @param CounselCoupon|null $coupon
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         *
         *
         * 필수
         */
        #[\SensitiveParameter]
        public CounselImage $image,
        /**
         * 캐러셀 아이템 버튼 목록입니다. 쿠폰을 적용하면 최대 4개, 그 외 최대 5개입니다.
         *
         * 항목 최대 5개
         */
        #[\SensitiveParameter]
        public ?array $buttons = null,
        #[\SensitiveParameter]
        public ?CounselCoupon $coupon = null,
    ) {
        Check::all(
            Check::items('buttons', $buttons, null, 5),
            Check::listOf('buttons', $buttons, [CounselButton::class]),
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
        $image = $in->model('image', CounselImage::class, true);
        $buttons = $in->modelList('buttons', CounselButton::class);
        $coupon = $in->model('coupon', CounselCoupon::class);

        return $in->create(static fn (): static => new static(
            image: $image ?? throw new \LogicException('checked by RequestData::create()'),
            buttons: $buttons,
            coupon: $coupon,
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
            'buttons' => Serialize::models($this->buttons),
            'image' => $this->image->toArray(),
            'coupon' => $this->coupon?->toArray(),
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
            'buttons' => $this->buttons,
            'image' => $this->image,
            'coupon' => $this->coupon,
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
        return Mask::describe('CounselCarouselItemAttachment', get_object_vars($this));
    }
}
