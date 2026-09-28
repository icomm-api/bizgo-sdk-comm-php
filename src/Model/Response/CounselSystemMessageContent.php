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
 * 시스템 메시지 1건의 내용입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselSystemMessageContent implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['messageType', 'content', 'buttons'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['messageType', 'content', 'buttons'];

    /**
     * @param string|null $messageType 시스템 메시지 타입입니다.
     * @param string|null $content 메시지 내용입니다.
     * @param list<CounselSystemMessageButton>|null $buttons 메시지 하단 버튼 목록입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 시스템 메시지 타입입니다. 문서 예시 값은 `ST`입니다.
         *
         * 알려진 값: `ST`
         *
         * 확인 필요(x-unverified): messageType 코드 목록과 의미가 문서에 없습니다.
         */
        public ?string $messageType = null,
        /**
         * 메시지 내용입니다.
         */
        public ?string $content = null,
        /**
         * 메시지 하단 버튼 목록입니다. 등록 요청에서는 필수로 표시되어 있습니다.
         */
        public ?array $buttons = null,
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
            messageType: $in->string('messageType'),
            content: $in->string('content'),
            buttons: $in->modelList('buttons', CounselSystemMessageButton::class),
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
            'messageType' => $this->messageType,
            'content' => $this->content,
            'buttons' => Serialize::models($this->buttons),
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
        return Mask::describe('CounselSystemMessageContent', get_object_vars($this));
    }
}
