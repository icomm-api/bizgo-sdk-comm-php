<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 상담 세션 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselSession implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['sessionId', 'senderKey', 'startedType', 'startedAt', 'expiredType', 'expiredAt'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param int|null $sessionId 세션 아이디입니다.
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $startedType 세션 시작 타입입니다.
     * @param string|null $startedAt 세션 시작 시각입니다.
     * @param string|null $expiredType 세션 종료 타입입니다.
     * @param string|null $expiredAt 세션 종료 시각입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 세션 아이디입니다.
         *
         * 확인 필요(x-unverified): 세션 조회 응답은 Integer, 웹훅의 sessionId는 String으로 문서가 타입을 다르게 적습니다.
         */
        public ?int $sessionId = null,
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 세션 시작 타입입니다. 문서 예시 값은 `UM`입니다.
         *
         * 알려진 값: `UM`
         *
         * 확인 필요(x-unverified): startedType·expiredType 코드 목록과 의미가 문서에 없습니다.
         */
        public ?string $startedType = null,
        /**
         * 세션 시작 시각입니다. 문서 예시는 오프셋 없는 ISO 8601 형식(`2025-09-24T11:22:08.557`)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss.SSS`
         */
        public ?string $startedAt = null,
        /**
         * 세션 종료 타입입니다. 문서 예시 값은 `AE`입니다.
         *
         * 알려진 값: `AE`
         */
        public ?string $expiredType = null,
        /**
         * 세션 종료 시각입니다. 현재 시각보다 크면 종료 예정 시각입니다(마지막 메시지 수신 시각 기준 다음 정각으로부터 30일 후).
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss`
         */
        public ?string $expiredAt = null,
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
            sessionId: $in->int('sessionId'),
            senderKey: $in->string('senderKey'),
            startedType: $in->string('startedType'),
            startedAt: $in->string('startedAt'),
            expiredType: $in->string('expiredType'),
            expiredAt: $in->string('expiredAt'),
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
            'sessionId' => $this->sessionId,
            'senderKey' => $this->senderKey,
            'startedType' => $this->startedType,
            'startedAt' => $this->startedAt,
            'expiredType' => $this->expiredType,
            'expiredAt' => $this->expiredAt,
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
        return Mask::describe('CounselSession', get_object_vars($this));
    }
}
