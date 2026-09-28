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
 * 전화번호 명단 기반 발송 예상 모수 확인 요청입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageAudiencePossibleByPhoneNumbersRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['senderKey', 'phoneNumbers'];

    /**
     * @param string $senderKey 발신프로필 키입니다.
     * @param list<string> $phoneNumbers 발송 가능 여부를 확인할 전화번호 목록입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 발송 가능 여부를 확인할 전화번호 목록입니다. 최소 10건이며, 하나라도 형식이 잘못되면 요청 전체가 거부됩니다. 국가코드는 자동 정규화됩니다(010 / +82 / 82 →
         * 82).
         *
         * 필수 · 항목 최소 10개
         */
        #[\SensitiveParameter]
        public array $phoneNumbers,
    ) {
        Check::all(
            Check::items('phoneNumbers', $phoneNumbers, 10, null),
            Check::stringList('phoneNumbers', $phoneNumbers),
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
        $phoneNumbers = $in->requiredStringList('phoneNumbers');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            phoneNumbers: $phoneNumbers,
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
            'phoneNumbers' => $this->phoneNumbers,
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
            'phoneNumbers' => $this->phoneNumbers,
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
        return Mask::describe('BrandMessageAudiencePossibleByPhoneNumbersRequest', get_object_vars($this));
    }
}
