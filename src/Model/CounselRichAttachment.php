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
 * Rich 메시지 첨부 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselRichAttachment implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['image', 'buttons', 'quickReplies', 'coupon', 'item'];

    /**
     * @param CounselImage|null $image
     * @param list<CounselButton>|null $buttons 버튼 목록입니다.
     * @param list<CounselQuickReply>|null $quickReplies 바로연결 목록입니다.
     * @param CounselCoupon|null $coupon
     * @param CounselItem|null $item
     *
     * @throws ValidationException
     */
    public function __construct(
        #[\SensitiveParameter]
        public ?CounselImage $image = null,
        /**
         * 버튼 목록입니다. TEXT, IMAGE, ITEM_LIST는 쿠폰 적용 시 최대 4개, 그 외 최대 5개이며 WIDE, WIDE_ITEM_LIST,
         * CAROUSEL_FEED는 최대 2개입니다.
         *
         * 항목 최대 5개
         */
        #[\SensitiveParameter]
        public ?array $buttons = null,
        /**
         * 바로연결 목록입니다. 최대 10개입니다.
         *
         * 항목 최대 10개
         */
        #[\SensitiveParameter]
        public ?array $quickReplies = null,
        #[\SensitiveParameter]
        public ?CounselCoupon $coupon = null,
        #[\SensitiveParameter]
        public ?CounselItem $item = null,
    ) {
        Check::all(
            Check::items('buttons', $buttons, null, 5),
            Check::listOf('buttons', $buttons, [CounselButton::class]),
            Check::items('quickReplies', $quickReplies, null, 10),
            Check::listOf('quickReplies', $quickReplies, [CounselQuickReply::class]),
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
        $image = $in->model('image', CounselImage::class);
        $buttons = $in->modelList('buttons', CounselButton::class);
        $quickReplies = $in->modelList('quickReplies', CounselQuickReply::class);
        $coupon = $in->model('coupon', CounselCoupon::class);
        $item = $in->model('item', CounselItem::class);

        return $in->create(static fn (): static => new static(
            image: $image,
            buttons: $buttons,
            quickReplies: $quickReplies,
            coupon: $coupon,
            item: $item,
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
            'image' => $this->image?->toArray(),
            'buttons' => Serialize::models($this->buttons),
            'quickReplies' => Serialize::models($this->quickReplies),
            'coupon' => $this->coupon?->toArray(),
            'item' => $this->item?->toArray(),
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
            'image' => $this->image,
            'buttons' => $this->buttons,
            'quickReplies' => $this->quickReplies,
            'coupon' => $this->coupon,
            'item' => $this->item,
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
        return Mask::describe('CounselRichAttachment', get_object_vars($this));
    }
}
