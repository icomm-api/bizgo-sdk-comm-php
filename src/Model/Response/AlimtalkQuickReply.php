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
 * 알림톡 바로연결 1개입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkQuickReply implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'type',
        'name',
        'urlPc',
        'urlMobile',
        'schemeIos',
        'schemeAndroid',
        'chatExtra',
        'chatEvent',
        'bizFormId',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['type', 'name'];

    /**
     * @param string|null $type 바로연결 타입 코드입니다.
     * @param string|null $name 바로연결 제목입니다.
     * @param string|null $urlPc PC 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $urlMobile 모바일 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $chatExtra 봇/상담톡 전환 시 전달할 메타 정보입니다.
     * @param string|null $chatEvent 봇/상담톡 전환 시 연결할 이벤트명입니다.
     * @param string|null $bizFormId 비즈폼 ID입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 바로연결 타입 코드입니다.
         *
         * 확인 필요(x-unverified): 바로연결 타입 코드 목록이 문서에 없습니다.
         */
        public ?string $type = null,
        /**
         * 바로연결 제목입니다. 템플릿 등록 규격상 최대 14자입니다.
         */
        public ?string $name = null,
        /**
         * PC 환경에서 클릭 시 이동할 URL입니다.
         */
        public ?string $urlPc = null,
        /**
         * 모바일 환경에서 클릭 시 이동할 URL입니다.
         */
        public ?string $urlMobile = null,
        /**
         * iOS 커스텀 스킴입니다.
         */
        public ?string $schemeIos = null,
        /**
         * Android 커스텀 스킴입니다.
         */
        public ?string $schemeAndroid = null,
        /**
         * 봇/상담톡 전환 시 전달할 메타 정보입니다.
         */
        public ?string $chatExtra = null,
        /**
         * 봇/상담톡 전환 시 연결할 이벤트명입니다.
         */
        public ?string $chatEvent = null,
        /**
         * 비즈폼 ID입니다.
         */
        public ?string $bizFormId = null,
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
            type: $in->string('type'),
            name: $in->string('name'),
            urlPc: $in->string('urlPc'),
            urlMobile: $in->string('urlMobile'),
            schemeIos: $in->string('schemeIos'),
            schemeAndroid: $in->string('schemeAndroid'),
            chatExtra: $in->string('chatExtra'),
            chatEvent: $in->string('chatEvent'),
            bizFormId: $in->string('bizFormId'),
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
            'type' => $this->type,
            'name' => $this->name,
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'chatExtra' => $this->chatExtra,
            'chatEvent' => $this->chatEvent,
            'bizFormId' => $this->bizFormId,
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
        return Mask::describe('AlimtalkQuickReply', get_object_vars($this));
    }
}
