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
 * 요일별 상담 시간입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselWeekTime implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['day', 'startAt', 'endAt'];

    /**
     * @param string $day 요일입니다.
     * @param string $startAt 상담 시작 시간입니다.
     * @param string $endAt 상담 종료 시간입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 요일입니다.
         *
         * 필수 · 허용 값: `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `sun`
         */
        #[\SensitiveParameter]
        public string $day,
        /**
         * 상담 시작 시간입니다. 문서 예시는 `0900`입니다.
         *
         * 필수 · 시각 형식 `HHmm`
         */
        #[\SensitiveParameter]
        public string $startAt,
        /**
         * 상담 종료 시간입니다. 문서 예시는 `1800`입니다.
         *
         * 필수 · 시각 형식 `HHmm`
         */
        #[\SensitiveParameter]
        public string $endAt,
    ) {
        Check::all(
            Check::enum('day', $day, ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']),
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
        $day = $in->requiredString('day');
        $startAt = $in->requiredString('startAt');
        $endAt = $in->requiredString('endAt');

        return $in->create(static fn (): static => new static(
            day: $day,
            startAt: $startAt,
            endAt: $endAt,
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
            'day' => $this->day,
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
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
            'day' => $this->day,
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
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
        return Mask::describe('CounselWeekTime', get_object_vars($this));
    }
}
