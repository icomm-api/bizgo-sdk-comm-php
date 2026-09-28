<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 *
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class MoMessageListResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['lastSeq', 'hasNext', 'messages'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param int|null $lastSeq 다음 페이지 조회에 쓰는 커서입니다(MO 이력 조회).
     * @param bool|null $hasNext 다음 페이지가 있는지 여부입니다(MO 이력 조회).
     * @param list<MoMessage>|null $messages MO 메시지 목록입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 다음 페이지 조회에 쓰는 커서입니다(MO 이력 조회).
         */
        public ?int $lastSeq = null,
        /**
         * 다음 페이지가 있는지 여부입니다(MO 이력 조회).
         */
        public ?bool $hasNext = null,
        /**
         * MO 메시지 목록입니다.
         */
        public ?array $messages = null,
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
            lastSeq: $in->int('lastSeq'),
            hasNext: $in->bool('hasNext'),
            messages: $in->modelList('messages', MoMessage::class),
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
            'lastSeq' => $this->lastSeq,
            'hasNext' => $this->hasNext,
            'messages' => Serialize::models($this->messages),
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
        return Mask::describe('MoMessageListResult', get_object_vars($this));
    }
}
