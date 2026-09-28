<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\RequiredIf;
use Bizgo\Internal\Serialize;

/**
 * 카카오 브랜드메시지입니다. 카카오톡 채널 친구에게 브랜드형 메시지를 발송합니다. `sendType`으로 발송 방식을 구분합니다.
 *
 * | sendType | 방식 | 필수 필드 |
 * | --- | --- | --- |
 * | `basic` | 기본형. 사전 승인된 템플릿으로 발송합니다. 변수 분리 방식(`messageVariable` 등 `*Variable` 필드) 또는 전문 방식(`text`, `attachment`, `carousel`)을 템플릿 구조와 `msgType`에 맞게 선택해 씁니다. Variable 필드는 변수 치환이 필요할 때만 씁니다. | `sendType`, `msgType`, `senderKey`, `templateCode`, `targeting` |
 * | `template` | 기본형 템플릿 자동 치환. 템플릿 코드와 `destinations[].replaceWords`만으로 발송하며 Bizgo API가 전문을 생성합니다. 사용 가능 필드는 `senderKey`, `templateCode`, `sendType`, `targeting`, `pushAlarm`, `originCID`, `unsubscribePhoneNumber`, `unsubscribeAuthNumber`입니다. | `sendType`, `senderKey`, `templateCode`, `targeting` (+ `destinations[].replaceWords`) |
 * | `free` | 자유형. 템플릿 없이 본문·버튼·이미지·캐러셀 등을 직접 구성합니다. `templateCode`와 `*Variable` 필드는 쓰지 않으며, `groupTagKey`·`adult`·`adFlag`는 자유형에서만 씁니다. | `sendType`, `msgType`, `senderKey` |
 *
 * 위 표의 `sendType`별 필수 필드는 `x-sdk-required-if`에 있으며 SDK가 보내기 전에 검사합니다. 아래 `msgType`별 요소는 설명으로만 두고 SDK가
 * 검사하지 않습니다(`msgType` 값 확인 전).
 *
 * `msgType`별 주요 요소(문서의 필드 제약에서 정리):
 * - `FT`: 본문 최대 1,300자
 * - `FI`: `attachment.image` 필수, 본문 최대 400자
 * - `FW`: 와이드 이미지. `attachment.image` 필수, 본문 최대 76자
 * - `FL`: 와이드 리스트. `attachment.item`
 * - `FP`: 동영상. `attachment.video`, 본문 최대 76자
 * - `FM`: 커머스. `attachment.image` 필수, `attachment.commerce`, `additionalContent`
 * - `FC`: 캐러셀 피드. `carousel`, 아이템별 `header`·`message`·`attachment.image` 필수, `carousel.head` 사용 불가
 * - `FA`: 캐러셀 커머스. `carousel`, 아이템별 `attachment.image` 필수·`attachment.commerce`, 아이템
 *   `header`·`message`·`additionalContent` 사용 불가
 * - `FG`: 카탈로그. `header`(헤더 타이틀)·`attachment.catalog`(아이템 3~7개) 필수, `headerDescription` 사용. 친구톡 호환
 *   모드로는 발송할 수 없고, PC톡·맥톡에서는 노출되지 않으며 카카오톡 v25.8.0 이상에서 확인할 수 있습니다. 기본형으로 등록한 카탈로그 템플릿은 `targeting`
 *   `M`·`N`으로 발송할 수 있습니다.
 *
 * `messageFlow[].brandmessage`로 보냅니다. `$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.
 *
 * 조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): `sendType` in `[basic, free]`이면
 * `msgType`; `sendType` in `[basic, template]`이면 `templateCode`, `targeting`; `sendType` ==
 * `template`이면 `$.destinations[].replaceWords`.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessage implements ChannelMessage
{
    /** `messageFlow` channel key of this message. */
    public const CHANNEL_KEY = 'brandmessage';

    /** API field names, in spec order. */
    public const FIELDS = [
        'sendType',
        'msgType',
        'senderKey',
        'templateCode',
        'targeting',
        'text',
        'header',
        'headerDescription',
        'additionalContent',
        'groupTagKey',
        'adult',
        'pushAlarm',
        'adFlag',
        'messageVariable',
        'buttonVariable',
        'couponVariable',
        'imageVariable',
        'videoVariable',
        'commerceVariable',
        'carouselVariable',
        'catalogVariable',
        'originCID',
        'unsubscribePhoneNumber',
        'unsubscribeAuthNumber',
        'attachment',
        'carousel',
    ];

    /**
     * @param string $sendType 브랜드메시지 발송 타입입니다.
     * @param string $senderKey 발신프로필 키입니다.
     * @param string|null $msgType 카카오 비즈메시지(브랜드메시지) 타입입니다.
     * @param string|null $templateCode 브랜드메시지 템플릿 코드입니다.
     * @param string|null $targeting 고객사의 광고성 정보 수신동의 회원 대상 타겟팅입니다.
     * @param string|null $text 브랜드메시지 본문입니다.
     * @param string|null $header 헤더 정보입니다.
     * @param string|null $headerDescription 헤더 디스크립션입니다.
     * @param string|null $additionalContent 부가 정보입니다.
     * @param string|null $groupTagKey 그룹태그 등록으로 발급받은 키입니다.
     * @param string|null $adult 성인 대상 메시지 여부입니다.
     * @param string|null $pushAlarm 메시지 도착 시 푸시 알림 발송 여부입니다.
     * @param string|null $adFlag 광고성 메시지 필수 표기 사항 노출 여부입니다.
     * @param array<string, mixed>|null $messageVariable 메시지 영역 변수 정의입니다.
     * @param array<string, mixed>|null $buttonVariable 버튼 영역 변수 정의입니다.
     * @param array<string, mixed>|null $couponVariable 쿠폰 영역 변수 정의입니다.
     * @param array<string, mixed>|null $imageVariable 이미지 영역 변수 정의입니다.
     * @param array<string, mixed>|null $videoVariable 비디오 영역 변수 정의입니다.
     * @param array<string, mixed>|null $commerceVariable 커머스 영역 변수 정의입니다.
     * @param list<BrandMessageCarouselVariable>|null $carouselVariable 캐러셀 영역 변수 목록입니다.
     * @param list<BrandMessageCatalogVariable>|null $catalogVariable FG(카탈로그) 전용 아이템 단위 변수입니다.
     * @param string|null $originCID 최초 발신사업자 식별코드(최대 9자)입니다.
     * @param string|null $unsubscribePhoneNumber 무료 수신거부 전화번호입니다.
     * @param string|null $unsubscribeAuthNumber 무료 수신거부 인증번호입니다.
     * @param BrandMessageAttachment|null $attachment
     * @param BrandMessageCarousel|null $carousel
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 브랜드메시지 발송 타입입니다.
         * - `basic`: 기본형 발송
         * - `template`: 기본형 템플릿 자동 치환 발송
         * - `free`: 자유형 발송
         *
         * 필수 · 허용 값: `basic`, `template`, `free`
         */
        #[\SensitiveParameter]
        public string $sendType,
        /**
         * 발신프로필 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 카카오 비즈메시지(브랜드메시지) 타입입니다. `basic`, `free` 발송 시 필수입니다(`template`에서는 생략).
         * 카카오 원본 chatBubbleType으로 변환됩니다. 알려진 값은 `FT`, `FI`, `FW`, `FL`, `FC`, `FM`, `FA`, `FP`, `FG`입니다.
         *
         * 알려진 값: `FT`, `FI`, `FW`, `FL`, `FC`, `FM`, `FA`, `FP`, `FG`
         *
         * 확인 필요(x-unverified): 문서의 요청 예시는 msgType에 `TEXT`를 쓰지만 필드 설명·템플릿 규격은 `FT` 등 2자리 코드를 씁니다. 각 코드의
         * 명칭(와이드/커머스 등)은 필드 제약과 이미지 업로드 API 이름에서 추정한 것입니다.
         */
        #[\SensitiveParameter]
        public ?string $msgType = null,
        /**
         * 브랜드메시지 템플릿 코드입니다. `basic`, `template` 발송 시 필수이며 `free`에서는 쓰지 않습니다.
         */
        #[\SensitiveParameter]
        public ?string $templateCode = null,
        /**
         * 고객사의 광고성 정보 수신동의 회원 대상 타겟팅입니다. `basic`, `template` 발송 시 필수, `free`에서는 선택입니다. 허용된(allowlist)
         * 발신프로필만 사용할 수 있습니다.
         * - `M`: 광고성 정보 수신동의 회원
         * - `N`: 광고성 정보 수신동의 회원 − 채널 친구
         * - `O`: 광고성 정보 수신동의 회원 ∩ 채널 친구 (수신거부 방식: 080 번호 안내)
         * - `I`: 광고성 정보 수신동의 회원 ∩ 채널 친구 (수신거부 방식: 채널 차단 안내)
         *
         * 허용 값: `M`, `N`, `O`, `I`
         */
        #[\SensitiveParameter]
        public ?string $targeting = null,
        /**
         * 브랜드메시지 본문입니다. 최대 글자 수는 `msgType`에 따라 다릅니다(FT 1,300자, FI 400자, FW/FP 76자).
         *
         * 최대 1300자
         *
         * 확인 필요(x-unverified): 자유형 발송 요청 예시는 본문을 `text`가 아닌 `content` 필드로 보냅니다. 필드 표(Part A)의 `text`를
         * 따릅니다.
         */
        #[\SensitiveParameter]
        public ?string $text = null,
        /**
         * 헤더 정보입니다. 최대 20자입니다. `FG`(카탈로그)에서는 헤더 타이틀로 필수입니다.
         *
         * 최대 20자
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        /**
         * 헤더 디스크립션입니다. `msgType`이 `FG`일 때만 사용합니다.
         */
        #[\SensitiveParameter]
        public ?string $headerDescription = null,
        /**
         * 부가 정보입니다. `FM` 타입 전용이며 최대 34자입니다.
         *
         * 최대 34자
         */
        #[\SensitiveParameter]
        public ?string $additionalContent = null,
        /**
         * 그룹태그 등록으로 발급받은 키입니다. 자유형(`free`) 발송에서 씁니다. 최대 40자입니다.
         *
         * 최대 40자
         */
        #[\SensitiveParameter]
        public ?string $groupTagKey = null,
        /**
         * 성인 대상 메시지 여부입니다. 자유형(`free`) 발송에서 씁니다. 기본값은 `N`입니다.
         *
         * 허용 값: `Y`, `N` · 서버 기본값 `'N'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $adult = null,
        /**
         * 메시지 도착 시 푸시 알림 발송 여부입니다. 기본값은 `Y`입니다.
         *
         * 허용 값: `Y`, `N` · 서버 기본값 `'Y'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $pushAlarm = null,
        /**
         * 광고성 메시지 필수 표기 사항 노출 여부입니다. 자유형(`free`) 발송에서 씁니다. 기본값은 `Y`이며, `msgType`이 `FL`, `FC`, `FA`이면 `Y`로만
         * 발송할 수 있습니다.
         *
         * 허용 값: `Y`, `N` · 서버 기본값 `'Y'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $adFlag = null,
        /**
         * 메시지 영역 변수 정의입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FT, FI, FW, FL, FP, FM 타입에서 사용합니다.
         *
         * 확인 필요(x-unverified): 변수 객체의 내부 구조(키·값 형식)가 문서에 없습니다. 기본형 요청 예시는 `messageVariable`을
         * `destinations[]` 안에 넣고 있어 위치도 불명확합니다.
         */
        #[\SensitiveParameter]
        public ?array $messageVariable = null,
        /**
         * 버튼 영역 변수 정의입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FT, FI, FW, FL, FP, FM 타입에서 사용합니다.
         *
         * 확인 필요(x-unverified): 변수 객체의 내부 구조가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?array $buttonVariable = null,
        /**
         * 쿠폰 영역 변수 정의입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FT, FI, FW, FL, FP, FM 타입에서 사용합니다.
         *
         * 확인 필요(x-unverified): 변수 객체의 내부 구조가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?array $couponVariable = null,
        /**
         * 이미지 영역 변수 정의입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FI, FW, FL, FM 타입에서 사용합니다.
         *
         * 확인 필요(x-unverified): 변수 객체의 내부 구조가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?array $imageVariable = null,
        /**
         * 비디오 영역 변수 정의입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FP 타입에서 사용합니다.
         *
         * 확인 필요(x-unverified): 변수 객체의 내부 구조가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?array $videoVariable = null,
        /**
         * 커머스 영역 변수 정의입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FM 타입에서 사용합니다.
         *
         * 확인 필요(x-unverified): 변수 객체의 내부 구조가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?array $commerceVariable = null,
        /**
         * 캐러셀 영역 변수 목록입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다. FC, FA 타입에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?array $carouselVariable = null,
        /**
         * FG(카탈로그) 전용 아이템 단위 변수입니다. 기본형(`basic`) 변수 분리 방식에서 씁니다.
         */
        #[\SensitiveParameter]
        public ?array $catalogVariable = null,
        /**
         * 최초 발신사업자 식별코드(최대 9자)입니다. 재판매사·특수부가통신사업자는 필수입니다.
         *
         * 최대 9자
         */
        #[\SensitiveParameter]
        public ?string $originCID = null,
        /**
         * 무료 수신거부 전화번호입니다. 최대 13자입니다.
         *
         * 최대 13자
         */
        #[\SensitiveParameter]
        public ?string $unsubscribePhoneNumber = null,
        /**
         * 무료 수신거부 인증번호입니다. 최대 10자입니다.
         *
         * 최대 10자
         */
        #[\SensitiveParameter]
        public ?string $unsubscribeAuthNumber = null,
        #[\SensitiveParameter]
        public ?BrandMessageAttachment $attachment = null,
        #[\SensitiveParameter]
        public ?BrandMessageCarousel $carousel = null,
    ) {
        Check::all(
            Check::enum('sendType', $sendType, ['basic', 'template', 'free']),
            Check::enum('targeting', $targeting, ['M', 'N', 'O', 'I']),
            Check::maxLength('text', $text, 1300),
            Check::maxLength('header', $header, 20),
            Check::maxLength('additionalContent', $additionalContent, 34),
            Check::maxLength('groupTagKey', $groupTagKey, 40),
            Check::enum('adult', $adult, ['Y', 'N']),
            Check::enum('pushAlarm', $pushAlarm, ['Y', 'N']),
            Check::enum('adFlag', $adFlag, ['Y', 'N']),
            Check::listOf('carouselVariable', $carouselVariable, [BrandMessageCarouselVariable::class]),
            Check::listOf('catalogVariable', $catalogVariable, [BrandMessageCatalogVariable::class]),
            Check::maxLength('originCID', $originCID, 9),
            Check::maxLength('unsubscribePhoneNumber', $unsubscribePhoneNumber, 13),
            Check::maxLength('unsubscribeAuthNumber', $unsubscribeAuthNumber, 10),
        );
        Check::all(RequiredIf::check('BrandMessage', $this->toArray()));
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
        $sendType = $in->requiredString('sendType');
        $senderKey = $in->requiredString('senderKey');
        $msgType = $in->string('msgType');
        $templateCode = $in->string('templateCode');
        $targeting = $in->string('targeting');
        $text = $in->string('text');
        $header = $in->string('header');
        $headerDescription = $in->string('headerDescription');
        $additionalContent = $in->string('additionalContent');
        $groupTagKey = $in->string('groupTagKey');
        $adult = $in->string('adult');
        $pushAlarm = $in->string('pushAlarm');
        $adFlag = $in->string('adFlag');
        $messageVariable = $in->object('messageVariable');
        $buttonVariable = $in->object('buttonVariable');
        $couponVariable = $in->object('couponVariable');
        $imageVariable = $in->object('imageVariable');
        $videoVariable = $in->object('videoVariable');
        $commerceVariable = $in->object('commerceVariable');
        $carouselVariable = $in->modelList('carouselVariable', BrandMessageCarouselVariable::class);
        $catalogVariable = $in->modelList('catalogVariable', BrandMessageCatalogVariable::class);
        $originCID = $in->string('originCID');
        $unsubscribePhoneNumber = $in->string('unsubscribePhoneNumber');
        $unsubscribeAuthNumber = $in->string('unsubscribeAuthNumber');
        $attachment = $in->model('attachment', BrandMessageAttachment::class);
        $carousel = $in->model('carousel', BrandMessageCarousel::class);

        return $in->create(static fn (): static => new static(
            sendType: $sendType,
            senderKey: $senderKey,
            msgType: $msgType,
            templateCode: $templateCode,
            targeting: $targeting,
            text: $text,
            header: $header,
            headerDescription: $headerDescription,
            additionalContent: $additionalContent,
            groupTagKey: $groupTagKey,
            adult: $adult,
            pushAlarm: $pushAlarm,
            adFlag: $adFlag,
            messageVariable: $messageVariable,
            buttonVariable: $buttonVariable,
            couponVariable: $couponVariable,
            imageVariable: $imageVariable,
            videoVariable: $videoVariable,
            commerceVariable: $commerceVariable,
            carouselVariable: $carouselVariable,
            catalogVariable: $catalogVariable,
            originCID: $originCID,
            unsubscribePhoneNumber: $unsubscribePhoneNumber,
            unsubscribeAuthNumber: $unsubscribeAuthNumber,
            attachment: $attachment,
            carousel: $carousel,
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
            'sendType' => $this->sendType,
            'msgType' => $this->msgType,
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'targeting' => $this->targeting,
            'text' => $this->text,
            'header' => $this->header,
            'headerDescription' => $this->headerDescription,
            'additionalContent' => $this->additionalContent,
            'groupTagKey' => $this->groupTagKey,
            'adult' => $this->adult,
            'pushAlarm' => $this->pushAlarm,
            'adFlag' => $this->adFlag,
            'messageVariable' => $this->messageVariable,
            'buttonVariable' => $this->buttonVariable,
            'couponVariable' => $this->couponVariable,
            'imageVariable' => $this->imageVariable,
            'videoVariable' => $this->videoVariable,
            'commerceVariable' => $this->commerceVariable,
            'carouselVariable' => Serialize::models($this->carouselVariable),
            'catalogVariable' => Serialize::models($this->catalogVariable),
            'originCID' => $this->originCID,
            'unsubscribePhoneNumber' => $this->unsubscribePhoneNumber,
            'unsubscribeAuthNumber' => $this->unsubscribeAuthNumber,
            'attachment' => $this->attachment?->toArray(),
            'carousel' => $this->carousel?->toArray(),
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
            'sendType' => $this->sendType,
            'msgType' => $this->msgType,
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'targeting' => $this->targeting,
            'text' => $this->text,
            'header' => $this->header,
            'headerDescription' => $this->headerDescription,
            'additionalContent' => $this->additionalContent,
            'groupTagKey' => $this->groupTagKey,
            'adult' => $this->adult,
            'pushAlarm' => $this->pushAlarm,
            'adFlag' => $this->adFlag,
            'messageVariable' => Serialize::jsonObject($this->messageVariable),
            'buttonVariable' => Serialize::jsonObject($this->buttonVariable),
            'couponVariable' => Serialize::jsonObject($this->couponVariable),
            'imageVariable' => Serialize::jsonObject($this->imageVariable),
            'videoVariable' => Serialize::jsonObject($this->videoVariable),
            'commerceVariable' => Serialize::jsonObject($this->commerceVariable),
            'carouselVariable' => $this->carouselVariable,
            'catalogVariable' => $this->catalogVariable,
            'originCID' => $this->originCID,
            'unsubscribePhoneNumber' => $this->unsubscribePhoneNumber,
            'unsubscribeAuthNumber' => $this->unsubscribeAuthNumber,
            'attachment' => $this->attachment,
            'carousel' => $this->carousel,
        ]);
    }

    public function channelKey(): string
    {
        return self::CHANNEL_KEY;
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
        return Mask::describe('BrandMessage', get_object_vars($this));
    }
}
