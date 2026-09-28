<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 그룹에 발신프로필 등록 요청 본문입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class KakaoGroupSenderAddRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['groupKey', 'senderKey', 'groupName'];

    /**
     * @param string $groupKey 발신프로필을 등록할 그룹 키입니다.
     * @param string $senderKey 등록할 발신프로필 키입니다.
     * @param string|null $groupName 그룹 이름입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신프로필을 등록할 그룹 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $groupKey,
        /**
         * 등록할 발신프로필 키입니다. 최대 40자입니다.
         *
         * 필수 · 최대 40자
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 그룹 이름입니다.
         */
        #[\SensitiveParameter]
        public ?string $groupName = null,
    ) {
        Check::all(
            Check::maxLength('senderKey', $senderKey, 40),
        );
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
        $groupKey = $in->requiredString('groupKey');
        $senderKey = $in->requiredString('senderKey');
        $groupName = $in->string('groupName');

        return $in->create(static fn (): static => new static(
            groupKey: $groupKey,
            senderKey: $senderKey,
            groupName: $groupName,
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
            'groupKey' => $this->groupKey,
            'senderKey' => $this->senderKey,
            'groupName' => $this->groupName,
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
            'groupKey' => $this->groupKey,
            'senderKey' => $this->senderKey,
            'groupName' => $this->groupName,
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
        return Mask::describe('KakaoGroupSenderAddRequest', get_object_vars($this));
    }
}
