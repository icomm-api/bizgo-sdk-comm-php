<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 양식 로고 이미지 정보입니다.
 *
 * 확인 필요(x-unverified): 표에는 fileId·fileUrl·fileName이 있으나 응답 예시에는 fileUrl 대신 imageWidth·imageHeight가
 * 있습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateFormLogo implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileId', 'fileUrl', 'fileName'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $fileId 로고 이미지 파일 ID입니다.
     * @param string|null $fileUrl 로고 이미지 파일 URL입니다.
     * @param string|null $fileName 로고 이미지 파일 이름입니다(최대 256자).
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 로고 이미지 파일 ID입니다.
         */
        public ?string $fileId = null,
        /**
         * 로고 이미지 파일 URL입니다.
         */
        public ?string $fileUrl = null,
        /**
         * 로고 이미지 파일 이름입니다(최대 256자).
         *
         * 최대 256자
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
            fileUrl: $in->string('fileUrl'),
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
            'fileUrl' => $this->fileUrl,
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
        return Mask::describe('RcsTemplateFormLogo', get_object_vars($this));
    }
}
