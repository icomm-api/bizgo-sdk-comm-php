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
 * 일정 등록 객체입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsCalendarActionCreateCalendarEvent implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['startTime', 'endTime', 'title', 'description'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['startTime', 'endTime', 'title'];

    /**
     * @param string|null $startTime 시작 시간입니다.
     * @param string|null $endTime 종료 시간입니다.
     * @param string|null $title 일정 제목입니다.
     * @param string|null $description 일정 설명입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 시작 시간입니다.
         *
         * 확인 필요(x-unverified): 시각 형식이 문서화되어 있지 않습니다.
         */
        public ?string $startTime = null,
        /**
         * 종료 시간입니다.
         *
         * 확인 필요(x-unverified): 시각 형식이 문서화되어 있지 않습니다.
         */
        public ?string $endTime = null,
        /**
         * 일정 제목입니다.
         */
        public ?string $title = null,
        /**
         * 일정 설명입니다.
         */
        public ?string $description = null,
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
            startTime: $in->string('startTime'),
            endTime: $in->string('endTime'),
            title: $in->string('title'),
            description: $in->string('description'),
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
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'title' => $this->title,
            'description' => $this->description,
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
        return Mask::describe('RcsCalendarActionCreateCalendarEvent', get_object_vars($this));
    }
}
