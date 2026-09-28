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
 * 친구 그룹 정보입니다.
 *
 * 확인 필요(x-unverified): `fileKey`와 `phoneNumbers`를 둘 다 보내거나 둘 다 생략할 때의 동작이 문서에 없습니다. 친구 그룹 등록 시
 * `friendGroupKey`를 고객이 정하는지(요청 필수)만 명시되어 있습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageFriendGroupRequestFriendGroup implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['senderKey', 'friendGroupKey', 'fileKey', 'phoneNumbers'];

    /**
     * @param string $senderKey 발신프로필 키입니다.
     * @param string $friendGroupKey 친구 그룹 키입니다.
     * @param string|null $fileKey 친구 그룹 파일 업로드로 발급받은 임시 파일 키입니다.
     * @param list<string>|null $phoneNumbers 전화번호 목록입니다.
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
         * 친구 그룹 키입니다. 1~50자이며 한글·영문·숫자와 일부 특수문자를 쓸 수 있습니다.
         *
         * 필수 · 최대 50자 · 최소 1자
         */
        #[\SensitiveParameter]
        public string $friendGroupKey,
        /**
         * 친구 그룹 파일 업로드로 발급받은 임시 파일 키입니다.
         */
        #[\SensitiveParameter]
        public ?string $fileKey = null,
        /**
         * 전화번호 목록입니다. 1~10,000건입니다.
         *
         * 항목 1~10000개
         */
        #[\SensitiveParameter]
        public ?array $phoneNumbers = null,
    ) {
        Check::all(
            Check::maxLength('friendGroupKey', $friendGroupKey, 50),
            Check::minLength('friendGroupKey', $friendGroupKey, 1),
            Check::items('phoneNumbers', $phoneNumbers, 1, 10000),
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
        $friendGroupKey = $in->requiredString('friendGroupKey');
        $fileKey = $in->string('fileKey');
        $phoneNumbers = $in->stringList('phoneNumbers');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            friendGroupKey: $friendGroupKey,
            fileKey: $fileKey,
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
            'friendGroupKey' => $this->friendGroupKey,
            'fileKey' => $this->fileKey,
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
            'friendGroupKey' => $this->friendGroupKey,
            'fileKey' => $this->fileKey,
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
        return Mask::describe('BrandMessageFriendGroupRequestFriendGroup', get_object_vars($this));
    }
}
