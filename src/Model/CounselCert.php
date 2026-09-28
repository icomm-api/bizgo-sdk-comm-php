<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 카카오톡 인증(전자서명) 진행 상태입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselCert implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['txId', 'sessionId', 'signStatus', 'createdAt', 'viewedAt', 'completedAt', 'expiredAt'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $txId 인증 트랜잭션 ID입니다.
     * @param int|null $sessionId 세션 아이디입니다.
     * @param string|null $signStatus 서명 상태입니다.
     * @param string|null $createdAt 서명 요청 시각입니다.
     * @param string|null $viewedAt 사용자가 서명 내용을 확인한 시각입니다.
     * @param string|null $completedAt 서명 완료 시각입니다.
     * @param string|null $expiredAt 서명 만료 시각입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 인증 트랜잭션 ID입니다.
         */
        public ?string $txId = null,
        /**
         * 세션 아이디입니다.
         */
        public ?int $sessionId = null,
        /**
         * 서명 상태입니다. 문서 예시 값은 `COMPLETED`입니다.
         *
         * 알려진 값: `COMPLETED`
         *
         * 확인 필요(x-unverified): signStatus 값 목록이 문서에 없습니다.
         */
        public ?string $signStatus = null,
        /**
         * 서명 요청 시각입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss`
         */
        public ?string $createdAt = null,
        /**
         * 사용자가 서명 내용을 확인한 시각입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss`
         */
        public ?string $viewedAt = null,
        /**
         * 서명 완료 시각입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss`
         */
        public ?string $completedAt = null,
        /**
         * 서명 만료 시각입니다.
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
            txId: $in->string('txId'),
            sessionId: $in->int('sessionId'),
            signStatus: $in->string('signStatus'),
            createdAt: $in->string('createdAt'),
            viewedAt: $in->string('viewedAt'),
            completedAt: $in->string('completedAt'),
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
            'txId' => $this->txId,
            'sessionId' => $this->sessionId,
            'signStatus' => $this->signStatus,
            'createdAt' => $this->createdAt,
            'viewedAt' => $this->viewedAt,
            'completedAt' => $this->completedAt,
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
        return Mask::describe('CounselCert', get_object_vars($this));
    }
}
