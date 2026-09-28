<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 등록된 시스템 메시지입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselSystemMessage implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'id',
        'name',
        'status',
        'createdAt',
        'modifiedAt',
        'inspectStatus',
        'inspectRequestAt',
        'inspectedAt',
        'messages',
        'comments',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $id 시스템 메시지 ID입니다.
     * @param string|null $name 시스템 메시지 이름입니다.
     * @param string|null $status 시스템 메시지 상태입니다.
     * @param string|null $createdAt 등록일입니다.
     * @param string|null $modifiedAt 수정일입니다.
     * @param string|null $inspectStatus 검수 상태입니다.
     * @param string|null $inspectRequestAt 검수 요청일입니다.
     * @param string|null $inspectedAt 검수일입니다.
     * @param list<Response\CounselSystemMessageContent>|null $messages 시스템 메시지 배열입니다.
     * @param list<CounselSystemMessageComment>|null $comments 검수 결과 및 문의 목록입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 시스템 메시지 ID입니다.
         */
        public ?string $id = null,
        /**
         * 시스템 메시지 이름입니다.
         */
        public ?string $name = null,
        /**
         * 시스템 메시지 상태입니다. 문서 예시 값은 `A`입니다.
         *
         * 알려진 값: `A`
         *
         * 확인 필요(x-unverified): status·inspectStatus 코드 목록이 문서에 없습니다.
         */
        public ?string $status = null,
        /**
         * 등록일입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $createdAt = null,
        /**
         * 수정일입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $modifiedAt = null,
        /**
         * 검수 상태입니다. 문서 예시 값은 `REG`입니다.
         *
         * 알려진 값: `REG`
         */
        public ?string $inspectStatus = null,
        /**
         * 검수 요청일입니다. 값이 없으면 빈 문자열일 수 있습니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $inspectRequestAt = null,
        /**
         * 검수일입니다. 값이 없으면 빈 문자열일 수 있습니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $inspectedAt = null,
        /**
         * 시스템 메시지 배열입니다.
         */
        public ?array $messages = null,
        /**
         * 검수 결과 및 문의 목록입니다.
         */
        public ?array $comments = null,
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
            name: $in->string('name'),
            status: $in->string('status'),
            createdAt: $in->string('createdAt'),
            modifiedAt: $in->string('modifiedAt'),
            inspectStatus: $in->string('inspectStatus'),
            inspectRequestAt: $in->string('inspectRequestAt'),
            inspectedAt: $in->string('inspectedAt'),
            messages: $in->modelList('messages', Response\CounselSystemMessageContent::class),
            comments: $in->modelList('comments', CounselSystemMessageComment::class),
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
            'name' => $this->name,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt,
            'inspectStatus' => $this->inspectStatus,
            'inspectRequestAt' => $this->inspectRequestAt,
            'inspectedAt' => $this->inspectedAt,
            'messages' => Serialize::models($this->messages),
            'comments' => Serialize::models($this->comments),
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
        return Mask::describe('CounselSystemMessage', get_object_vars($this));
    }
}
