<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * RCS 메시지 버튼 1개의 클릭 통계입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class InsightRcsButtonClick implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['buttonNum', 'actionType', 'title', 'clickCount'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param int|null $buttonNum 버튼 번호입니다.
     * @param string|null $actionType 버튼 액션 유형입니다.
     * @param string|null $title 버튼 제목입니다.
     * @param int|null $clickCount 클릭 수입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 버튼 번호입니다.
         */
        public ?int $buttonNum = null,
        /**
         * 버튼 액션 유형입니다. 알려진 값은 `urlAction`입니다(발송 규격의 action 키 이름으로 보입니다).
         *
         * 알려진 값: `urlAction`
         */
        public ?string $actionType = null,
        /**
         * 버튼 제목입니다.
         */
        public ?string $title = null,
        /**
         * 클릭 수입니다.
         */
        public ?int $clickCount = null,
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
            buttonNum: $in->int('buttonNum'),
            actionType: $in->string('actionType'),
            title: $in->string('title'),
            clickCount: $in->int('clickCount'),
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
            'buttonNum' => $this->buttonNum,
            'actionType' => $this->actionType,
            'title' => $this->title,
            'clickCount' => $this->clickCount,
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
        return Mask::describe('InsightRcsButtonClick', get_object_vars($this));
    }
}
