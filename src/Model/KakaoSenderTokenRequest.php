<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 카카오 채널 인증 토큰 요청 본문입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class KakaoSenderTokenRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['yellowId', 'phoneNumber'];

    /**
     * @param string $yellowId 카카오톡 채널 아이디입니다(`@`로 시작).
     * @param string $phoneNumber 카카오톡 채널 알림을 받는 관리자 전화번호입니다.
     */
    public function __construct(
        /**
         * 카카오톡 채널 아이디입니다(`@`로 시작).
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $yellowId,
        /**
         * 카카오톡 채널 알림을 받는 관리자 전화번호입니다. 이 번호에 연결된 카카오톡이 채널을 차단하지 않았으면 인증 토큰이 발송됩니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $phoneNumber,
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
        $yellowId = $in->requiredString('yellowId');
        $phoneNumber = $in->requiredString('phoneNumber');

        return $in->create(static fn (): static => new static(
            yellowId: $yellowId,
            phoneNumber: $phoneNumber,
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
            'yellowId' => $this->yellowId,
            'phoneNumber' => $this->phoneNumber,
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
            'yellowId' => $this->yellowId,
            'phoneNumber' => $this->phoneNumber,
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
        return Mask::describe('KakaoSenderTokenRequest', get_object_vars($this));
    }
}
