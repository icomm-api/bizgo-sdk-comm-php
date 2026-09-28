<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 친구 그룹 전화번호 추가·삭제 요청 1건의 처리 상태입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageFriendGroupPhoneNumberRequest implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'friendGroupKey',
        'requestId',
        'type',
        'status',
        'processedCount',
        'createdAt',
        'modifiedAt',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $friendGroupKey 친구 그룹 키입니다.
     * @param string|null $requestId 요청 아이디입니다.
     * @param string|null $type 요청 유형입니다.
     * @param string|null $status 요청 처리 상태입니다.
     * @param int|null $processedCount 처리된 전화번호 건수입니다.
     * @param string|null $createdAt 요청 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
     * @param string|null $modifiedAt 상태 변경 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 친구 그룹 키입니다.
         */
        public ?string $friendGroupKey = null,
        /**
         * 요청 아이디입니다.
         */
        public ?string $requestId = null,
        /**
         * 요청 유형입니다. 문서 예시 값은 `ADD`입니다.
         *
         * 알려진 값: `ADD`
         *
         * 확인 필요(x-unverified): 요청 유형 값 목록이 문서에 없습니다(예시는 `ADD`만 있음. 삭제 요청의 값은 미확인).
         */
        public ?string $type = null,
        /**
         * 요청 처리 상태입니다. 문서 예시 값은 `COMPLETED`입니다.
         *
         * 알려진 값: `IN_PROGRESS`, `COMPLETED`
         */
        public ?string $status = null,
        /**
         * 처리된 전화번호 건수입니다.
         */
        public ?int $processedCount = null,
        /**
         * 요청 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $createdAt = null,
        /**
         * 상태 변경 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $modifiedAt = null,
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
            friendGroupKey: $in->string('friendGroupKey'),
            requestId: $in->string('requestId'),
            type: $in->string('type'),
            status: $in->string('status'),
            processedCount: $in->int('processedCount'),
            createdAt: $in->string('createdAt'),
            modifiedAt: $in->string('modifiedAt'),
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
            'friendGroupKey' => $this->friendGroupKey,
            'requestId' => $this->requestId,
            'type' => $this->type,
            'status' => $this->status,
            'processedCount' => $this->processedCount,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt,
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
        return Mask::describe('BrandMessageFriendGroupPhoneNumberRequest', get_object_vars($this));
    }
}
