<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 본문 영역의 검증 파라미터 정의입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsTemplateBodyParam implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['param', 'isMandatory', 'type', 'strSize', 'imageWidth', 'imageHeight'];

    /**
     * @param string $param 검증 파라미터명입니다.
     * @param string $isMandatory 필수 여부입니다.
     * @param string|null $type 기술 검증 타입입니다.
     * @param string|null $strSize 문자열 최대 크기입니다.
     * @param string|null $imageWidth 이미지 최소 가로 크기입니다.
     * @param string|null $imageHeight 이미지 최소 세로 크기입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 검증 파라미터명입니다. 영문, 숫자, `_`만 쓸 수 있습니다(영문 문서 기준).
         *
         * 필수 · 형식 `^[A-Za-z0-9_]+$`
         */
        #[\SensitiveParameter]
        public string $param,
        /**
         * 필수 여부입니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): 값 형식(Y/N, true/false 등)이 문서화되어 있지 않습니다.
         */
        #[\SensitiveParameter]
        public string $isMandatory,
        /**
         * 기술 검증 타입입니다.
         *
         * 확인 필요(x-unverified): 타입 값 목록이 문서화되어 있지 않습니다.
         */
        #[\SensitiveParameter]
        public ?string $type = null,
        /**
         * 문자열 최대 크기입니다. 문서상 문자열입니다.
         */
        #[\SensitiveParameter]
        public ?string $strSize = null,
        /**
         * 이미지 최소 가로 크기입니다. 문서상 문자열입니다.
         */
        #[\SensitiveParameter]
        public ?string $imageWidth = null,
        /**
         * 이미지 최소 세로 크기입니다. 문서상 문자열입니다.
         */
        #[\SensitiveParameter]
        public ?string $imageHeight = null,
    ) {
        Check::all(
            Check::pattern('param', $param, '^[A-Za-z0-9_]+$'),
        );
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
        $param = $in->requiredString('param');
        $isMandatory = $in->requiredString('isMandatory');
        $type = $in->string('type');
        $strSize = $in->string('strSize');
        $imageWidth = $in->string('imageWidth');
        $imageHeight = $in->string('imageHeight');

        return $in->create(static fn (): static => new static(
            param: $param,
            isMandatory: $isMandatory,
            type: $type,
            strSize: $strSize,
            imageWidth: $imageWidth,
            imageHeight: $imageHeight,
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
            'param' => $this->param,
            'isMandatory' => $this->isMandatory,
            'type' => $this->type,
            'strSize' => $this->strSize,
            'imageWidth' => $this->imageWidth,
            'imageHeight' => $this->imageHeight,
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
            'param' => $this->param,
            'isMandatory' => $this->isMandatory,
            'type' => $this->type,
            'strSize' => $this->strSize,
            'imageWidth' => $this->imageWidth,
            'imageHeight' => $this->imageHeight,
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
        return Mask::describe('RcsTemplateBodyParam', get_object_vars($this));
    }
}
