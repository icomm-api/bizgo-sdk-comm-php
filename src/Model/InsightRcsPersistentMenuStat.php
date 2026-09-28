<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 대화방 메뉴 클릭 일자별 통계 1건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class InsightRcsPersistentMenuStat implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['statDate', 'corpId', 'corpRegNum', 'brandId', 'chatbotId', 'menuList'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $statDate 통계 일자(yyyyMMdd)입니다.
     * @param string|null $corpId 기업 ID입니다.
     * @param string|null $corpRegNum 사업자등록번호입니다.
     * @param string|null $brandId RCS 브랜드 ID입니다.
     * @param string|null $chatbotId 챗봇 ID입니다.
     * @param list<InsightRcsMenuClick>|null $menuList 메뉴별 클릭 상세입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 통계 일자(yyyyMMdd)입니다.
         *
         * 시각 형식 `yyyyMMdd`
         */
        public ?string $statDate = null,
        /**
         * 기업 ID입니다.
         */
        public ?string $corpId = null,
        /**
         * 사업자등록번호입니다.
         */
        public ?string $corpRegNum = null,
        /**
         * RCS 브랜드 ID입니다.
         */
        public ?string $brandId = null,
        /**
         * 챗봇 ID입니다.
         */
        public ?string $chatbotId = null,
        /**
         * 메뉴별 클릭 상세입니다.
         */
        public ?array $menuList = null,
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
            statDate: $in->string('statDate'),
            corpId: $in->string('corpId'),
            corpRegNum: $in->string('corpRegNum'),
            brandId: $in->string('brandId'),
            chatbotId: $in->string('chatbotId'),
            menuList: $in->modelList('menuList', InsightRcsMenuClick::class),
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
            'statDate' => $this->statDate,
            'corpId' => $this->corpId,
            'corpRegNum' => $this->corpRegNum,
            'brandId' => $this->brandId,
            'chatbotId' => $this->chatbotId,
            'menuList' => Serialize::models($this->menuList),
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
        return Mask::describe('InsightRcsPersistentMenuStat', get_object_vars($this));
    }
}
