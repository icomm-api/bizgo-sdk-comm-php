<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 공통 포맷 또는 템플릿 양식의 상세 정보입니다. 공통 포맷 상세 조회는 모든 필드를,
 * 템플릿 양식 상세 조회는 그중 일부(formatId, templateName, body, buttons, agencyId, brandId,
 * messagebaseformId, policyInfo.maxButtonCount/maxMediaSize, registerDate, updateDate, productCode,
 * spec, cardType)를 문서화합니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsMessagebaseFormDetail implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'formatId',
        'templateName',
        'body',
        'buttons',
        'agencyId',
        'brandId',
        'messagebaseformId',
        'policyInfo',
        'status',
        'approvalResult',
        'registerDate',
        'approvalDate',
        'updateDate',
        'updateId',
        'productCode',
        'spec',
        'cardType',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $formatId 템플릿 포맷 ID입니다.
     * @param string|null $templateName 템플릿 등록 시 입력된 템플릿 명칭입니다.
     * @param list<Response\RcsTemplateBodyParam>|null $body body 정의입니다.
     * @param Response\RcsTemplateButton|null $buttons
     * @param string|null $agencyId 대행사 ID입니다.
     * @param string|null $brandId 브랜드 ID입니다.
     * @param string|null $messagebaseformId 템플릿의 원형인 템플릿 양식 ID입니다.
     * @param RcsMessagebasePolicyInfo|null $policyInfo
     * @param string|null $status 템플릿의 상태입니다.
     * @param string|null $approvalResult 템플릿의 승인 상태입니다.
     * @param string|null $registerDate 템플릿 등록일시입니다.
     * @param string|null $approvalDate 템플릿 승인일시입니다.
     * @param string|null $updateDate 템플릿 수정일시입니다.
     * @param string|null $updateId 템플릿 수정 계정 ID입니다.
     * @param string|null $productCode 메시지 상품 종류입니다.
     * @param string|null $spec 레이아웃 구조입니다.
     * @param string|null $cardType 카드 종류입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 템플릿 포맷 ID입니다.
         */
        public ?string $formatId = null,
        /**
         * 템플릿 등록 시 입력된 템플릿 명칭입니다.
         */
        public ?string $templateName = null,
        /**
         * body 정의입니다.
         *
         * 확인 필요(x-unverified): 템플릿 양식 상세 조회 문서에는 body 항목의 하위 필드가 없습니다(공통 포맷 상세와 같은 구조로 가정).
         */
        public ?array $body = null,
        /**
         *
         *
         * 확인 필요(x-unverified): 표에는 Object(suggestions 배열 포함)로 표기되어 있으나 템플릿 양식 상세 응답 예시는 배열(`[]`)이고, 템플릿 등록
         * 요청의 buttons는 Object Array입니다. suggestions 항목의 하위 필드도 조회 응답에는 문서화되어 있지 않습니다.
         */
        public ?Response\RcsTemplateButton $buttons = null,
        /**
         * 대행사 ID입니다.
         */
        public ?string $agencyId = null,
        /**
         * 브랜드 ID입니다.
         */
        public ?string $brandId = null,
        /**
         * 템플릿의 원형인 템플릿 양식 ID입니다.
         */
        public ?string $messagebaseformId = null,
        public ?RcsMessagebasePolicyInfo $policyInfo = null,
        /**
         * 템플릿의 상태입니다.
         */
        public ?string $status = null,
        /**
         * 템플릿의 승인 상태입니다.
         */
        public ?string $approvalResult = null,
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
         * 템플릿 수정 계정 ID입니다.
         */
        public ?string $updateId = null,
        /**
         * 메시지 상품 종류입니다.
         */
        public ?string $productCode = null,
        /**
         * 레이아웃 구조입니다.
         */
        public ?string $spec = null,
        /**
         * 카드 종류입니다.
         */
        public ?string $cardType = null,
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
            formatId: $in->string('formatId'),
            templateName: $in->string('templateName'),
            body: $in->modelList('body', Response\RcsTemplateBodyParam::class),
            buttons: $in->model('buttons', Response\RcsTemplateButton::class),
            agencyId: $in->string('agencyId'),
            brandId: $in->string('brandId'),
            messagebaseformId: $in->string('messagebaseformId'),
            policyInfo: $in->model('policyInfo', RcsMessagebasePolicyInfo::class),
            status: $in->string('status'),
            approvalResult: $in->string('approvalResult'),
            registerDate: $in->string('registerDate'),
            approvalDate: $in->string('approvalDate'),
            updateDate: $in->string('updateDate'),
            updateId: $in->string('updateId'),
            productCode: $in->string('productCode'),
            spec: $in->string('spec'),
            cardType: $in->string('cardType'),
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
            'formatId' => $this->formatId,
            'templateName' => $this->templateName,
            'body' => Serialize::models($this->body),
            'buttons' => $this->buttons?->toArray(),
            'agencyId' => $this->agencyId,
            'brandId' => $this->brandId,
            'messagebaseformId' => $this->messagebaseformId,
            'policyInfo' => $this->policyInfo?->toArray(),
            'status' => $this->status,
            'approvalResult' => $this->approvalResult,
            'registerDate' => $this->registerDate,
            'approvalDate' => $this->approvalDate,
            'updateDate' => $this->updateDate,
            'updateId' => $this->updateId,
            'productCode' => $this->productCode,
            'spec' => $this->spec,
            'cardType' => $this->cardType,
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
        return Mask::describe('RcsMessagebaseFormDetail', get_object_vars($this));
    }
}
