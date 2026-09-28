<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 시스템 메시지 검수 결과 또는 문의입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselSystemMessageComment implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['id', 'content', 'userName', 'createdAt', 'status'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $id 댓글 아이디입니다.
     * @param string|null $content 댓글 내용입니다.
     * @param string|null $userName 작성자입니다.
     * @param string|null $createdAt 등록일입니다.
     * @param string|null $status 상태입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 댓글 아이디입니다.
         */
        public ?string $id = null,
        /**
         * 댓글 내용입니다.
         */
        public ?string $content = null,
        /**
         * 작성자입니다.
         */
        public ?string $userName = null,
        /**
         * 등록일입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $createdAt = null,
        /**
         * 상태입니다. `APR` 승인, `REJ` 반려, `INQ` 문의입니다.
         *
         * 값: `APR`, `REJ`, `INQ`
         */
        public ?string $status = null,
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
            id: $in->string('id'),
            content: $in->string('content'),
            userName: $in->string('userName'),
            createdAt: $in->string('createdAt'),
            status: $in->string('status'),
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
            'id' => $this->id,
            'content' => $this->content,
            'userName' => $this->userName,
            'createdAt' => $this->createdAt,
            'status' => $this->status,
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
        return Mask::describe('CounselSystemMessageComment', get_object_vars($this));
    }
}
