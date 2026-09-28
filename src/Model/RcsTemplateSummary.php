<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * RCS 템플릿 목록 항목입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateSummary implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'messagebaseId',
        'formatId',
        'templateName',
        'brandId',
        'status',
        'approvalResult',
        'approvalReason',
        'registerDate',
        'approvalDate',
        'updateDate',
        'registerId',
        'updateId',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $messagebaseId 메시지베이스 ID입니다.
     * @param string|null $formatId 템플릿 양식 ID입니다.
     * @param string|null $templateName 템플릿 이름입니다.
     * @param string|null $brandId 브랜드 ID입니다.
     * @param string|null $status 템플릿의 상태입니다.
     * @param string|null $approvalResult 템플릿의 승인 상태입니다.
     * @param string|null $approvalReason 승인 사유입니다.
     * @param string|null $registerDate 템플릿 등록일시입니다.
     * @param string|null $approvalDate 템플릿 승인일시입니다.
     * @param string|null $updateDate 템플릿 수정일시입니다.
     * @param string|null $registerId 템플릿 등록 계정 ID입니다.
     * @param string|null $updateId 템플릿 수정 계정 ID입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 메시지베이스 ID입니다. 발송 시에는 이 값을 `formatId` 필드에 넣습니다(영문 문서 기준).
         */
        public ?string $messagebaseId = null,
        /**
         * 템플릿 양식 ID입니다.
         */
        public ?string $formatId = null,
        /**
         * 템플릿 이름입니다.
         */
        public ?string $templateName = null,
        /**
         * 브랜드 ID입니다.
         */
        public ?string $brandId = null,
        /**
         * 템플릿의 상태입니다. 알려진 값(영문 문서 기준)은 `ready`(사용 중), `pause`(중지)입니다.
         *
         * 알려진 값: `ready`, `pause`
         */
        public ?string $status = null,
        /**
         * 템플릿의 승인 상태입니다. 문서 예시 값은 `approved`입니다.
         */
        public ?string $approvalResult = null,
        /**
         * 승인 사유입니다.
         */
        public ?string $approvalReason = null,
        /**
         * 템플릿 등록일시입니다.
         */
        public ?string $registerDate = null,
        /**
         * 템플릿 승인일시입니다.
         */
        public ?string $approvalDate = null,
        /**
         * 템플릿 수정일시입니다.
         */
        public ?string $updateDate = null,
        /**
         * 템플릿 등록 계정 ID입니다.
         */
        public ?string $registerId = null,
        /**
         * 템플릿 수정 계정 ID입니다.
         */
        public ?string $updateId = null,
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
            messagebaseId: $in->string('messagebaseId'),
            formatId: $in->string('formatId'),
            templateName: $in->string('templateName'),
            brandId: $in->string('brandId'),
            status: $in->string('status'),
            approvalResult: $in->string('approvalResult'),
            approvalReason: $in->string('approvalReason'),
            registerDate: $in->string('registerDate'),
            approvalDate: $in->string('approvalDate'),
            updateDate: $in->string('updateDate'),
            registerId: $in->string('registerId'),
            updateId: $in->string('updateId'),
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
            'messagebaseId' => $this->messagebaseId,
            'formatId' => $this->formatId,
            'templateName' => $this->templateName,
            'brandId' => $this->brandId,
            'status' => $this->status,
            'approvalResult' => $this->approvalResult,
            'approvalReason' => $this->approvalReason,
            'registerDate' => $this->registerDate,
            'approvalDate' => $this->approvalDate,
            'updateDate' => $this->updateDate,
            'registerId' => $this->registerId,
            'updateId' => $this->updateId,
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
        return Mask::describe('RcsTemplateSummary', get_object_vars($this));
    }
}
