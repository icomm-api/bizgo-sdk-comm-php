<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 상담톡 Rich 메시지·캐러셀 버튼입니다. 버튼 타입에 따라 필요한 필드가 다릅니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselButton implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'type',
        'name',
        'urlMobile',
        'urlPc',
        'schemeIos',
        'schemeAndroid',
        'urlTarget',
        'extra',
        'chatEvent',
        'startType',
        'bizFormKey',
    ];

    /**
     * @param string $type 카카오 버튼 타입 코드입니다.
     * @param string $name 버튼명입니다.
     * @param string|null $urlMobile 모바일 환경에서 버튼 클릭 시 이동할 URL입니다.
     * @param string|null $urlPc PC 환경에서 버튼 클릭 시 이동할 URL입니다.
     * @param string|null $schemeIos iOS 환경에서 버튼 클릭 시 실행할 application custom scheme입니다.
     * @param string|null $schemeAndroid Android 환경에서 버튼 클릭 시 실행할 application custom scheme입니다.
     * @param string|null $urlTarget WL 타입에서 외부 브라우저로 열려면 `out`을 입력합니다.
     * @param string|null $extra 봇/상담톡 전환 시 전달할 메타 정보입니다.
     * @param string|null $chatEvent 봇/상담톡 전환 시 연결할 이벤트명입니다.
     * @param string|null $startType 상담 시작 타입입니다.
     * @param string|null $bizFormKey 비즈폼 키입니다.
     */
    public function __construct(
        /**
         * 카카오 버튼 타입 코드입니다. 문서에 나온 값은 `WL`(웹 링크, 예시)과 `BF`(비즈폼)입니다.
         *
         * 필수 · 알려진 값: `WL`, `BF`
         *
         * 확인 필요(x-unverified): 버튼 타입 코드 전체 목록과 타입별 필수 필드가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $type,
        /**
         * 버튼명입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $name,
        /**
         * 모바일 환경에서 버튼 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * PC 환경에서 버튼 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * iOS 환경에서 버튼 클릭 시 실행할 application custom scheme입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 환경에서 버튼 클릭 시 실행할 application custom scheme입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
        /**
         * WL 타입에서 외부 브라우저로 열려면 `out`을 입력합니다.
         *
         * 알려진 값: `out`
         */
        #[\SensitiveParameter]
        public ?string $urlTarget = null,
        /**
         * 봇/상담톡 전환 시 전달할 메타 정보입니다.
         */
        #[\SensitiveParameter]
        public ?string $extra = null,
        /**
         * 봇/상담톡 전환 시 연결할 이벤트명입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatEvent = null,
        /**
         * 상담 시작 타입입니다.
         *
         * 확인 필요(x-unverified): startType 값 목록이 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $startType = null,
        /**
         * 비즈폼 키입니다. BF 타입에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?string $bizFormKey = null,
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
        $urlMobile = $in->string('urlMobile');
        $urlPc = $in->string('urlPc');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');
        $urlTarget = $in->string('urlTarget');
        $extra = $in->string('extra');
        $chatEvent = $in->string('chatEvent');
        $startType = $in->string('startType');
        $bizFormKey = $in->string('bizFormKey');

        return $in->create(static fn (): static => new static(
            type: $type,
            name: $name,
            urlMobile: $urlMobile,
            urlPc: $urlPc,
            schemeIos: $schemeIos,
            schemeAndroid: $schemeAndroid,
            urlTarget: $urlTarget,
            extra: $extra,
            chatEvent: $chatEvent,
            startType: $startType,
            bizFormKey: $bizFormKey,
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
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'urlTarget' => $this->urlTarget,
            'extra' => $this->extra,
            'chatEvent' => $this->chatEvent,
            'startType' => $this->startType,
            'bizFormKey' => $this->bizFormKey,
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
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'urlTarget' => $this->urlTarget,
            'extra' => $this->extra,
            'chatEvent' => $this->chatEvent,
            'startType' => $this->startType,
            'bizFormKey' => $this->bizFormKey,
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
        return Mask::describe('CounselButton', get_object_vars($this));
    }
}
