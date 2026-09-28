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
 * 아이템 리스트 요소입니다. ITEM_LIST, WIDE_ITEM_LIST 타입에서 필수입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselItem implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['highlight', 'list', 'summary'];

    /**
     * @param list<CounselItemListEntry> $list 아이템 리스트입니다.
     * @param CounselItemHighlight|null $highlight
     * @param CounselItemSummary|null $summary
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 아이템 리스트입니다. ITEM_LIST는 2~10개, WIDE_ITEM_LIST는 3~4개입니다.
         *
         * 필수 · 항목 2~10개
         */
        #[\SensitiveParameter]
        public array $list,
        #[\SensitiveParameter]
        public ?CounselItemHighlight $highlight = null,
        #[\SensitiveParameter]
        public ?CounselItemSummary $summary = null,
    ) {
        Check::all(
            Check::items('list', $list, 2, 10),
            Check::listOf('list', $list, [CounselItemListEntry::class]),
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
        $list = $in->modelList('list', CounselItemListEntry::class, true);
        $highlight = $in->model('highlight', CounselItemHighlight::class);
        $summary = $in->model('summary', CounselItemSummary::class);

        return $in->create(static fn (): static => new static(
            list: $list ?? throw new \LogicException('checked by RequestData::create()'),
            highlight: $highlight,
            summary: $summary,
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
            'highlight' => $this->highlight?->toArray(),
            'list' => Serialize::models($this->list),
            'summary' => $this->summary?->toArray(),
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
            'highlight' => $this->highlight,
            'list' => $this->list,
            'summary' => $this->summary,
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
        return Mask::describe('CounselItem', get_object_vars($this));
    }
}
