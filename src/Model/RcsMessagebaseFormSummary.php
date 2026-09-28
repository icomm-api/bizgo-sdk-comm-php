<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 양식 목록 항목입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsMessagebaseFormSummary implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'messagebaseformId',
        'formName',
        'bizCondition',
        'cardType',
        'bizCategory',
        'bizService',
        'registerDate',
        'updateDate',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $messagebaseformId 템플릿 양식 ID입니다.
     * @param string|null $formName 템플릿 양식명입니다.
     * @param string|null $bizCondition 양식을 사용할 수 있는 대상 업태의 목록입니다.
     * @param string|null $cardType 카드 종류입니다.
     * @param string|null $bizCategory Description, Cell의 유형 그룹입니다.
     * @param string|null $bizService Description, Cell의 세부 유형입니다.
     * @param string|null $registerDate 등록일시입니다.
     * @param string|null $updateDate 수정일시입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 템플릿 양식 ID입니다.
         */
        public ?string $messagebaseformId = null,
        /**
         * 템플릿 양식명입니다.
         */
        public ?string $formName = null,
        /**
         * 양식을 사용할 수 있는 대상 업태의 목록입니다.
         *
         * 확인 필요(x-unverified): 표에는 String으로 표기되어 있으나 응답 예시는 배열(`[]`)이고, 공통 포맷 목록의 같은 필드는 String Array입니다.
         */
        public ?string $bizCondition = null,
        /**
         * 카드 종류입니다.
         */
        public ?string $cardType = null,
        /**
         * Description, Cell의 유형 그룹입니다.
         */
        public ?string $bizCategory = null,
        /**
         * Description, Cell의 세부 유형입니다.
         */
        public ?string $bizService = null,
        /**
         * 등록일시입니다.
         */
        public ?string $registerDate = null,
        /**
         * 수정일시입니다.
         */
        public ?string $updateDate = null,
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
            messagebaseformId: $in->string('messagebaseformId'),
            formName: $in->string('formName'),
            bizCondition: $in->string('bizCondition'),
            cardType: $in->string('cardType'),
            bizCategory: $in->string('bizCategory'),
            bizService: $in->string('bizService'),
            registerDate: $in->string('registerDate'),
            updateDate: $in->string('updateDate'),
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
            'messagebaseformId' => $this->messagebaseformId,
            'formName' => $this->formName,
            'bizCondition' => $this->bizCondition,
            'cardType' => $this->cardType,
            'bizCategory' => $this->bizCategory,
            'bizService' => $this->bizService,
            'registerDate' => $this->registerDate,
            'updateDate' => $this->updateDate,
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
        return Mask::describe('RcsMessagebaseFormSummary', get_object_vars($this));
    }
}
