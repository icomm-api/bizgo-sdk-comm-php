<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 상담톡 파일 업로드 결과입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselFileUploadResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileUrl', 'fileName', 'size'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $fileUrl 업로드된 파일 URL입니다.
     * @param string|null $fileName 업로드된 파일 이름입니다.
     * @param string|null $size 업로드된 파일 용량입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 업로드된 파일 URL입니다. 발송 요청의 `attachment.file.fileUrl`에 넣습니다.
         */
        public ?string $fileUrl = null,
        /**
         * 업로드된 파일 이름입니다.
         */
        public ?string $fileName = null,
        /**
         * 업로드된 파일 용량입니다. 문서 예시는 byte 단위 문자열(`102400`)입니다.
         */
        public ?string $size = null,
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
            fileUrl: $in->string('fileUrl'),
            fileName: $in->string('fileName'),
            size: $in->string('size'),
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
            'fileUrl' => $this->fileUrl,
            'fileName' => $this->fileName,
            'size' => $this->size,
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
        return Mask::describe('CounselFileUploadResult', get_object_vars($this));
    }
}
