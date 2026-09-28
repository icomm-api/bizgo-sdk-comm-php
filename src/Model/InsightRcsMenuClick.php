<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 대화방 메뉴 1개의 클릭 통계입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class InsightRcsMenuClick implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['postbackId', 'menuType', 'actionType', 'title', 'clickCount', 'subList'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $postbackId 포스트백 ID입니다.
     * @param string|null $menuType 메뉴 유형입니다.
     * @param string|null $actionType 메뉴 액션 유형입니다.
     * @param string|null $title 메뉴 제목입니다.
     * @param int|null $clickCount 클릭 수입니다.
     * @param list<InsightRcsMenuClick>|null $subList 하위 메뉴 목록입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 포스트백 ID입니다.
         */
        public ?string $postbackId = null,
        /**
         * 메뉴 유형입니다. 알려진 값은 `text`입니다.
         *
         * 알려진 값: `text`
         */
        public ?string $menuType = null,
        /**
         * 메뉴 액션 유형입니다. 알려진 값은 `postbackAction`입니다.
         *
         * 알려진 값: `postbackAction`
         */
        public ?string $actionType = null,
        /**
         * 메뉴 제목입니다.
         */
        public ?string $title = null,
        /**
         * 클릭 수입니다.
         */
        public ?int $clickCount = null,
        /**
         * 하위 메뉴 목록입니다. 상위 메뉴와 같은 항목으로 구성됩니다.
         *
         * 확인 필요(x-unverified): 하위 메뉴가 다시 subList를 가질 수 있는지(중첩 깊이)가 문서에 없습니다.
         */
        public ?array $subList = null,
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
            postbackId: $in->string('postbackId'),
            menuType: $in->string('menuType'),
            actionType: $in->string('actionType'),
            title: $in->string('title'),
            clickCount: $in->int('clickCount'),
            subList: $in->modelList('subList', InsightRcsMenuClick::class),
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
            'postbackId' => $this->postbackId,
            'menuType' => $this->menuType,
            'actionType' => $this->actionType,
            'title' => $this->title,
            'clickCount' => $this->clickCount,
            'subList' => Serialize::models($this->subList),
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
        return Mask::describe('InsightRcsMenuClick', get_object_vars($this));
    }
}
