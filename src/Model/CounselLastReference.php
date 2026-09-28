<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 현재 메타 정보가 없을 때 전달되는 가장 마지막 메타 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselLastReference implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['extra', 'bot', 'bot_event', 'created_at'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $extraField 이전 상담 연결 버튼으로 전달된 메타 정보입니다.
     * @param string|null $bot 상담을 어떻게 시작했는지 나타내는 값입니다.
     * @param string|null $bot_event 봇으로 상담을 시작했을 때 전달되는 봇 블록 이벤트 값입니다.
     * @param string|null $created_at 마지막 메타 정보 생성 시각입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 이전 상담 연결 버튼으로 전달된 메타 정보입니다.
         */
        public ?string $extraField = null,
        /**
         * 상담을 어떻게 시작했는지 나타내는 값입니다. 문서 예시 값은 문자열 `false`입니다.
         */
        public ?string $bot = null,
        /**
         * 봇으로 상담을 시작했을 때 전달되는 봇 블록 이벤트 값입니다.
         */
        public ?string $bot_event = null,
        /**
         * 마지막 메타 정보 생성 시각입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss.SSSXXX`
         */
        public ?string $created_at = null,
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
            extraField: $in->string('extra'),
            bot: $in->string('bot'),
            bot_event: $in->string('bot_event'),
            created_at: $in->string('created_at'),
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
            'extra' => $this->extraField,
            'bot' => $this->bot,
            'bot_event' => $this->bot_event,
            'created_at' => $this->created_at,
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
        return Mask::describe('CounselLastReference', get_object_vars($this));
    }
}
