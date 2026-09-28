<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model\Response;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;
use Bizgo\Model\ResponseModel;

/**
 * RCS 대화방(챗봇) 정보입니다.
 *
 * 확인 필요(x-unverified): 일시 필드(approvalDate, registerDate, updateDate)의 형식이 문서화되어 있지 않습니다(예시는
 * `yyyy-MM-dd HH:mm:ss`). service·display·inputField·rcsReply·searchWeight·status·approvalResult 값 목록도
 * 없습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsChatbot implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'chatbotId',
        'brandId',
        'groupId',
        'mdn',
        'subNum',
        'isMainNum',
        'subTitle',
        'subDescr',
        'service',
        'display',
        'inputField',
        'botAgencyId',
        'saftyStatusYn',
        'psMenuUse',
        'persistentMenu',
        'rcsReply',
        'webhook',
        'searchWeight',
        'botTcPage',
        'mediaUrl',
        'status',
        'approvalResult',
        'approvalReason',
        'approvalDate',
        'registerDate',
        'registerId',
        'updateDate',
        'updateId',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $chatbotId 대화방 ID입니다.
     * @param string|null $brandId 대화방이 속한 브랜드 ID입니다.
     * @param string|null $groupId 대화방이 속한 그룹 ID입니다.
     * @param string|null $mdn 대화방에 연결된 발신번호입니다.
     * @param string|null $subNum 대화방에 연결된 부가번호입니다.
     * @param string|null $isMainNum 대표번호 여부입니다.
     * @param string|null $subTitle 대화방 부제목입니다.
     * @param string|null $subDescr 대화방 부가 설명입니다.
     * @param string|null $service 대화방의 서비스 구분 값입니다.
     * @param string|null $display 대화방 노출 설정 값입니다.
     * @param int|null $inputField 대화방 입력창 사용 설정 값입니다.
     * @param string|null $botAgencyId 대화방을 등록한 대행사 ID입니다.
     * @param string|null $saftyStatusYn 안심마크 지정 기업 여부입니다.
     * @param string|null $psMenuUse 고정 메뉴 사용 여부입니다.
     * @param array<string, mixed>|null $persistentMenu 대화방 고정 메뉴 구성 정보입니다.
     * @param string|null $rcsReply 대화방 응답 설정 값입니다.
     * @param string|null $webhook 대화방 이벤트를 수신할 웹훅 URL입니다.
     * @param string|null $searchWeight 대화방 검색 가중치입니다.
     * @param string|null $botTcPage 대화방 이용약관 페이지 주소입니다.
     * @param array<string, mixed>|null $mediaUrl 대화방 이미지 파일 ID와 URL 및 사용 유형 정보입니다.
     * @param string|null $status 대화방의 상태입니다.
     * @param string|null $approvalResult 대화방 승인 결과입니다.
     * @param string|null $approvalReason 검수 시 반려 사유입니다.
     * @param string|null $approvalDate 승인일시입니다.
     * @param string|null $registerDate 등록일시입니다.
     * @param string|null $registerId 등록자 ID입니다.
     * @param string|null $updateDate 수정일시입니다.
     * @param string|null $updateId 수정자 ID입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 대화방 ID입니다.
         */
        public ?string $chatbotId = null,
        /**
         * 대화방이 속한 브랜드 ID입니다.
         */
        public ?string $brandId = null,
        /**
         * 대화방이 속한 그룹 ID입니다.
         */
        public ?string $groupId = null,
        /**
         * 대화방에 연결된 발신번호입니다.
         */
        public ?string $mdn = null,
        /**
         * 대화방에 연결된 부가번호입니다.
         */
        public ?string $subNum = null,
        /**
         * 대표번호 여부입니다. `Y` 대표번호, `N` 부가번호입니다(영문 문서 기준).
         *
         * 값: `Y`, `N`
         */
        public ?string $isMainNum = null,
        /**
         * 대화방 부제목입니다.
         */
        public ?string $subTitle = null,
        /**
         * 대화방 부가 설명입니다.
         */
        public ?string $subDescr = null,
        /**
         * 대화방의 서비스 구분 값입니다.
         */
        public ?string $service = null,
        /**
         * 대화방 노출 설정 값입니다.
         */
        public ?string $display = null,
        /**
         * 대화방 입력창 사용 설정 값입니다.
         */
        public ?int $inputField = null,
        /**
         * 대화방을 등록한 대행사 ID입니다.
         */
        public ?string $botAgencyId = null,
        /**
         * 안심마크 지정 기업 여부입니다. 필드명은 문서 표기(`safty`)를 따릅니다.
         */
        public ?string $saftyStatusYn = null,
        /**
         * 고정 메뉴 사용 여부입니다. `Y` 사용, `N` 미사용입니다(영문 문서 기준).
         *
         * 값: `Y`, `N`
         */
        public ?string $psMenuUse = null,
        /**
         * 대화방 고정 메뉴 구성 정보입니다.
         *
         * 확인 필요(x-unverified): 하위 필드가 문서화되어 있지 않습니다.
         */
        public ?array $persistentMenu = null,
        /**
         * 대화방 응답 설정 값입니다.
         */
        public ?string $rcsReply = null,
        /**
         * 대화방 이벤트를 수신할 웹훅 URL입니다.
         */
        public ?string $webhook = null,
        /**
         * 대화방 검색 가중치입니다.
         */
        public ?string $searchWeight = null,
        /**
         * 대화방 이용약관 페이지 주소입니다.
         */
        public ?string $botTcPage = null,
        /**
         * 대화방 이미지 파일 ID와 URL 및 사용 유형 정보입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object이지만 하위 필드가 없습니다. 브랜드의 mediaUrl은 Object
         * Array(fileId/url/typeName/fileName)라서 같은 구조의 배열일 가능성이 있습니다.
         */
        public ?array $mediaUrl = null,
        /**
         * 대화방의 상태입니다. 문서 예시 값은 `ready`입니다.
         */
        public ?string $status = null,
        /**
         * 대화방 승인 결과입니다. 문서 예시 값은 `approved`입니다.
         */
        public ?string $approvalResult = null,
        /**
         * 검수 시 반려 사유입니다.
         */
        public ?string $approvalReason = null,
        /**
         * 승인일시입니다.
         */
        public ?string $approvalDate = null,
        /**
         * 등록일시입니다.
         */
        public ?string $registerDate = null,
        /**
         * 등록자 ID입니다.
         */
        public ?string $registerId = null,
        /**
         * 수정일시입니다.
         */
        public ?string $updateDate = null,
        /**
         * 수정자 ID입니다.
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
            chatbotId: $in->string('chatbotId'),
            brandId: $in->string('brandId'),
            groupId: $in->string('groupId'),
            mdn: $in->string('mdn'),
            subNum: $in->string('subNum'),
            isMainNum: $in->string('isMainNum'),
            subTitle: $in->string('subTitle'),
            subDescr: $in->string('subDescr'),
            service: $in->string('service'),
            display: $in->string('display'),
            inputField: $in->int('inputField'),
            botAgencyId: $in->string('botAgencyId'),
            saftyStatusYn: $in->string('saftyStatusYn'),
            psMenuUse: $in->string('psMenuUse'),
            persistentMenu: $in->object('persistentMenu'),
            rcsReply: $in->string('rcsReply'),
            webhook: $in->string('webhook'),
            searchWeight: $in->string('searchWeight'),
            botTcPage: $in->string('botTcPage'),
            mediaUrl: $in->object('mediaUrl'),
            status: $in->string('status'),
            approvalResult: $in->string('approvalResult'),
            approvalReason: $in->string('approvalReason'),
            approvalDate: $in->string('approvalDate'),
            registerDate: $in->string('registerDate'),
            registerId: $in->string('registerId'),
            updateDate: $in->string('updateDate'),
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
            'chatbotId' => $this->chatbotId,
            'brandId' => $this->brandId,
            'groupId' => $this->groupId,
            'mdn' => $this->mdn,
            'subNum' => $this->subNum,
            'isMainNum' => $this->isMainNum,
            'subTitle' => $this->subTitle,
            'subDescr' => $this->subDescr,
            'service' => $this->service,
            'display' => $this->display,
            'inputField' => $this->inputField,
            'botAgencyId' => $this->botAgencyId,
            'saftyStatusYn' => $this->saftyStatusYn,
            'psMenuUse' => $this->psMenuUse,
            'persistentMenu' => $this->persistentMenu,
            'rcsReply' => $this->rcsReply,
            'webhook' => $this->webhook,
            'searchWeight' => $this->searchWeight,
            'botTcPage' => $this->botTcPage,
            'mediaUrl' => $this->mediaUrl,
            'status' => $this->status,
            'approvalResult' => $this->approvalResult,
            'approvalReason' => $this->approvalReason,
            'approvalDate' => $this->approvalDate,
            'registerDate' => $this->registerDate,
            'registerId' => $this->registerId,
            'updateDate' => $this->updateDate,
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
        return Mask::describe('RcsChatbot', get_object_vars($this));
    }
}
