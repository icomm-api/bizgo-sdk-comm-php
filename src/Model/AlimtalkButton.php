<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 알림톡 버튼입니다. 버튼 타입에 따라 필요한 필드가 다릅니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkButton implements RequestModel
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

    /**
     * @param string $type 카카오 버튼 타입 코드입니다.
     * @param string $name 카카오 버튼명입니다.
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
     */
    public function __construct(
        /**
         * 카카오 버튼 타입 코드입니다. 문서 예시에 나온 값은 `WL`(웹 링크)입니다.
         *
         * 필수 · 알려진 값: `WL`
         *
         * 확인 필요(x-unverified): 버튼 타입 코드 전체 목록과 타입별 필수 필드가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $type,
        /**
         * 카카오 버튼명입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $name,
        /**
         * PC 환경에서 버튼 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * 모바일 환경에서 버튼 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * iOS 환경에서 버튼 클릭 시 실행할 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 환경에서 버튼 클릭 시 실행할 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
        /**
         * 웹 링크 버튼의 target 값입니다. `type`이 `WL`이고 `target`이 `out`이면 아웃링크로 동작합니다.
         */
        #[\SensitiveParameter]
        public ?string $target = null,
        /**
         * 봇/상담톡 전환 시 전달할 메타 정보입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatExtra = null,
        /**
         * 봇/상담톡 전환 시 연결할 이벤트명입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatEvent = null,
        /**
         * 비즈플러그인 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $pluginId = null,
        /**
         * 비즈플러그인 relay ID입니다. 비즈플러그인 실행 시 `X-Kakao-Plugin-Relay-Id` 헤더로 전달됩니다.
         */
        #[\SensitiveParameter]
        public ?string $relayId = null,
        /**
         * 원클릭 결제 정보 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $oneclickId = null,
        /**
         * 원클릭 결제 상품 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $productId = null,
        /**
         * 비즈폼 키입니다.
         */
        #[\SensitiveParameter]
        public ?string $bizFormKey = null,
        /**
         * 비즈폼 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $bizFormId = null,
        /**
         * 전화번호입니다.
         */
        #[\SensitiveParameter]
        public ?string $telNumber = null,
    ) {
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
        $type = $in->requiredString('type');
        $name = $in->requiredString('name');
        $urlPc = $in->string('urlPc');
        $urlMobile = $in->string('urlMobile');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');
        $target = $in->string('target');
        $chatExtra = $in->string('chatExtra');
        $chatEvent = $in->string('chatEvent');
        $pluginId = $in->string('pluginId');
        $relayId = $in->string('relayId');
        $oneclickId = $in->string('oneclickId');
        $productId = $in->string('productId');
        $bizFormKey = $in->string('bizFormKey');
        $bizFormId = $in->string('bizFormId');
        $telNumber = $in->string('telNumber');

        return $in->create(static fn (): static => new static(
            type: $type,
            name: $name,
            urlPc: $urlPc,
            urlMobile: $urlMobile,
            schemeIos: $schemeIos,
            schemeAndroid: $schemeAndroid,
            target: $target,
            chatExtra: $chatExtra,
            chatEvent: $chatEvent,
            pluginId: $pluginId,
            relayId: $relayId,
            oneclickId: $oneclickId,
            productId: $productId,
            bizFormKey: $bizFormKey,
            bizFormId: $bizFormId,
            telNumber: $telNumber,
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
        return Mask::describe('AlimtalkButton', get_object_vars($this));
    }
}
