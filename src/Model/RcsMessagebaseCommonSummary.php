<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 공통 포맷 목록 항목입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsMessagebaseCommonSummary implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'messagebaseformId',
        'formName',
        'bizCondition',
        'cardType',
        'formatId',
        'templateName',
        'brandId',
        'status',
        'approvalResult',
        'updateId',
        'registerDate',
        'approvalDate',
        'updateDate',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $messagebaseformId 템플릿의 원형인 템플릿 양식 ID입니다.
     * @param string|null $formName 포맷 이름입니다.
     * @param list<string>|null $bizCondition 비즈 조건 목록입니다.
     * @param string|null $cardType 카드 유형입니다.
     * @param string|null $formatId 템플릿 포맷 ID입니다.
     * @param string|null $templateName 템플릿 등록 시 입력된 템플릿 명칭입니다.
     * @param string|null $brandId 브랜드 ID입니다.
     * @param string|null $status 템플릿의 상태입니다.
     * @param string|null $approvalResult 템플릿의 승인 상태입니다.
     * @param string|null $updateId 템플릿 수정 계정 ID입니다.
     * @param string|null $registerDate 템플릿 등록일시입니다.
     * @param string|null $approvalDate 템플릿 승인일시입니다.
     * @param string|null $updateDate 템플릿 수정일시입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 템플릿의 원형인 템플릿 양식 ID입니다.
         */
        public ?string $messagebaseformId = null,
        /**
         * 포맷 이름입니다.
         */
        public ?string $formName = null,
        /**
         * 비즈 조건 목록입니다.
         */
        public ?array $bizCondition = null,
        /**
         * 카드 유형입니다. 문서 예시 값은 `standalone media top`입니다.
         */
        public ?string $cardType = null,
        /**
         * 템플릿 포맷 ID입니다.
         */
        public ?string $formatId = null,
        /**
         * 템플릿 등록 시 입력된 템플릿 명칭입니다.
         */
        public ?string $templateName = null,
        /**
         * 브랜드 ID입니다.
         */
        public ?string $brandId = null,
        /**
         * 템플릿의 상태입니다.
         */
        public ?string $status = null,
        /**
         * 템플릿의 승인 상태입니다.
         */
        public ?string $approvalResult = null,
        /**
         * 템플릿 수정 계정 ID입니다.
         */
        public ?string $updateId = null,
        /**
         * 템플릿 등록일시입니다. 문서 예시는 `yyyy-MM-dd'T'HH:mm:ss` 형식입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss`
         */
        public ?string $registerDate = null,
        /**
         * 템플릿 승인일시입니다.
         */
        public ?string $approvalDate = null,
        /**
         * 템플릿 수정일시입니다. 문서 예시는 `yyyy-MM-dd'T'HH:mm:ss` 형식입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss`
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
            bizCondition: $in->stringList('bizCondition'),
            cardType: $in->string('cardType'),
            formatId: $in->string('formatId'),
            templateName: $in->string('templateName'),
            brandId: $in->string('brandId'),
            status: $in->string('status'),
            approvalResult: $in->string('approvalResult'),
            updateId: $in->string('updateId'),
            registerDate: $in->string('registerDate'),
            approvalDate: $in->string('approvalDate'),
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
            'formatId' => $this->formatId,
            'templateName' => $this->templateName,
            'brandId' => $this->brandId,
            'status' => $this->status,
            'approvalResult' => $this->approvalResult,
            'updateId' => $this->updateId,
            'registerDate' => $this->registerDate,
            'approvalDate' => $this->approvalDate,
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
        return Mask::describe('RcsMessagebaseCommonSummary', get_object_vars($this));
    }
}
