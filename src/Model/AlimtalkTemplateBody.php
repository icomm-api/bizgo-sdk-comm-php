<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 알림톡 템플릿 등록·수정 요청의 템플릿 정보(`alimtalk`)입니다.
 * 이미지형·아이템리스트형 템플릿은 템플릿 이미지 업로드(`POST /file/alimtalk/template`)로 받은 `imgUrl`과 이름을 `imgUrl`, `imgName`에
 * 넣습니다.
 * 버튼·바로연결·아이템 구조는 발송 규격의 `AlimtalkButton`, `AlimtalkQuickReply`, `AlimtalkItem` 등과 같습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkTemplateBody implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'senderKeyType',
        'templateName',
        'templateCode',
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
    ];

    /**
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다.
     * @param string $templateName 템플릿 이름입니다.
     * @param string $templateCode 알림톡 템플릿 코드입니다.
     * @param string $templateMessageType 템플릿 메시지 타입입니다.
     * @param string $templateEmphasizeType 템플릿 강조 타입입니다.
     * @param string $text 알림톡 템플릿 본문입니다.
     * @param string|null $senderKeyType 발신프로필 키 타입입니다.
     * @param string|null $title 강조표기형 템플릿 제목입니다.
     * @param string|null $subTitle 강조 표기 보조 문구입니다.
     * @param string|null $header 메시지 상단에 표시할 제목입니다.
     * @param string|null $imgName 템플릿 이미지 파일 이름입니다.
     * @param string|null $imgUrl 템플릿 이미지 URL입니다.
     * @param AlimtalkLink|null $link
     * @param AlimtalkAttachment|null $attachment
     * @param AlimtalkSupplement|null $supplement
     * @param string|null $categoryCode 템플릿 카테고리 코드입니다.
     * @param bool|null $securityFlag 보안 템플릿 여부입니다.
     * @param bool|null $adultFlag 연령 인증 설정 여부입니다.
     * @param string|null $previewMessage 템플릿 미리보기 메시지입니다.
     * @param string|null $extra 부가 정보입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 카카오 비즈메시지 발신프로필 키입니다. `senderKeyType`이 `G`이면 그룹 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 템플릿 이름입니다. 최대 200자입니다.
         *
         * 필수 · 최대 200자
         */
        #[\SensitiveParameter]
        public string $templateName,
        /**
         * 알림톡 템플릿 코드입니다. 최대 30자입니다.
         *
         * 필수 · 최대 30자
         */
        #[\SensitiveParameter]
        public string $templateCode,
        /**
         * 템플릿 메시지 타입입니다. 문서 예시 값은 `BA`입니다.
         *
         * 필수 · 알려진 값: `BA`
         *
         * 확인 필요(x-unverified): 템플릿 메시지 타입 코드 전체 목록이 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $templateMessageType,
        /**
         * 템플릿 강조 타입입니다. 문서 예시 값은 `NONE`입니다.
         *
         * 필수 · 알려진 값: `NONE`
         *
         * 확인 필요(x-unverified): 템플릿 강조 타입 코드 전체 목록과 타입별 필수 필드(title, imgUrl, itemHighlight 등)가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $templateEmphasizeType,
        /**
         * 알림톡 템플릿 본문입니다. 최대 1,300자이며 `#{변수}` 형식의 치환 변수를 쓸 수 있습니다.
         *
         * 필수 · 최대 1300자
         */
        #[\SensitiveParameter]
        public string $text,
        /**
         * 발신프로필 키 타입입니다. 기본값은 `S`입니다.
         * - `G`: 그룹
         * - `S`: 발신프로필
         *
         * 허용 값: `G`, `S` · 서버 기본값 `'S'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $senderKeyType = null,
        /**
         * 강조표기형 템플릿 제목입니다. 최대 50자입니다.
         *
         * 최대 50자
         */
        #[\SensitiveParameter]
        public ?string $title = null,
        /**
         * 강조 표기 보조 문구입니다.
         */
        #[\SensitiveParameter]
        public ?string $subTitle = null,
        /**
         * 메시지 상단에 표시할 제목입니다.
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        /**
         * 템플릿 이미지 파일 이름입니다.
         *
         * 확인 필요(x-unverified): 이미지 업로드 응답은 `fileName`을 돌려주므로 `imgName`에 그 값을 그대로 넣는지 문서에 명시되지 않았습니다.
         */
        #[\SensitiveParameter]
        public ?string $imgName = null,
        /**
         * 템플릿 이미지 URL입니다. 템플릿 이미지 업로드 응답의 `imgUrl`을 씁니다.
         */
        #[\SensitiveParameter]
        public ?string $imgUrl = null,
        #[\SensitiveParameter]
        public ?AlimtalkLink $link = null,
        #[\SensitiveParameter]
        public ?AlimtalkAttachment $attachment = null,
        #[\SensitiveParameter]
        public ?AlimtalkSupplement $supplement = null,
        /**
         * 템플릿 카테고리 코드입니다. 템플릿 카테고리 전체 조회로 확인합니다.
         */
        #[\SensitiveParameter]
        public ?string $categoryCode = null,
        /**
         * 보안 템플릿 여부입니다.
         */
        #[\SensitiveParameter]
        public ?bool $securityFlag = null,
        /**
         * 연령 인증 설정 여부입니다. 수신자가 연령 인증을 마치기 전까지 채팅방에서 메시지가 가려지며 만 20세 이상만 볼 수 있습니다. 기본값은 `false`입니다.
         *
         * 서버 기본값 `false` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?bool $adultFlag = null,
        /**
         * 템플릿 미리보기 메시지입니다.
         */
        #[\SensitiveParameter]
        public ?string $previewMessage = null,
        /**
         * 부가 정보입니다.
         */
        #[\SensitiveParameter]
        public ?string $extra = null,
    ) {
        Check::all(
            Check::maxLength('templateName', $templateName, 200),
            Check::maxLength('templateCode', $templateCode, 30),
            Check::maxLength('text', $text, 1300),
            Check::enum('senderKeyType', $senderKeyType, ['G', 'S']),
            Check::maxLength('title', $title, 50),
        );
    }

    /**
     * Builds the model from an array with API field names (for example decoded JSON).
     * Unknown keys, missing required fields and wrong types are reported together, by field path.
     *
     * @param array<array-key, mixed> $data
     *
     * @throws ValidationException
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new RequestData($data, $path, self::FIELDS);
        $senderKey = $in->requiredString('senderKey');
        $templateName = $in->requiredString('templateName');
        $templateCode = $in->requiredString('templateCode');
        $templateMessageType = $in->requiredString('templateMessageType');
        $templateEmphasizeType = $in->requiredString('templateEmphasizeType');
        $text = $in->requiredString('text');
        $senderKeyType = $in->string('senderKeyType');
        $title = $in->string('title');
        $subTitle = $in->string('subTitle');
        $header = $in->string('header');
        $imgName = $in->string('imgName');
        $imgUrl = $in->string('imgUrl');
        $link = $in->model('link', AlimtalkLink::class);
        $attachment = $in->model('attachment', AlimtalkAttachment::class);
        $supplement = $in->model('supplement', AlimtalkSupplement::class);
        $categoryCode = $in->string('categoryCode');
        $securityFlag = $in->bool('securityFlag');
        $adultFlag = $in->bool('adultFlag');
        $previewMessage = $in->string('previewMessage');
        $extra = $in->string('extra');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            templateName: $templateName,
            templateCode: $templateCode,
            templateMessageType: $templateMessageType,
            templateEmphasizeType: $templateEmphasizeType,
            text: $text,
            senderKeyType: $senderKeyType,
            title: $title,
            subTitle: $subTitle,
            header: $header,
            imgName: $imgName,
            imgUrl: $imgUrl,
            link: $link,
            attachment: $attachment,
            supplement: $supplement,
            categoryCode: $categoryCode,
            securityFlag: $securityFlag,
            adultFlag: $adultFlag,
            previewMessage: $previewMessage,
            extra: $extra,
        ));
    }

    /**
     * The request body with API field names. Only fields you set are included.
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
            'extra' => $this->extra,
        ]);
    }

    /**
     * The value for json_encode(): like toArray(), with maps as JSON objects even when empty.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return Serialize::compact([
            'senderKey' => $this->senderKey,
            'senderKeyType' => $this->senderKeyType,
            'templateName' => $this->templateName,
            'templateCode' => $this->templateCode,
            'templateMessageType' => $this->templateMessageType,
            'templateEmphasizeType' => $this->templateEmphasizeType,
            'text' => $this->text,
            'title' => $this->title,
            'subTitle' => $this->subTitle,
            'header' => $this->header,
            'imgName' => $this->imgName,
            'imgUrl' => $this->imgUrl,
            'link' => $this->link,
            'attachment' => $this->attachment,
            'supplement' => $this->supplement,
            'categoryCode' => $this->categoryCode,
            'securityFlag' => $this->securityFlag,
            'adultFlag' => $this->adultFlag,
            'previewMessage' => $this->previewMessage,
            'extra' => $this->extra,
        ]);
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
        return Mask::describe('AlimtalkTemplateBody', get_object_vars($this));
    }
}
