<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 친구 그룹 상세 정보입니다. 동보 발송에 쓰려면 상태가 완료이고 `userCount`가 10 이상이어야 합니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageFriendGroup implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['friendGroupKey', 'userCount', 'friendCount', 'status', 'createdAt', 'modifiedAt'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $friendGroupKey 친구 그룹 키입니다.
     * @param int|null $userCount 등록 유저 수입니다.
     * @param int|null $friendCount 친구 수입니다.
     * @param string|null $status 친구 그룹 처리 상태입니다.
     * @param string|null $createdAt 생성 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
     * @param string|null $modifiedAt 수정 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 친구 그룹 키입니다.
         */
        public ?string $friendGroupKey = null,
        /**
         * 등록 유저 수입니다.
         */
        public ?int $userCount = null,
        /**
         * 친구 수입니다. `status`가 `IN_PROGRESS`이면 없을 수 있습니다.
         */
        public ?int $friendCount = null,
        /**
         * 친구 그룹 처리 상태입니다. 알려진 값: `IN_PROGRESS`, `COMPLETED`.
         *
         * 알려진 값: `IN_PROGRESS`, `COMPLETED`
         *
         * 확인 필요(x-unverified): 동보 발송 안내는 그룹 상태를 `C`(Completed)로 표기하지만 응답 값은 `COMPLETED`입니다. 같은 값인지 확인이
         * 필요합니다.
         */
        public ?string $status = null,
        /**
         * 생성 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $createdAt = null,
        /**
         * 수정 일시(`yyyy-MM-dd HH:mm:ss`)입니다.
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
            userCount: $in->int('userCount'),
            friendCount: $in->int('friendCount'),
            status: $in->string('status'),
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
            'userCount' => $this->userCount,
            'friendCount' => $this->friendCount,
            'status' => $this->status,
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
        return Mask::describe('BrandMessageFriendGroup', get_object_vars($this));
    }
}
