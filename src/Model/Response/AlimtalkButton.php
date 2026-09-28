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
 * 알림톡 버튼입니다. 버튼 타입에 따라 필요한 필드가 다릅니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkButton implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'type',
        'name',
        'urlPc',
        'urlMobile',
        'schemeIos',
        'schemeAndroid',
        'target',
        'chatExtra',
        'chatEvent',
        'pluginId',
        'relayId',
        'oneclickId',
        'productId',
        'bizFormKey',
        'bizFormId',
        'telNumber',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['type', 'name'];

    /**
     * @param string|null $type 카카오 버튼 타입 코드입니다.
     * @param string|null $name 카카오 버튼명입니다.
     * @param string|null $urlPc PC 환경에서 버튼 클릭 시 이동할 URL입니다.
     * @param string|null $urlMobile 모바일 환경에서 버튼 클릭 시 이동할 URL입니다.
     * @param string|null $schemeIos iOS 환경에서 버튼 클릭 시 실행할 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 환경에서 버튼 클릭 시 실행할 커스텀 스킴입니다.
     * @param string|null $target 웹 링크 버튼의 target 값입니다.
     * @param string|null $chatExtra 봇/상담톡 전환 시 전달할 메타 정보입니다.
     * @param string|null $chatEvent 봇/상담톡 전환 시 연결할 이벤트명입니다.
     * @param string|null $pluginId 비즈플러그인 ID입니다.
     * @param string|null $relayId 비즈플러그인 relay ID입니다.
     * @param string|null $oneclickId 원클릭 결제 정보 ID입니다.
     * @param string|null $productId 원클릭 결제 상품 ID입니다.
     * @param string|null $bizFormKey 비즈폼 키입니다.
     * @param string|null $bizFormId 비즈폼 ID입니다.
     * @param string|null $telNumber 전화번호입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 카카오 버튼 타입 코드입니다. 문서 예시에 나온 값은 `WL`(웹 링크)입니다.
         *
         * 알려진 값: `WL`
         *
         * 확인 필요(x-unverified): 버튼 타입 코드 전체 목록과 타입별 필수 필드가 문서에 없습니다.
         */
        public ?string $type = null,
        /**
         * 카카오 버튼명입니다.
         */
        public ?string $name = null,
        /**
         * PC 환경에서 버튼 클릭 시 이동할 URL입니다.
         */
        public ?string $urlPc = null,
        /**
         * 모바일 환경에서 버튼 클릭 시 이동할 URL입니다.
         */
        public ?string $urlMobile = null,
        /**
         * iOS 환경에서 버튼 클릭 시 실행할 커스텀 스킴입니다.
         */
        public ?string $schemeIos = null,
        /**
         * Android 환경에서 버튼 클릭 시 실행할 커스텀 스킴입니다.
         */
        public ?string $schemeAndroid = null,
        /**
         * 웹 링크 버튼의 target 값입니다. `type`이 `WL`이고 `target`이 `out`이면 아웃링크로 동작합니다.
         */
        public ?string $target = null,
        /**
         * 봇/상담톡 전환 시 전달할 메타 정보입니다.
         */
        public ?string $chatExtra = null,
        /**
         * 봇/상담톡 전환 시 연결할 이벤트명입니다.
         */
        public ?string $chatEvent = null,
        /**
         * 비즈플러그인 ID입니다.
         */
        public ?string $pluginId = null,
        /**
         * 비즈플러그인 relay ID입니다. 비즈플러그인 실행 시 `X-Kakao-Plugin-Relay-Id` 헤더로 전달됩니다.
         */
        public ?string $relayId = null,
        /**
         * 원클릭 결제 정보 ID입니다.
         */
        public ?string $oneclickId = null,
        /**
         * 원클릭 결제 상품 ID입니다.
         */
        public ?string $productId = null,
        /**
         * 비즈폼 키입니다.
         */
        public ?string $bizFormKey = null,
        /**
         * 비즈폼 ID입니다.
         */
        public ?string $bizFormId = null,
        /**
         * 전화번호입니다.
         */
        public ?string $telNumber = null,
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
            target: $in->string('target'),
            chatExtra: $in->string('chatExtra'),
            chatEvent: $in->string('chatEvent'),
            pluginId: $in->string('pluginId'),
            relayId: $in->string('relayId'),
            oneclickId: $in->string('oneclickId'),
            productId: $in->string('productId'),
            bizFormKey: $in->string('bizFormKey'),
            bizFormId: $in->string('bizFormId'),
            telNumber: $in->string('telNumber'),
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
            'target' => $this->target,
            'chatExtra' => $this->chatExtra,
            'chatEvent' => $this->chatEvent,
            'pluginId' => $this->pluginId,
            'relayId' => $this->relayId,
            'oneclickId' => $this->oneclickId,
            'productId' => $this->productId,
            'bizFormKey' => $this->bizFormKey,
            'bizFormId' => $this->bizFormId,
            'telNumber' => $this->telNumber,
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
        return Mask::describe('AlimtalkButton', get_object_vars($this));
    }
}
