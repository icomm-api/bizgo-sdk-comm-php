<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드 프로필 노출 일자별 통계 1건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class InsightRcsBrandProfileStat implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['statDate', 'chatbotId', 'displayedCount'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $statDate 통계 일자(yyyyMMdd)입니다.
     * @param string|null $chatbotId 챗봇 ID입니다.
     * @param int|null $displayedCount 노출 건수입니다.
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
         * 챗봇 ID입니다.
         */
        public ?string $chatbotId = null,
        /**
         * 노출 건수입니다.
         */
        public ?int $displayedCount = null,
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
            chatbotId: $in->string('chatbotId'),
            displayedCount: $in->int('displayedCount'),
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
            'chatbotId' => $this->chatbotId,
            'displayedCount' => $this->displayedCount,
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
        return Mask::describe('InsightRcsBrandProfileStat', get_object_vars($this));
    }
}
