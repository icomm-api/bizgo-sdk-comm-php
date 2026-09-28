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
 * 삭제할 상담톡 메시지입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselMessageDeleteTarget implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['senderKey', 'userKey', 'msgKey'];

    /**
     * @param string $senderKey 발신프로필 키입니다.
     * @param string $userKey 메시지를 받은 사용자 키입니다.
     * @param string $msgKey 삭제할 메시지의 발송 시 msgKey입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신프로필 키입니다. 호출자 소유가 아니면 `A502`로 거부됩니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 메시지를 받은 사용자 키입니다.
         *
         * 필수 · 최대 20자 · 최소 1자
         */
        #[\SensitiveParameter]
        public string $userKey,
        /**
         * 삭제할 메시지의 발송 시 msgKey입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $msgKey,
    ) {
        Check::all(
            Check::maxLength('userKey', $userKey, 20),
            Check::minLength('userKey', $userKey, 1),
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
        $senderKey = $in->requiredString('senderKey');
        $userKey = $in->requiredString('userKey');
        $msgKey = $in->requiredString('msgKey');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            userKey: $userKey,
            msgKey: $msgKey,
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
            'senderKey' => $this->senderKey,
            'userKey' => $this->userKey,
            'msgKey' => $this->msgKey,
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
            'senderKey' => $this->senderKey,
            'userKey' => $this->userKey,
            'msgKey' => $this->msgKey,
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
        return Mask::describe('CounselMessageDeleteTarget', get_object_vars($this));
    }
}
