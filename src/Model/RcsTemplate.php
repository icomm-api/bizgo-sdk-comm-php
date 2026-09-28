<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * RCS 템플릿 상세 정보입니다.
 *
 * 확인 필요(x-unverified): approvalResult 예시가 목록 조회(approved)와 상세 조회(승인)에서 다릅니다. 일시 형식이 문서화되어 있지 않습니다. 응답에
 * messagebaseId·templateName·brandId·buttons가 없습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplate implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'status',
        'approvalResult',
        'approvalReason',
        'registerDate',
        'approvalDate',
        'updateDate',
        'registerId',
        'updateId',
        'productCode',
        'spec',
        'cardType',
        'body',
        'brandKey',
        'formatId',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $status 템플릿 상태입니다.
     * @param string|null $approvalResult 승인 결과입니다.
     * @param string|null $approvalReason 승인 사유입니다.
     * @param string|null $registerDate 등록일시입니다.
     * @param string|null $approvalDate 승인일시입니다.
     * @param string|null $updateDate 수정일시입니다.
     * @param string|null $registerId 등록 계정 ID입니다.
     * @param string|null $updateId 수정 계정 ID입니다.
     * @param string|null $productCode 상품 코드입니다.
     * @param string|null $spec 템플릿 스펙입니다.
     * @param string|null $cardType 카드 종류입니다.
     * @param RcsTemplateDetailBody|null $body
     * @param string|null $brandKey 브랜드 키입니다.
     * @param string|null $formatId 템플릿 양식 ID입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 템플릿 상태입니다.
         */
        public ?string $status = null,
        /**
         * 승인 결과입니다.
         */
        public ?string $approvalResult = null,
        /**
         * 승인 사유입니다.
         */
        public ?string $approvalReason = null,
        /**
         * 등록일시입니다.
         */
        public ?string $registerDate = null,
        /**
         * 승인일시입니다.
         */
        public ?string $approvalDate = null,
        /**
         * 수정일시입니다.
         */
        public ?string $updateDate = null,
        /**
         * 등록 계정 ID입니다.
         */
        public ?string $registerId = null,
        /**
         * 수정 계정 ID입니다.
         */
        public ?string $updateId = null,
        /**
         * 상품 코드입니다. 문서 예시 값은 `lms`입니다.
         */
        public ?string $productCode = null,
        /**
         * 템플릿 스펙입니다. 문서 예시 값은 `openrichcard`입니다.
         */
        public ?string $spec = null,
        /**
         * 카드 종류입니다. 문서 예시 값은 `descriptionNew`입니다.
         */
        public ?string $cardType = null,
        public ?RcsTemplateDetailBody $body = null,
        /**
         * 브랜드 키입니다.
         */
        public ?string $brandKey = null,
        /**
         * 템플릿 양식 ID입니다. 발송 시에는 이 값을 `formatId` 필드에 넣습니다(영문 문서 기준).
         *
         * 확인 필요(x-unverified): 영문 문서는 messagebaseId와 formatId 모두 발송 시 formatId 필드에 넣는다고 설명해 어느 값을 써야 하는지
         * 불명확합니다.
         */
        public ?string $formatId = null,
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
            status: $in->string('status'),
            approvalResult: $in->string('approvalResult'),
            approvalReason: $in->string('approvalReason'),
            registerDate: $in->string('registerDate'),
            approvalDate: $in->string('approvalDate'),
            updateDate: $in->string('updateDate'),
            registerId: $in->string('registerId'),
            updateId: $in->string('updateId'),
            productCode: $in->string('productCode'),
            spec: $in->string('spec'),
            cardType: $in->string('cardType'),
            body: $in->model('body', RcsTemplateDetailBody::class),
            brandKey: $in->string('brandKey'),
            formatId: $in->string('formatId'),
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
            'status' => $this->status,
            'approvalResult' => $this->approvalResult,
            'approvalReason' => $this->approvalReason,
            'registerDate' => $this->registerDate,
            'approvalDate' => $this->approvalDate,
            'updateDate' => $this->updateDate,
            'registerId' => $this->registerId,
            'updateId' => $this->updateId,
            'productCode' => $this->productCode,
            'spec' => $this->spec,
            'cardType' => $this->cardType,
            'body' => $this->body?->toArray(),
            'brandKey' => $this->brandKey,
            'formatId' => $this->formatId,
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
        return Mask::describe('RcsTemplate', get_object_vars($this));
    }
}
