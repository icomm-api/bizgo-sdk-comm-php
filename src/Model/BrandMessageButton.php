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
 * 브랜드메시지 버튼입니다.
 *
 * 조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): `type` == `WL`이면 `urlPc`,
 * `urlMobile`; `type` == `AL`이면 `urlMobile`.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageButton implements RequestModel
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

    /**
     * @param string $type 버튼 타입입니다.
     * @param string|null $name 버튼명입니다.
     * @param string|null $urlPc PC 클릭 URL입니다.
     * @param string|null $urlMobile 모바일 클릭 URL입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $chatExtra 봇/상담톡 메타데이터입니다.
     * @param string|null $chatEvent 봇/상담톡 이벤트명입니다.
     * @param string|null $bizFormKey 비즈폼 키입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 버튼 타입입니다. 문서에 언급된 값은 `WL`, `AL`입니다.
         *
         * 필수 · 알려진 값: `WL`, `AL`
         *
         * 확인 필요(x-unverified): 버튼 타입 코드 전체 목록이 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $type,
        /**
         * 버튼명입니다. 최대 28자입니다.
         *
         * 최대 28자
         */
        #[\SensitiveParameter]
        public ?string $name = null,
        /**
         * PC 클릭 URL입니다. `WL` 타입에서 필수입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * 모바일 클릭 URL입니다. `WL`, `AL` 타입에서 필수입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * iOS 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
        /**
         * 봇/상담톡 메타데이터입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatExtra = null,
        /**
         * 봇/상담톡 이벤트명입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatEvent = null,
        /**
         * 비즈폼 키입니다.
         */
        #[\SensitiveParameter]
        public ?string $bizFormKey = null,
    ) {
        Check::all(
            Check::maxLength('name', $name, 28),
        );
        Check::all(RequiredIf::check('BrandMessageButton', $this->toArray()));
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
        $name = $in->string('name');
        $urlPc = $in->string('urlPc');
        $urlMobile = $in->string('urlMobile');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');
        $chatExtra = $in->string('chatExtra');
        $chatEvent = $in->string('chatEvent');
        $bizFormKey = $in->string('bizFormKey');

        return $in->create(static fn (): static => new static(
            type: $type,
            name: $name,
            urlPc: $urlPc,
            urlMobile: $urlMobile,
            schemeIos: $schemeIos,
            schemeAndroid: $schemeAndroid,
            chatExtra: $chatExtra,
            chatEvent: $chatEvent,
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
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'chatExtra' => $this->chatExtra,
            'chatEvent' => $this->chatEvent,
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
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'chatExtra' => $this->chatExtra,
            'chatEvent' => $this->chatEvent,
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
        return Mask::describe('BrandMessageButton', get_object_vars($this));
    }
}
