<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 친구 그룹 등록, 전화번호 추가·삭제 요청입니다. 전화번호는 `fileKey` 또는 `phoneNumbers`로 전달합니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageFriendGroupRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['friendGroup'];

    /**
     * @param BrandMessageFriendGroupRequestFriendGroup $friendGroup 친구 그룹 정보입니다.
     */
    public function __construct(
        /**
         * 친구 그룹 정보입니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): `fileKey`와 `phoneNumbers`를 둘 다 보내거나 둘 다 생략할 때의 동작이 문서에 없습니다. 친구 그룹 등록 시
         * `friendGroupKey`를 고객이 정하는지(요청 필수)만 명시되어 있습니다.
         */
        #[\SensitiveParameter]
        public BrandMessageFriendGroupRequestFriendGroup $friendGroup,
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
        $friendGroup = $in->model('friendGroup', BrandMessageFriendGroupRequestFriendGroup::class, true);

        return $in->create(static fn (): static => new static(
            friendGroup: $friendGroup ?? throw new \LogicException('checked by RequestData::create()'),
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
            'friendGroup' => $this->friendGroup->toArray(),
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
            'friendGroup' => $this->friendGroup,
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
        return Mask::describe('BrandMessageFriendGroupRequest', get_object_vars($this));
    }
}
