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
 * 브랜드메시지 캐러셀 객체입니다(FC 캐러셀 피드, FA 캐러셀 커머스).
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCarousel implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['head', 'list', 'tail'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param BrandMessageCarouselHead|null $head
     * @param list<BrandMessageCarouselItem>|null $list 캐러셀 아이템 목록입니다.
     * @param BrandMessageCarouselTail|null $tail
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        public ?BrandMessageCarouselHead $head = null,
        /**
         * 캐러셀 아이템 목록입니다. 최소 2개, 최대 10개입니다.
         *
         * 항목 2~10개
         */
        public ?array $list = null,
        public ?BrandMessageCarouselTail $tail = null,
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
            head: $in->model('head', BrandMessageCarouselHead::class),
            list: $in->modelList('list', BrandMessageCarouselItem::class),
            tail: $in->model('tail', BrandMessageCarouselTail::class),
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
            'head' => $this->head?->toArray(),
            'list' => Serialize::models($this->list),
            'tail' => $this->tail?->toArray(),
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
        return Mask::describe('BrandMessageCarousel', get_object_vars($this));
    }
}
