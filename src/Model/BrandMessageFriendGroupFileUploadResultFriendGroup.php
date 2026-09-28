<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 임시 업로드 파일 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageFriendGroupFileUploadResultFriendGroup implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['fileKey', 'expiredAt'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $fileKey 친구 그룹 등록·전화번호 추가·삭제에 쓸 임시 파일 키입니다.
     * @param string|null $expiredAt 파일 키 만료 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 친구 그룹 등록·전화번호 추가·삭제에 쓸 임시 파일 키입니다.
         */
        public ?string $fileKey = null,
        /**
         * 파일 키 만료 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $expiredAt = null,
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
            fileKey: $in->string('fileKey'),
            expiredAt: $in->string('expiredAt'),
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
            'fileKey' => $this->fileKey,
            'expiredAt' => $this->expiredAt,
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
        return Mask::describe('BrandMessageFriendGroupFileUploadResultFriendGroup', get_object_vars($this));
    }
}
