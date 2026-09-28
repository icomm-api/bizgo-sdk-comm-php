<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 심사 의견 1건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkTemplateComment implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['id', 'userName', 'createdAt', 'status', 'attachment'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param int|null $id 심사 의견 ID입니다.
     * @param string|null $userName 심사 의견을 남긴 담당자명입니다.
     * @param string|null $createdAt 심사 의견 등록일입니다.
     * @param string|null $status 해당 의견 시점의 심사 상태입니다.
     * @param list<array<string, mixed>>|null $attachment 심사 의견에 첨부된 파일 목록입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 심사 의견 ID입니다.
         */
        public ?int $id = null,
        /**
         * 심사 의견을 남긴 담당자명입니다.
         */
        public ?string $userName = null,
        /**
         * 심사 의견 등록일입니다.
         *
         * 확인 필요(x-unverified): 날짜 형식이 문서에 없습니다.
         */
        public ?string $createdAt = null,
        /**
         * 해당 의견 시점의 심사 상태입니다.
         */
        public ?string $status = null,
        /**
         * 심사 의견에 첨부된 파일 목록입니다.
         *
         * 확인 필요(x-unverified): 첨부 파일 항목의 하위 필드가 문서에 없습니다.
         */
        public ?array $attachment = null,
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
            id: $in->int('id'),
            userName: $in->string('userName'),
            createdAt: $in->string('createdAt'),
            status: $in->string('status'),
            attachment: $in->objectList('attachment'),
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
            'userName' => $this->userName,
            'createdAt' => $this->createdAt,
            'status' => $this->status,
            'attachment' => $this->attachment,
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
        return Mask::describe('AlimtalkTemplateComment', get_object_vars($this));
    }
}
