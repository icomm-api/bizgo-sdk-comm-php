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
 * 알림톡 대표 링크 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkLink implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['urlPc', 'urlMobile', 'schemeAndroid', 'schemeIos'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['urlPc', 'urlMobile'];

    /**
     * @param string|null $urlPc PC 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $urlMobile 모바일 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * PC 환경에서 클릭 시 이동할 URL입니다.
         */
        public ?string $urlPc = null,
        /**
         * 모바일 환경에서 클릭 시 이동할 URL입니다.
         */
        public ?string $urlMobile = null,
        /**
         * Android 커스텀 스킴입니다.
         */
        public ?string $schemeAndroid = null,
        /**
         * iOS 커스텀 스킴입니다.
         */
        public ?string $schemeIos = null,
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
            urlPc: $in->string('urlPc'),
            urlMobile: $in->string('urlMobile'),
            schemeAndroid: $in->string('schemeAndroid'),
            schemeIos: $in->string('schemeIos'),
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
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeAndroid' => $this->schemeAndroid,
            'schemeIos' => $this->schemeIos,
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
        return Mask::describe('AlimtalkLink', get_object_vars($this));
    }
}
