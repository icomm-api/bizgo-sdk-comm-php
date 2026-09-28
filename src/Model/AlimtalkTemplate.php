<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 조회·등록·수정 응답의 알림톡 템플릿 정보입니다. 응답에는 요청 필드 외에 검수 상태, 차단·휴면 여부, 심사 의견 등이 더해집니다.
 * 응답에 따라 일부 필드만 올 수 있습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkTemplate implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'senderKeyType',
        'templateName',
        'templateCode',
        'msgType',
        'templateMessageType',
        'templateEmphasizeType',
        'text',
        'title',
        'subTitle',
        'header',
        'imgName',
        'imgUrl',
        'link',
        'attachment',
        'supplement',
        'categoryCode',
        'securityFlag',
        'adultFlag',
        'previewMessage',
        'extra',
        'inspectionStatus',
        'status',
        'block',
        'dormant',
        'createdAt',
        'modifiedAt',
        'comments',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $senderKeyType 발신프로필 키 타입입니다.
     * @param string|null $templateName 템플릿 이름입니다.
     * @param string|null $templateCode 템플릿 코드입니다.
     * @param string|null $msgType 이 템플릿으로 발송할 때 쓰는 카카오 비즈메시지 타입입니다(`AT` 텍스트형, `AI` 이미지형).
     * @param string|null $templateMessageType 템플릿 메시지 타입입니다.
     * @param string|null $templateEmphasizeType 템플릿 강조 타입입니다.
     * @param string|null $text 템플릿 본문입니다.
     * @param string|null $title 강조표기형 템플릿 제목입니다.
     * @param string|null $subTitle 강조 표기 보조 문구입니다.
     * @param string|null $header 메시지 상단에 표시할 제목입니다.
     * @param string|null $imgName 이미지 이름입니다.
     * @param string|null $imgUrl 이미지 URL입니다.
     * @param Response\AlimtalkLink|null $link
     * @param Response\AlimtalkAttachment|null $attachment
     * @param Response\AlimtalkSupplement|null $supplement
     * @param string|null $categoryCode 템플릿 카테고리 코드입니다.
     * @param bool|null $securityFlag 보안 템플릿 여부입니다.
     * @param bool|null $adultFlag 연령 인증 설정 여부입니다.
     * @param string|null $previewMessage 템플릿 미리보기 메시지입니다.
     * @param string|null $extraField 부가 정보입니다.
     * @param string|null $inspectionStatus 검수 상태입니다.
     * @param string|null $status 템플릿 상태입니다.
     * @param string|null $block 템플릿 차단 여부입니다.
     * @param string|null $dormant 템플릿 휴면 여부입니다.
     * @param string|null $createdAt 생성일입니다.
     * @param string|null $modifiedAt 수정일입니다.
     * @param list<AlimtalkTemplateComment>|null $comments 템플릿 심사 의견 목록입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 발신프로필 키 타입입니다. 기본값은 `S`입니다.
         * - `G`: 그룹
         * - `S`: 발신프로필
         *
         * 값: `G`, `S`
         */
        public ?string $senderKeyType = null,
        /**
         * 템플릿 이름입니다.
         */
        public ?string $templateName = null,
        /**
         * 템플릿 코드입니다.
         */
        public ?string $templateCode = null,
        /**
         * 이 템플릿으로 발송할 때 쓰는 카카오 비즈메시지 타입입니다(`AT` 텍스트형, `AI` 이미지형).
         * 전문 발송의 `AlimtalkMessage.msgType`에 그대로 넣습니다. 필드 표에는 없지만 응답에 옵니다(2026-09-28 sandbox 확인).
         *
         * 알려진 값: `AT`, `AI`
         */
        public ?string $msgType = null,
        /**
         * 템플릿 메시지 타입입니다. 문서 예시 값은 `BA`입니다.
         *
         * 알려진 값: `BA`
         */
        public ?string $templateMessageType = null,
        /**
         * 템플릿 강조 타입입니다. 문서 예시 값은 `NONE`입니다.
         *
         * 알려진 값: `NONE`
         */
        public ?string $templateEmphasizeType = null,
        /**
         * 템플릿 본문입니다.
         */
        public ?string $text = null,
        /**
         * 강조표기형 템플릿 제목입니다.
         */
        public ?string $title = null,
        /**
         * 강조 표기 보조 문구입니다.
         */
        public ?string $subTitle = null,
        /**
         * 메시지 상단에 표시할 제목입니다.
         */
        public ?string $header = null,
        /**
         * 이미지 이름입니다.
         */
        public ?string $imgName = null,
        /**
         * 이미지 URL입니다.
         */
        public ?string $imgUrl = null,
        public ?Response\AlimtalkLink $link = null,
        public ?Response\AlimtalkAttachment $attachment = null,
        public ?Response\AlimtalkSupplement $supplement = null,
        /**
         * 템플릿 카테고리 코드입니다.
         */
        public ?string $categoryCode = null,
        /**
         * 보안 템플릿 여부입니다.
         */
        public ?bool $securityFlag = null,
        /**
         * 연령 인증 설정 여부입니다.
         */
        public ?bool $adultFlag = null,
        /**
         * 템플릿 미리보기 메시지입니다.
         */
        public ?string $previewMessage = null,
        /**
         * 부가 정보입니다.
         */
        public ?string $extraField = null,
        /**
         * 검수 상태입니다.
         * - `REG`: 등록
         * - `REQ`: 검수 요청
         * - `APR`: 승인
         * - `REJ`: 반려
         *
         * 값: `REG`, `REQ`, `APR`, `REJ`
         *
         * 확인 필요(x-unverified): 검수 상태 값 목록은 Copy Markdown(Part B)에만 있습니다.
         */
        public ?string $inspectionStatus = null,
        /**
         * 템플릿 상태입니다. 목록 조회·최근 변경 템플릿 조회 응답에 있습니다.
         *
         * 확인 필요(x-unverified): 값 목록이 문서에 없고, 목록 조회 예시는 `A`, 최근 변경 조회 예시는 `APR`로 서로 다릅니다. 검수 요청 문서는 '템플릿
         * 상태가 대기'일 때 요청 가능하다고 하나 대기에 해당하는 코드가 없습니다.
         */
        public ?string $status = null,
        /**
         * 템플릿 차단 여부입니다. `true` 또는 `false` 문자열입니다.
         *
         * 알려진 값: `true`, `false`
         *
         * 확인 필요(x-unverified): 문서상 타입이 Boolean이 아닌 String입니다(발신프로필의 block은 Boolean).
         */
        public ?string $block = null,
        /**
         * 템플릿 휴면 여부입니다. `true` 또는 `false` 문자열입니다.
         *
         * 알려진 값: `true`, `false`
         *
         * 확인 필요(x-unverified): 문서상 타입이 Boolean이 아닌 String입니다(발신프로필의 dormant는 Boolean).
         */
        public ?string $dormant = null,
        /**
         * 생성일입니다.
         *
         * 확인 필요(x-unverified): 날짜 형식이 문서에 없습니다.
         */
        public ?string $createdAt = null,
        /**
         * 수정일입니다.
         *
         * 확인 필요(x-unverified): 날짜 형식이 문서에 없습니다.
         */
        public ?string $modifiedAt = null,
        /**
         * 템플릿 심사 의견 목록입니다.
         */
        public ?array $comments = null,
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
            senderKey: $in->string('senderKey'),
            senderKeyType: $in->string('senderKeyType'),
            templateName: $in->string('templateName'),
            templateCode: $in->string('templateCode'),
            msgType: $in->string('msgType'),
            templateMessageType: $in->string('templateMessageType'),
            templateEmphasizeType: $in->string('templateEmphasizeType'),
            text: $in->string('text'),
            title: $in->string('title'),
            subTitle: $in->string('subTitle'),
            header: $in->string('header'),
            imgName: $in->string('imgName'),
            imgUrl: $in->string('imgUrl'),
            link: $in->model('link', Response\AlimtalkLink::class),
            attachment: $in->model('attachment', Response\AlimtalkAttachment::class),
            supplement: $in->model('supplement', Response\AlimtalkSupplement::class),
            categoryCode: $in->string('categoryCode'),
            securityFlag: $in->bool('securityFlag'),
            adultFlag: $in->bool('adultFlag'),
            previewMessage: $in->string('previewMessage'),
            extraField: $in->string('extra'),
            inspectionStatus: $in->string('inspectionStatus'),
            status: $in->string('status'),
            block: $in->string('block'),
            dormant: $in->string('dormant'),
            createdAt: $in->string('createdAt'),
            modifiedAt: $in->string('modifiedAt'),
            comments: $in->modelList('comments', AlimtalkTemplateComment::class),
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
            'senderKey' => $this->senderKey,
            'senderKeyType' => $this->senderKeyType,
            'templateName' => $this->templateName,
            'templateCode' => $this->templateCode,
            'msgType' => $this->msgType,
            'templateMessageType' => $this->templateMessageType,
            'templateEmphasizeType' => $this->templateEmphasizeType,
            'text' => $this->text,
            'title' => $this->title,
            'subTitle' => $this->subTitle,
            'header' => $this->header,
            'imgName' => $this->imgName,
            'imgUrl' => $this->imgUrl,
            'link' => $this->link?->toArray(),
            'attachment' => $this->attachment?->toArray(),
            'supplement' => $this->supplement?->toArray(),
            'categoryCode' => $this->categoryCode,
            'securityFlag' => $this->securityFlag,
            'adultFlag' => $this->adultFlag,
            'previewMessage' => $this->previewMessage,
            'extra' => $this->extraField,
            'inspectionStatus' => $this->inspectionStatus,
            'status' => $this->status,
            'block' => $this->block,
            'dormant' => $this->dormant,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt,
            'comments' => Serialize::models($this->comments),
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
        return Mask::describe('AlimtalkTemplate', get_object_vars($this));
    }
}
