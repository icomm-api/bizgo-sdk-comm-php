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
 * 알림톡 아이템 요약 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkItemSummary implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'description'];

    /**
     * @param string $title 요약 타이틀입니다.
     * @param string $description 요약 가격정보입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 요약 타이틀입니다. 최대 6자입니다.
         *
         * 필수 · 최대 6자
         */
        #[\SensitiveParameter]
        public string $title,
        /**
         * 요약 가격정보입니다. 최대 14자입니다.
         * 허용 문자: 통화 기호(유니코드 통화 기호, 元, 円, 원), 통화 코드(ISO 4217), 숫자, 쉼표, 소수점, 공백. 소수점 이하는 2자리까지 허용합니다.
         *
         * 필수 · 최대 14자
         */
        #[\SensitiveParameter]
        public string $description,
    ) {
        Check::all(
            Check::maxLength('title', $title, 6),
            Check::maxLength('description', $description, 14),
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
        $title = $in->requiredString('title');
        $description = $in->requiredString('description');

        return $in->create(static fn (): static => new static(
            title: $title,
            description: $description,
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
            'title' => $this->title,
            'description' => $this->description,
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
            'title' => $this->title,
            'description' => $this->description,
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
        return Mask::describe('AlimtalkItemSummary', get_object_vars($this));
    }
}
