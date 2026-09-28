<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 이미지 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateImage implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileId', 'fileName', 'imageWidth', 'imageHeight'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $fileId 템플릿 파일 ID입니다.
     * @param string|null $fileName 파일 이름입니다.
     * @param int|null $imageWidth 이미지 가로 크기입니다.
     * @param int|null $imageHeight 이미지 세로 크기입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 템플릿 파일 ID입니다.
         */
        public ?string $fileId = null,
        /**
         * 파일 이름입니다.
         */
        public ?string $fileName = null,
        /**
         * 이미지 가로 크기입니다.
         */
        public ?int $imageWidth = null,
        /**
         * 이미지 세로 크기입니다.
         */
        public ?int $imageHeight = null,
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
            fileName: $in->string('fileName'),
            imageWidth: $in->int('imageWidth'),
            imageHeight: $in->int('imageHeight'),
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
            'fileName' => $this->fileName,
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
        return Mask::describe('RcsTemplateImage', get_object_vars($this));
    }
}
