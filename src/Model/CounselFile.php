<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 상담톡 파일 첨부 정보입니다(VIDEO·AUDIO·FILE 타입).
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselFile implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileUrl', 'fileName', 'fileSize'];

    /**
     * @param string $fileUrl 상담톡 파일 업로드 API(`/api/comm/v1/file/cstalk`)로 등록한 파일 URL입니다.
     * @param string|null $fileName 파일명입니다.
     * @param string|null $fileSize 파일 크기입니다.
     */
    public function __construct(
        /**
         * 상담톡 파일 업로드 API(`/api/comm/v1/file/cstalk`)로 등록한 파일 URL입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $fileUrl,
        /**
         * 파일명입니다. `msgType`이 `FILE`이면 필수입니다(`CounselPlainMessageRequest`의 `x-sdk-required-if`).
         */
        #[\SensitiveParameter]
        public ?string $fileName = null,
        /**
         * 파일 크기입니다. `msgType`이 `FILE`이면 필수입니다(`CounselPlainMessageRequest`의 `x-sdk-required-if`). 문서상 타입은
         * 문자열입니다.
         */
        #[\SensitiveParameter]
        public ?string $fileSize = null,
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
        $fileUrl = $in->requiredString('fileUrl');
        $fileName = $in->string('fileName');
        $fileSize = $in->string('fileSize');

        return $in->create(static fn (): static => new static(
            fileUrl: $fileUrl,
            fileName: $fileName,
            fileSize: $fileSize,
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
            'fileUrl' => $this->fileUrl,
            'fileName' => $this->fileName,
            'fileSize' => $this->fileSize,
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
            'fileUrl' => $this->fileUrl,
            'fileName' => $this->fileName,
            'fileSize' => $this->fileSize,
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
        return Mask::describe('CounselFile', get_object_vars($this));
    }
}
