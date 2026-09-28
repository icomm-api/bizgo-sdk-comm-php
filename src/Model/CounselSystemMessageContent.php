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
 * 시스템 메시지 1건의 내용입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselSystemMessageContent implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['messageType', 'content', 'buttons'];

    /**
     * @param string $messageType 시스템 메시지 타입입니다.
     * @param string $content 메시지 내용입니다.
     * @param list<CounselSystemMessageButton> $buttons 메시지 하단 버튼 목록입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 시스템 메시지 타입입니다. 문서 예시 값은 `ST`입니다.
         *
         * 필수 · 알려진 값: `ST`
         *
         * 확인 필요(x-unverified): messageType 코드 목록과 의미가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $messageType,
        /**
         * 메시지 내용입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $content,
        /**
         * 메시지 하단 버튼 목록입니다. 등록 요청에서는 필수로 표시되어 있습니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public array $buttons,
    ) {
        Check::all(
            Check::listOf('buttons', $buttons, [CounselSystemMessageButton::class]),
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
        $messageType = $in->requiredString('messageType');
        $content = $in->requiredString('content');
        $buttons = $in->modelList('buttons', CounselSystemMessageButton::class, true);

        return $in->create(static fn (): static => new static(
            messageType: $messageType,
            content: $content,
            buttons: $buttons ?? throw new \LogicException('checked by RequestData::create()'),
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
            'messageType' => $this->messageType,
            'content' => $this->content,
            'buttons' => Serialize::models($this->buttons),
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
            'messageType' => $this->messageType,
            'content' => $this->content,
            'buttons' => $this->buttons,
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
        return Mask::describe('CounselSystemMessageContent', get_object_vars($this));
    }
}
