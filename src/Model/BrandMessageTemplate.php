<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드메시지 템플릿 상세 정보입니다.
 *
 * 확인 필요(x-unverified): 응답 필드 표에 `templateName`, `headerDescription`, `catalogVariable`,
 * 카탈로그(`attachment.catalog`)가 없어 응답에 포함되는지 알 수 없습니다. 첨부 스키마는 발송용 스키마를 재사용하므로 발송 규격의 필수 표시가 응답에도 적용되는지는
 * 확인이 필요합니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageTemplate implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'sendType',
        'templateCode',
        'msgType',
        'text',
        'carousel',
        'attachment',
        'header',
        'additionalContent',
        'pushAlarm',
        'messageVariable',
        'buttonVariable',
        'couponVariable',
        'imageVariable',
        'videoVariable',
        'commerceVariable',
        'carouselVariable',
        'createAt',
        'modifiedAt',
        'status',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $sendType 브랜드메시지 발송 타입입니다(`basic`, `free`).
     * @param string|null $templateCode 템플릿 코드입니다.
     * @param string|null $msgType 브랜드메시지 메시지 타입입니다.
     * @param string|null $text 템플릿 본문입니다.
     * @param Response\BrandMessageCarousel|null $carousel 캐러셀 정보입니다.
     * @param Response\BrandMessageAttachment|null $attachment 첨부 정보입니다.
     * @param string|null $header 헤더 정보입니다.
     * @param string|null $additionalContent 부가 정보입니다.
     * @param string|null $pushAlarm 메시지 푸시 알림 발송 여부입니다.
     * @param array<string, mixed>|null $messageVariable 메시지 영역 변수입니다.
     * @param array<string, mixed>|null $buttonVariable 버튼 영역 변수입니다.
     * @param array<string, mixed>|null $couponVariable 쿠폰 영역 변수입니다.
     * @param array<string, mixed>|null $imageVariable 이미지 영역 변수입니다.
     * @param array<string, mixed>|null $videoVariable 비디오 영역 변수입니다.
     * @param array<string, mixed>|null $commerceVariable 커머스 영역 변수입니다.
     * @param list<Response\BrandMessageCarouselVariable>|null $carouselVariable 캐러셀 영역 변수입니다.
     * @param string|null $createAt 등록일(`yyyy-MM-dd'T'HH:mm:ssXXX`)입니다.
     * @param string|null $modifiedAt 수정일(`yyyy-MM-dd'T'HH:mm:ssXXX`)입니다.
     * @param string|null $status 템플릿 상태입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 브랜드메시지 발송 타입입니다(`basic`, `free`).
         */
        public ?string $sendType = null,
        /**
         * 템플릿 코드입니다.
         */
        public ?string $templateCode = null,
        /**
         * 브랜드메시지 메시지 타입입니다.
         */
        public ?string $msgType = null,
        /**
         * 템플릿 본문입니다.
         */
        public ?string $text = null,
        /**
         * 캐러셀 정보입니다.
         */
        public ?Response\BrandMessageCarousel $carousel = null,
        /**
         * 첨부 정보입니다.
         */
        public ?Response\BrandMessageAttachment $attachment = null,
        /**
         * 헤더 정보입니다.
         */
        public ?string $header = null,
        /**
         * 부가 정보입니다.
         */
        public ?string $additionalContent = null,
        /**
         * 메시지 푸시 알림 발송 여부입니다.
         *
         * 값: `Y`, `N`
         */
        public ?string $pushAlarm = null,
        /**
         * 메시지 영역 변수입니다.
         */
        public ?array $messageVariable = null,
        /**
         * 버튼 영역 변수입니다.
         */
        public ?array $buttonVariable = null,
        /**
         * 쿠폰 영역 변수입니다.
         */
        public ?array $couponVariable = null,
        /**
         * 이미지 영역 변수입니다.
         */
        public ?array $imageVariable = null,
        /**
         * 비디오 영역 변수입니다.
         */
        public ?array $videoVariable = null,
        /**
         * 커머스 영역 변수입니다.
         */
        public ?array $commerceVariable = null,
        /**
         * 캐러셀 영역 변수입니다.
         */
        public ?array $carouselVariable = null,
        /**
         * 등록일(`yyyy-MM-dd'T'HH:mm:ssXXX`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $createAt = null,
        /**
         * 수정일(`yyyy-MM-dd'T'HH:mm:ssXXX`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $modifiedAt = null,
        /**
         * 템플릿 상태입니다. 알려진 값: `A`(등록), `S`(차단).
         *
         * 알려진 값: `A`, `S`
         */
        public ?string $status = null,
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
            sendType: $in->string('sendType'),
            templateCode: $in->string('templateCode'),
            msgType: $in->string('msgType'),
            text: $in->string('text'),
            carousel: $in->model('carousel', Response\BrandMessageCarousel::class),
            attachment: $in->model('attachment', Response\BrandMessageAttachment::class),
            header: $in->string('header'),
            additionalContent: $in->string('additionalContent'),
            pushAlarm: $in->string('pushAlarm'),
            messageVariable: $in->object('messageVariable'),
            buttonVariable: $in->object('buttonVariable'),
            couponVariable: $in->object('couponVariable'),
            imageVariable: $in->object('imageVariable'),
            videoVariable: $in->object('videoVariable'),
            commerceVariable: $in->object('commerceVariable'),
            carouselVariable: $in->modelList('carouselVariable', Response\BrandMessageCarouselVariable::class),
            createAt: $in->string('createAt'),
            modifiedAt: $in->string('modifiedAt'),
            status: $in->string('status'),
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
            'sendType' => $this->sendType,
            'templateCode' => $this->templateCode,
            'msgType' => $this->msgType,
            'text' => $this->text,
            'carousel' => $this->carousel?->toArray(),
            'attachment' => $this->attachment?->toArray(),
            'header' => $this->header,
            'additionalContent' => $this->additionalContent,
            'pushAlarm' => $this->pushAlarm,
            'messageVariable' => $this->messageVariable,
            'buttonVariable' => $this->buttonVariable,
            'couponVariable' => $this->couponVariable,
            'imageVariable' => $this->imageVariable,
            'videoVariable' => $this->videoVariable,
            'commerceVariable' => $this->commerceVariable,
            'carouselVariable' => Serialize::models($this->carouselVariable),
            'createAt' => $this->createAt,
            'modifiedAt' => $this->modifiedAt,
            'status' => $this->status,
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
        return Mask::describe('BrandMessageTemplate', get_object_vars($this));
    }
}
