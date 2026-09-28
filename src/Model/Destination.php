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
 * 수신자 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class Destination implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['to', 'replaceWords', 'ref'];

    /**
     * @param string $to 수신번호입니다.
     * @param array<string, string>|null $replaceWords 치환메시지 변수입니다.
     * @param string|null $ref 수신자별 참조 필드입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 수신번호입니다. 휴대폰 번호는 11자리 형식을 씁니다(국제메시지는 채널 규격 참고).
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $to,
        /**
         * 치환메시지 변수입니다. 본문·제목의 `#{key}`를 이 값으로 바꿉니다. 알림톡·브랜드메시지 템플릿 자동 치환 발송(`sendType`이 `template`)에서는
         * 필수입니다(각 채널 스키마의 `x-sdk-required-if`).
         */
        #[\SensitiveParameter]
        public ?array $replaceWords = null,
        /**
         * 수신자별 참조 필드입니다. 값이 있으면 이 수신번호에는 요청 단위 `ref` 대신 이 값이 리포트에 담깁니다.
         */
        #[\SensitiveParameter]
        public ?string $ref = null,
    ) {
        Check::all(
            Check::stringMap('replaceWords', $replaceWords),
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
        $to = $in->requiredString('to');
        $replaceWords = $in->stringMap('replaceWords');
        $ref = $in->string('ref');

        return $in->create(static fn (): static => new static(
            to: $to,
            replaceWords: $replaceWords,
            ref: $ref,
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
            'to' => $this->to,
            'replaceWords' => $this->replaceWords,
            'ref' => $this->ref,
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
            'to' => $this->to,
            'replaceWords' => Serialize::jsonObject($this->replaceWords),
            'ref' => $this->ref,
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
        return Mask::describe('Destination', get_object_vars($this));
    }
}
