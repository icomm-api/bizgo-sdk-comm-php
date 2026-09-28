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
 * 브랜드메시지 템플릿 등록·수정 정보입니다. 이미지가 필요한 유형은 이미지 파일 관리 API로 먼저 `imgUrl`을 발급받아 씁니다.
 *
 * 확인 필요(x-unverified): 등록 요청 필드 표에 응답용으로 보이는 `createAt`·`modifiedAt`·`status`가 포함되어 있습니다(문서 그대로 옮김).
 * 변수 객체(`*Variable`) 내부 구조도 문서에 없습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageTemplateInput implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'senderKeyType',
        'sendType',
        'templateName',
        'msgType',
        'templateCode',
        'text',
        'carousel',
        'attachment',
        'header',
        'headerDescription',
        'additionalContent',
        'pushAlarm',
        'messageVariable',
        'buttonVariable',
        'couponVariable',
        'imageVariable',
        'videoVariable',
        'commerceVariable',
        'carouselVariable',
        'catalogVariable',
        'createAt',
        'modifiedAt',
        'status',
    ];

    /**
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다.
     * @param string $senderKeyType 발신프로필 키 타입입니다.
     * @param string $sendType 브랜드메시지 발송 타입입니다.
     * @param string $templateName 템플릿 이름입니다.
     * @param string $msgType 브랜드메시지 메시지 타입입니다.
     * @param string|null $templateCode 템플릿 코드입니다.
     * @param string|null $text 템플릿 본문입니다.
     * @param BrandMessageCarousel|null $carousel 캐러셀 정보입니다(FC, FA).
     * @param BrandMessageAttachment|null $attachment 첨부 정보입니다.
     * @param string|null $header 헤더 정보입니다.
     * @param string|null $headerDescription 헤더 디스크립션입니다.
     * @param string|null $additionalContent 부가 정보입니다.
     * @param string|null $pushAlarm 메시지 푸시 알림 발송 여부입니다.
     * @param array<string, mixed>|null $messageVariable 메시지 영역 변수입니다.
     * @param array<string, mixed>|null $buttonVariable 버튼 영역 변수입니다.
     * @param array<string, mixed>|null $couponVariable 쿠폰 영역 변수입니다.
     * @param array<string, mixed>|null $imageVariable 이미지 영역 변수입니다.
     * @param array<string, mixed>|null $videoVariable 비디오 영역 변수입니다.
     * @param array<string, mixed>|null $commerceVariable 커머스 영역 변수입니다.
     * @param list<BrandMessageCarouselVariable>|null $carouselVariable 캐러셀 영역 변수입니다.
     * @param list<BrandMessageCatalogVariable>|null $catalogVariable FG(카탈로그) 전용 아이템 단위 변수입니다.
     * @param string|null $createAt 등록일입니다.
     * @param string|null $modifiedAt 수정일입니다.
     * @param string|null $status 템플릿 상태입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 카카오 비즈메시지 발신프로필 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 발신프로필 키 타입입니다. 문서 예시 값은 `S`입니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): Part A 필드 표는 필수로 표시하지만 요청 예시와 Part B 필드 표에는 없습니다. 값 목록도 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $senderKeyType,
        /**
         * 브랜드메시지 발송 타입입니다. `basic`(기본형) 또는 `free`(자유형)입니다.
         *
         * 필수 · 허용 값: `basic`, `free`
         *
         * 확인 필요(x-unverified): `basic`/`free` 값 목록은 Part B에만 있습니다. 자유형(`free`) 템플릿의 의미가 문서에 설명되어 있지 않습니다.
         */
        #[\SensitiveParameter]
        public string $sendType,
        /**
         * 템플릿 이름입니다. 최대 200자입니다.
         *
         * 필수 · 최대 200자
         */
        #[\SensitiveParameter]
        public string $templateName,
        /**
         * 브랜드메시지 메시지 타입입니다. 알려진 값: `FT`, `FI`, `FW`, `FL`, `FC`, `FM`, `FA`, `FP`, `FG`(카탈로그).
         *
         * 필수 · 알려진 값: `FT`, `FI`, `FW`, `FL`, `FC`, `FM`, `FA`, `FP`, `FG`
         */
        #[\SensitiveParameter]
        public string $msgType,
        /**
         * 템플릿 코드입니다. 수정 시 대상 템플릿을 지정합니다.
         *
         * 확인 필요(x-unverified): 등록 시 직접 지정할 수 있는지(응답에서 발급되는지), 수정 시 필수인지 문서에 없습니다. 수정 요청 예시에도 templateCode가
         * 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $templateCode = null,
        /**
         * 템플릿 본문입니다.
         */
        #[\SensitiveParameter]
        public ?string $text = null,
        /**
         * 캐러셀 정보입니다(FC, FA).
         */
        #[\SensitiveParameter]
        public ?BrandMessageCarousel $carousel = null,
        /**
         * 첨부 정보입니다. 버튼은 최대 5개, FT/FI에 쿠폰을 함께 쓰면 최대 4개입니다.
         */
        #[\SensitiveParameter]
        public ?BrandMessageAttachment $attachment = null,
        /**
         * 헤더 정보입니다. 최대 20자입니다. `FG`에서는 헤더 타이틀로 씁니다.
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
         * 부가 정보입니다. `msgType`이 `FM`일 때 사용합니다.
         */
        #[\SensitiveParameter]
        public ?string $additionalContent = null,
        /**
         * 메시지 푸시 알림 발송 여부입니다. 기본값은 `Y`입니다.
         *
         * 허용 값: `Y`, `N` · 서버 기본값 `'Y'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $pushAlarm = null,
        /**
         * 메시지 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $messageVariable = null,
        /**
         * 버튼 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $buttonVariable = null,
        /**
         * 쿠폰 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $couponVariable = null,
        /**
         * 이미지 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $imageVariable = null,
        /**
         * 비디오 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $videoVariable = null,
        /**
         * 커머스 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $commerceVariable = null,
        /**
         * 캐러셀 영역 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $carouselVariable = null,
        /**
         * FG(카탈로그) 전용 아이템 단위 변수입니다.
         */
        #[\SensitiveParameter]
        public ?array $catalogVariable = null,
        /**
         * 등록일입니다.
         */
        #[\SensitiveParameter]
        public ?string $createAt = null,
        /**
         * 수정일입니다.
         */
        #[\SensitiveParameter]
        public ?string $modifiedAt = null,
        /**
         * 템플릿 상태입니다. 알려진 값: `A`(등록), `S`(차단).
         *
         * 알려진 값: `A`, `S`
         */
        #[\SensitiveParameter]
        public ?string $status = null,
    ) {
        Check::all(
            Check::enum('sendType', $sendType, ['basic', 'free']),
            Check::maxLength('templateName', $templateName, 200),
            Check::maxLength('header', $header, 20),
            Check::enum('pushAlarm', $pushAlarm, ['Y', 'N']),
            Check::listOf('carouselVariable', $carouselVariable, [BrandMessageCarouselVariable::class]),
            Check::listOf('catalogVariable', $catalogVariable, [BrandMessageCatalogVariable::class]),
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
        $senderKeyType = $in->requiredString('senderKeyType');
        $sendType = $in->requiredString('sendType');
        $templateName = $in->requiredString('templateName');
        $msgType = $in->requiredString('msgType');
        $templateCode = $in->string('templateCode');
        $text = $in->string('text');
        $carousel = $in->model('carousel', BrandMessageCarousel::class);
        $attachment = $in->model('attachment', BrandMessageAttachment::class);
        $header = $in->string('header');
        $headerDescription = $in->string('headerDescription');
        $additionalContent = $in->string('additionalContent');
        $pushAlarm = $in->string('pushAlarm');
        $messageVariable = $in->object('messageVariable');
        $buttonVariable = $in->object('buttonVariable');
        $couponVariable = $in->object('couponVariable');
        $imageVariable = $in->object('imageVariable');
        $videoVariable = $in->object('videoVariable');
        $commerceVariable = $in->object('commerceVariable');
        $carouselVariable = $in->modelList('carouselVariable', BrandMessageCarouselVariable::class);
        $catalogVariable = $in->modelList('catalogVariable', BrandMessageCatalogVariable::class);
        $createAt = $in->string('createAt');
        $modifiedAt = $in->string('modifiedAt');
        $status = $in->string('status');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            senderKeyType: $senderKeyType,
            sendType: $sendType,
            templateName: $templateName,
            msgType: $msgType,
            templateCode: $templateCode,
            text: $text,
            carousel: $carousel,
            attachment: $attachment,
            header: $header,
            headerDescription: $headerDescription,
            additionalContent: $additionalContent,
            pushAlarm: $pushAlarm,
            messageVariable: $messageVariable,
            buttonVariable: $buttonVariable,
            couponVariable: $couponVariable,
            imageVariable: $imageVariable,
            videoVariable: $videoVariable,
            commerceVariable: $commerceVariable,
            carouselVariable: $carouselVariable,
            catalogVariable: $catalogVariable,
            createAt: $createAt,
            modifiedAt: $modifiedAt,
            status: $status,
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
            'sendType' => $this->sendType,
            'templateName' => $this->templateName,
            'msgType' => $this->msgType,
            'templateCode' => $this->templateCode,
            'text' => $this->text,
            'carousel' => $this->carousel?->toArray(),
            'attachment' => $this->attachment?->toArray(),
            'header' => $this->header,
            'headerDescription' => $this->headerDescription,
            'additionalContent' => $this->additionalContent,
            'pushAlarm' => $this->pushAlarm,
            'messageVariable' => $this->messageVariable,
            'buttonVariable' => $this->buttonVariable,
            'couponVariable' => $this->couponVariable,
            'imageVariable' => $this->imageVariable,
            'videoVariable' => $this->videoVariable,
            'commerceVariable' => $this->commerceVariable,
            'carouselVariable' => Serialize::models($this->carouselVariable),
            'catalogVariable' => Serialize::models($this->catalogVariable),
            'createAt' => $this->createAt,
            'modifiedAt' => $this->modifiedAt,
            'status' => $this->status,
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
            'sendType' => $this->sendType,
            'templateName' => $this->templateName,
            'msgType' => $this->msgType,
            'templateCode' => $this->templateCode,
            'text' => $this->text,
            'carousel' => $this->carousel,
            'attachment' => $this->attachment,
            'header' => $this->header,
            'headerDescription' => $this->headerDescription,
            'additionalContent' => $this->additionalContent,
            'pushAlarm' => $this->pushAlarm,
            'messageVariable' => Serialize::jsonObject($this->messageVariable),
            'buttonVariable' => Serialize::jsonObject($this->buttonVariable),
            'couponVariable' => Serialize::jsonObject($this->couponVariable),
            'imageVariable' => Serialize::jsonObject($this->imageVariable),
            'videoVariable' => Serialize::jsonObject($this->videoVariable),
            'commerceVariable' => Serialize::jsonObject($this->commerceVariable),
            'carouselVariable' => $this->carouselVariable,
            'catalogVariable' => $this->catalogVariable,
            'createAt' => $this->createAt,
            'modifiedAt' => $this->modifiedAt,
            'status' => $this->status,
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
        return Mask::describe('BrandMessageTemplateInput', get_object_vars($this));
    }
}
