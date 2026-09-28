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
 * 템플릿 본문 영역의 검증 파라미터 정의입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateBodyParam implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['param', 'isMandatory', 'type', 'strSize', 'imageWidth', 'imageHeight'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['param', 'isMandatory'];

    /**
     * @param string|null $param 검증 파라미터명입니다.
     * @param string|null $isMandatory 필수 여부입니다.
     * @param string|null $type 기술 검증 타입입니다.
     * @param string|null $strSize 문자열 최대 크기입니다.
     * @param string|null $imageWidth 이미지 최소 가로 크기입니다.
     * @param string|null $imageHeight 이미지 최소 세로 크기입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 검증 파라미터명입니다. 영문, 숫자, `_`만 쓸 수 있습니다(영문 문서 기준).
         *
         * 형식 `^[A-Za-z0-9_]+$`
         */
        public ?string $param = null,
        /**
         * 필수 여부입니다.
         *
         * 확인 필요(x-unverified): 값 형식(Y/N, true/false 등)이 문서화되어 있지 않습니다.
         */
        public ?string $isMandatory = null,
        /**
         * 기술 검증 타입입니다.
         *
         * 확인 필요(x-unverified): 타입 값 목록이 문서화되어 있지 않습니다.
         */
        public ?string $type = null,
        /**
         * 문자열 최대 크기입니다. 문서상 문자열입니다.
         */
        public ?string $strSize = null,
        /**
         * 이미지 최소 가로 크기입니다. 문서상 문자열입니다.
         */
        public ?string $imageWidth = null,
        /**
         * 이미지 최소 세로 크기입니다. 문서상 문자열입니다.
         */
        public ?string $imageHeight = null,
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
            param: $in->string('param'),
            isMandatory: $in->string('isMandatory'),
            type: $in->string('type'),
            strSize: $in->string('strSize'),
            imageWidth: $in->string('imageWidth'),
            imageHeight: $in->string('imageHeight'),
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
            'param' => $this->param,
            'isMandatory' => $this->isMandatory,
            'type' => $this->type,
            'strSize' => $this->strSize,
            'imageWidth' => $this->imageWidth,
            'imageHeight' => $this->imageHeight,
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
        return Mask::describe('RcsTemplateBodyParam', get_object_vars($this));
    }
}
