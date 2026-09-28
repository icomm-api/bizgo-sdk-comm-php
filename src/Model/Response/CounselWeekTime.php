<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model\Response;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;
use Bizgo\Model\ResponseModel;

/**
 * 요일별 상담 시간입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselWeekTime implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['day', 'startAt', 'endAt'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['day', 'startAt', 'endAt'];

    /**
     * @param string|null $day 요일입니다.
     * @param string|null $startAt 상담 시작 시간입니다.
     * @param string|null $endAt 상담 종료 시간입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 요일입니다.
         *
         * 값: `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `sun`
         */
        public ?string $day = null,
        /**
         * 상담 시작 시간입니다. 문서 예시는 `0900`입니다.
         *
         * 시각 형식 `HHmm`
         */
        public ?string $startAt = null,
        /**
         * 상담 종료 시간입니다. 문서 예시는 `1800`입니다.
         *
         * 시각 형식 `HHmm`
         */
        public ?string $endAt = null,
        /** Fields the spec does not describe (new server fields), by API name. */
        public array $extra = [],
    ) {
    }

    /**
     * @param array<array-key, mixed> $data decoded JSON object
     *
     * @throws InvalidResponseException when a field has an incompatible type
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new ResponseData($data, $path, self::FIELDS);

        return new static(
            day: $in->string('day'),
            startAt: $in->string('startAt'),
            endAt: $in->string('endAt'),
            extra: $in->extra(),
        );
    }

    /**
     * The fields with API names, followed by the unknown fields from `$extra`. Unset fields are omitted.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Serialize::compact([
            'day' => $this->day,
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
        ]) + $this->extra;
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
