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
 * 브랜드 이미지의 파일 ID, URL, 사용 유형 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsBrandMediaUrl implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileId', 'url', 'typeName', 'fileName'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $fileId 파일 ID입니다.
     * @param string|null $url 파일 URL입니다.
     * @param string|null $typeName 이미지의 사용 유형입니다.
     * @param string|null $fileName 등록한 파일의 이름입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 파일 ID입니다.
         */
        public ?string $fileId = null,
        /**
         * 파일 URL입니다.
         */
        public ?string $url = null,
        /**
         * 이미지의 사용 유형입니다. 알려진 값(영문 문서 기준): `icon`(템플릿 양식 아이콘 이미지),
         * `profile`(브랜드 프로필 이미지), `background`(브랜드 배경 이미지).
         *
         * 알려진 값: `icon`, `profile`, `background`
         */
        public ?string $typeName = null,
        /**
         * 등록한 파일의 이름입니다.
         */
        public ?string $fileName = null,
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
            fileId: $in->string('fileId'),
            url: $in->string('url'),
            typeName: $in->string('typeName'),
            fileName: $in->string('fileName'),
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
            'fileId' => $this->fileId,
            'url' => $this->url,
            'typeName' => $this->typeName,
            'fileName' => $this->fileName,
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
        return Mask::describe('RcsBrandMediaUrl', get_object_vars($this));
    }
}
