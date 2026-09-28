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
 * 인증 토큰으로 발신프로필을 등록하는 요청 본문입니다. 인증 토큰과 관리자 전화번호는 헤더(`token`, `phoneNumber`)로 보냅니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class KakaoSenderCreateRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['yellowId', 'categoryCode', 'unsubscribePhoneNumber', 'unsubscribeAuthNumber'];

    /**
     * @param string $yellowId 등록할 카카오톡 채널 아이디입니다.
     * @param string $categoryCode 발신프로필 카테고리 코드(11자리 숫자)입니다.
     * @param string|null $unsubscribePhoneNumber 무료수신거부 전화번호입니다(예 `080-0000-0000`).
     * @param string|null $unsubscribeAuthNumber 무료수신거부 인증번호입니다(예 `12345`).
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 등록할 카카오톡 채널 아이디입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $yellowId,
        /**
         * 발신프로필 카테고리 코드(11자리 숫자)입니다. 사용할 수 있는 코드는 카테고리 전체조회로 확인합니다.
         *
         * 필수 · 형식 `^[0-9]{11}$`
         */
        #[\SensitiveParameter]
        public string $categoryCode,
        /**
         * 무료수신거부 전화번호입니다(예 `080-0000-0000`). 브랜드메시지를 쓰는 발신프로필이 설정하며, 타겟팅 M, N, O 사용 시 필수입니다.
         */
        #[\SensitiveParameter]
        public ?string $unsubscribePhoneNumber = null,
        /**
         * 무료수신거부 인증번호입니다(예 `12345`). 타겟팅 M, N, O 사용 시 필수입니다.
         */
        #[\SensitiveParameter]
        public ?string $unsubscribeAuthNumber = null,
    ) {
        Check::all(
            Check::pattern('categoryCode', $categoryCode, '^[0-9]{11}$'),
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
        $yellowId = $in->requiredString('yellowId');
        $categoryCode = $in->requiredString('categoryCode');
        $unsubscribePhoneNumber = $in->string('unsubscribePhoneNumber');
        $unsubscribeAuthNumber = $in->string('unsubscribeAuthNumber');

        return $in->create(static fn (): static => new static(
            yellowId: $yellowId,
            categoryCode: $categoryCode,
            unsubscribePhoneNumber: $unsubscribePhoneNumber,
            unsubscribeAuthNumber: $unsubscribeAuthNumber,
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
            'categoryCode' => $this->categoryCode,
            'unsubscribePhoneNumber' => $this->unsubscribePhoneNumber,
            'unsubscribeAuthNumber' => $this->unsubscribeAuthNumber,
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
            'categoryCode' => $this->categoryCode,
            'unsubscribePhoneNumber' => $this->unsubscribePhoneNumber,
            'unsubscribeAuthNumber' => $this->unsubscribeAuthNumber,
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
        return Mask::describe('KakaoSenderCreateRequest', get_object_vars($this));
    }
}
