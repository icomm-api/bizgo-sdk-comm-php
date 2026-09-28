<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 목록의 항목 1개입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageTemplateSummary implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['senderKey', 'senderKeyType', 'templateCode', 'status'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $senderKeyType 발신키 유형입니다.
     * @param string|null $templateCode 템플릿 코드입니다.
     * @param string|null $status 템플릿 상태입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 발신키 유형입니다. 문서 예시 값은 `S`(발신프로필)입니다. 기본값은 `S`입니다.
         */
        public ?string $senderKeyType = null,
        /**
         * 템플릿 코드입니다.
         */
        public ?string $templateCode = null,
        /**
         * 템플릿 상태입니다. 알려진 값: `A`(등록), `S`(차단). 최근 변경 템플릿 조회 응답에는 없습니다.
         *
         * 알려진 값: `A`, `S`
         */
        public ?string $status = null,
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
            senderKey: $in->string('senderKey'),
            senderKeyType: $in->string('senderKeyType'),
            templateCode: $in->string('templateCode'),
            status: $in->string('status'),
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
            'senderKey' => $this->senderKey,
            'senderKeyType' => $this->senderKeyType,
            'templateCode' => $this->templateCode,
            'status' => $this->status,
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
        return Mask::describe('BrandMessageTemplateSummary', get_object_vars($this));
    }
}
