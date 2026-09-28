<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 알림톡 대표 링크 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkLink implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['urlPc', 'urlMobile', 'schemeAndroid', 'schemeIos'];

    /**
     * @param string $urlPc PC 환경에서 클릭 시 이동할 URL입니다.
     * @param string $urlMobile 모바일 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     */
    public function __construct(
        /**
         * PC 환경에서 클릭 시 이동할 URL입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $urlPc,
        /**
         * 모바일 환경에서 클릭 시 이동할 URL입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $urlMobile,
        /**
         * Android 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
        /**
         * iOS 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
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
        $urlPc = $in->requiredString('urlPc');
        $urlMobile = $in->requiredString('urlMobile');
        $schemeAndroid = $in->string('schemeAndroid');
        $schemeIos = $in->string('schemeIos');

        return $in->create(static fn (): static => new static(
            urlPc: $urlPc,
            urlMobile: $urlMobile,
            schemeAndroid: $schemeAndroid,
            schemeIos: $schemeIos,
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
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeAndroid' => $this->schemeAndroid,
            'schemeIos' => $this->schemeIos,
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
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeAndroid' => $this->schemeAndroid,
            'schemeIos' => $this->schemeIos,
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
        return Mask::describe('AlimtalkLink', get_object_vars($this));
    }
}
