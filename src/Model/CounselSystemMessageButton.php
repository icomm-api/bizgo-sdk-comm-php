<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 시스템 메시지 하단 버튼입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselSystemMessageButton implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['ordering', 'type', 'name', 'urlMobile', 'urlPc', 'schemeIos', 'schemeAndroid'];

    /**
     * @param int $ordering 버튼 노출 순서입니다.
     * @param string $type 시스템 메시지 버튼 링크 타입입니다.
     * @param string $name 버튼 이름입니다.
     * @param string|null $urlMobile 모바일 웹링크 주소입니다.
     * @param string|null $urlPc PC 웹링크 주소입니다.
     * @param string|null $schemeIos iOS 앱링크 주소입니다.
     * @param string|null $schemeAndroid Android 앱링크 주소입니다.
     */
    public function __construct(
        /**
         * 버튼 노출 순서입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public int $ordering,
        /**
         * 시스템 메시지 버튼 링크 타입입니다. 문서 예시 값은 `WL`입니다.
         *
         * 필수 · 알려진 값: `WL`
         *
         * 확인 필요(x-unverified): 시스템 메시지 버튼 타입 목록이 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $type,
        /**
         * 버튼 이름입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $name,
        /**
         * 모바일 웹링크 주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * PC 웹링크 주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * iOS 앱링크 주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 앱링크 주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
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
        $ordering = $in->requiredInt('ordering');
        $type = $in->requiredString('type');
        $name = $in->requiredString('name');
        $urlMobile = $in->string('urlMobile');
        $urlPc = $in->string('urlPc');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');

        return $in->create(static fn (): static => new static(
            ordering: $ordering,
            type: $type,
            name: $name,
            urlMobile: $urlMobile,
            urlPc: $urlPc,
            schemeIos: $schemeIos,
            schemeAndroid: $schemeAndroid,
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
            'ordering' => $this->ordering,
            'type' => $this->type,
            'name' => $this->name,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
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
            'ordering' => $this->ordering,
            'type' => $this->type,
            'name' => $this->name,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
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
        return Mask::describe('CounselSystemMessageButton', get_object_vars($this));
    }
}
