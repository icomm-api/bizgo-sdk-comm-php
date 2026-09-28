<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드 이미지의 파일 ID, URL, 사용 유형 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsBrandMediaUrl implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileId', 'url', 'typeName', 'fileName'];

    /**
     * @param string|null $fileId 파일 ID입니다.
     * @param string|null $url 파일 URL입니다.
     * @param string|null $typeName 이미지의 사용 유형입니다.
     * @param string|null $fileName 등록한 파일의 이름입니다.
     */
    public function __construct(
        /**
         * 파일 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $fileId = null,
        /**
         * 파일 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $url = null,
        /**
         * 이미지의 사용 유형입니다. 알려진 값(영문 문서 기준): `icon`(템플릿 양식 아이콘 이미지),
         * `profile`(브랜드 프로필 이미지), `background`(브랜드 배경 이미지).
         *
         * 알려진 값: `icon`, `profile`, `background`
         */
        #[\SensitiveParameter]
        public ?string $typeName = null,
        /**
         * 등록한 파일의 이름입니다.
         */
        #[\SensitiveParameter]
        public ?string $fileName = null,
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
        $fileId = $in->string('fileId');
        $url = $in->string('url');
        $typeName = $in->string('typeName');
        $fileName = $in->string('fileName');

        return $in->create(static fn (): static => new static(
            fileId: $fileId,
            url: $url,
            typeName: $typeName,
            fileName: $fileName,
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
            'fileId' => $this->fileId,
            'url' => $this->url,
            'typeName' => $this->typeName,
            'fileName' => $this->fileName,
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
            'fileId' => $this->fileId,
            'url' => $this->url,
            'typeName' => $this->typeName,
            'fileName' => $this->fileName,
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
        return Mask::describe('RcsBrandMediaUrl', get_object_vars($this));
    }
}
