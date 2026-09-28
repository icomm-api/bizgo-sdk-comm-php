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
 * 브랜드메시지 버튼입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageButton implements ResponseModel
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
        'bizFormKey',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['type'];

    /**
     * @param string|null $type 버튼 타입입니다.
     * @param string|null $name 버튼명입니다.
     * @param string|null $urlPc PC 클릭 URL입니다.
     * @param string|null $urlMobile 모바일 클릭 URL입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $chatExtra 봇/상담톡 메타데이터입니다.
     * @param string|null $chatEvent 봇/상담톡 이벤트명입니다.
     * @param string|null $bizFormKey 비즈폼 키입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 버튼 타입입니다. 문서에 언급된 값은 `WL`, `AL`입니다.
         *
         * 알려진 값: `WL`, `AL`
         *
         * 확인 필요(x-unverified): 버튼 타입 코드 전체 목록이 문서에 없습니다.
         */
        public ?string $type = null,
        /**
         * 버튼명입니다. 최대 28자입니다.
         *
         * 최대 28자
         */
        public ?string $name = null,
        /**
         * PC 클릭 URL입니다. `WL` 타입에서 필수입니다.
         */
        public ?string $urlPc = null,
        /**
         * 모바일 클릭 URL입니다. `WL`, `AL` 타입에서 필수입니다.
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
         * 봇/상담톡 메타데이터입니다.
         */
        public ?string $chatExtra = null,
        /**
         * 봇/상담톡 이벤트명입니다.
         */
        public ?string $chatEvent = null,
        /**
         * 비즈폼 키입니다.
         */
        public ?string $bizFormKey = null,
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
            bizFormKey: $in->string('bizFormKey'),
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
            'bizFormKey' => $this->bizFormKey,
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
        return Mask::describe('BrandMessageButton', get_object_vars($this));
    }
}
