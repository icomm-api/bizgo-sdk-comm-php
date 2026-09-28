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
 * 시스템 메시지 하단 버튼입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselSystemMessageButton implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['ordering', 'type', 'name', 'urlMobile', 'urlPc', 'schemeIos', 'schemeAndroid'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['ordering', 'type', 'name'];

    /**
     * @param int|null $ordering 버튼 노출 순서입니다.
     * @param string|null $type 시스템 메시지 버튼 링크 타입입니다.
     * @param string|null $name 버튼 이름입니다.
     * @param string|null $urlMobile 모바일 웹링크 주소입니다.
     * @param string|null $urlPc PC 웹링크 주소입니다.
     * @param string|null $schemeIos iOS 앱링크 주소입니다.
     * @param string|null $schemeAndroid Android 앱링크 주소입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 버튼 노출 순서입니다.
         */
        public ?int $ordering = null,
        /**
         * 시스템 메시지 버튼 링크 타입입니다. 문서 예시 값은 `WL`입니다.
         *
         * 알려진 값: `WL`
         *
         * 확인 필요(x-unverified): 시스템 메시지 버튼 타입 목록이 문서에 없습니다.
         */
        public ?string $type = null,
        /**
         * 버튼 이름입니다.
         */
        public ?string $name = null,
        /**
         * 모바일 웹링크 주소입니다.
         */
        public ?string $urlMobile = null,
        /**
         * PC 웹링크 주소입니다.
         */
        public ?string $urlPc = null,
        /**
         * iOS 앱링크 주소입니다.
         */
        public ?string $schemeIos = null,
        /**
         * Android 앱링크 주소입니다.
         */
        public ?string $schemeAndroid = null,
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
            ordering: $in->int('ordering'),
            type: $in->string('type'),
            name: $in->string('name'),
            urlMobile: $in->string('urlMobile'),
            urlPc: $in->string('urlPc'),
            schemeIos: $in->string('schemeIos'),
            schemeAndroid: $in->string('schemeAndroid'),
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
            'ordering' => $this->ordering,
            'type' => $this->type,
            'name' => $this->name,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
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
        return Mask::describe('CounselSystemMessageButton', get_object_vars($this));
    }
}
