<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * API Gateway 공통 결과(인증·권한·요청 형식 검증)입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CommonResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['authCode', 'authResult', 'infobankTrId'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['authCode', 'authResult'];

    /**
     * @param string|null $authCode 인증 결과 코드입니다.
     * @param string|null $authResult 인증 결과 메시지입니다.
     * @param string|null $infobankTrId 인포뱅크 트랜잭션 추적 ID입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 인증 결과 코드입니다. `A000`이면 성공입니다.
         */
        public ?string $authCode = null,
        /**
         * 인증 결과 메시지입니다.
         */
        public ?string $authResult = null,
        /**
         * 인포뱅크 트랜잭션 추적 ID입니다. 문의 시 이 값을 전달합니다.
         */
        public ?string $infobankTrId = null,
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
            authCode: $in->string('authCode'),
            authResult: $in->string('authResult'),
            infobankTrId: $in->string('infobankTrId'),
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
            'authCode' => $this->authCode,
            'authResult' => $this->authResult,
            'infobankTrId' => $this->infobankTrId,
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
        return Mask::describe('CommonResult', get_object_vars($this));
    }
}
