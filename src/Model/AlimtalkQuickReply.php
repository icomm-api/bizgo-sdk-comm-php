<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 알림톡 바로연결 1개입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkQuickReply implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'type',
        'name',
        'urlPc',
        'urlMobile',
        'schemeIos',
        'schemeAndroid',
        'chatExtra',
        'chatEvent',
        'bizFormId',
    ];

    /**
     * @param string $type 바로연결 타입 코드입니다.
     * @param string $name 바로연결 제목입니다.
     * @param string|null $urlPc PC 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $urlMobile 모바일 환경에서 클릭 시 이동할 URL입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $chatExtra 봇/상담톡 전환 시 전달할 메타 정보입니다.
     * @param string|null $chatEvent 봇/상담톡 전환 시 연결할 이벤트명입니다.
     * @param string|null $bizFormId 비즈폼 ID입니다.
     */
    public function __construct(
        /**
         * 바로연결 타입 코드입니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): 바로연결 타입 코드 목록이 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $type,
        /**
         * 바로연결 제목입니다. 템플릿 등록 규격상 최대 14자입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $name,
        /**
         * PC 환경에서 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * 모바일 환경에서 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * iOS 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
        /**
         * 봇/상담톡 전환 시 전달할 메타 정보입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatExtra = null,
        /**
         * 봇/상담톡 전환 시 연결할 이벤트명입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatEvent = null,
        /**
         * 비즈폼 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $bizFormId = null,
    ) {
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
        $type = $in->requiredString('type');
        $name = $in->requiredString('name');
        $urlPc = $in->string('urlPc');
        $urlMobile = $in->string('urlMobile');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');
        $chatExtra = $in->string('chatExtra');
        $chatEvent = $in->string('chatEvent');
        $bizFormId = $in->string('bizFormId');

        return $in->create(static fn (): static => new static(
            type: $type,
            name: $name,
            urlPc: $urlPc,
            urlMobile: $urlMobile,
            schemeIos: $schemeIos,
            schemeAndroid: $schemeAndroid,
            chatExtra: $chatExtra,
            chatEvent: $chatEvent,
            bizFormId: $bizFormId,
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
            'type' => $this->type,
            'name' => $this->name,
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'chatExtra' => $this->chatExtra,
            'chatEvent' => $this->chatEvent,
            'bizFormId' => $this->bizFormId,
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
            'type' => $this->type,
            'name' => $this->name,
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
            'chatExtra' => $this->chatExtra,
            'chatEvent' => $this->chatEvent,
            'bizFormId' => $this->bizFormId,
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
        return Mask::describe('AlimtalkQuickReply', get_object_vars($this));
    }
}
