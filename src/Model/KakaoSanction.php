<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 발신프로필·템플릿 제재 정보입니다. 제재가 적용되면 발송이 불가하거나 일부 기능이 제한될 수 있습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class KakaoSanction implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['restrictedAt', 'senderKey', 'templateCode', 'groupKey', 'senderKeyType', 'reasonType'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $restrictedAt 제재 적용 시각(yyyy-MM-dd HH:mm:ss)입니다.
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $templateCode 템플릿 코드입니다.
     * @param string|null $groupKey 발신프로필 그룹 키입니다.
     * @param string|null $senderKeyType 발신프로필 키 타입입니다.
     * @param string|null $reasonType 제재 사유입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 제재 적용 시각(yyyy-MM-dd HH:mm:ss)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $restrictedAt = null,
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 템플릿 코드입니다.
         */
        public ?string $templateCode = null,
        /**
         * 발신프로필 그룹 키입니다.
         */
        public ?string $groupKey = null,
        /**
         * 발신프로필 키 타입입니다.
         * - `G`: 그룹
         * - `S`: 발신프로필
         *
         * 값: `G`, `S`
         */
        public ?string $senderKeyType = null,
        /**
         * 제재 사유입니다. 알려진 값:
         * - `BZM`: 비즈메시지 운영정책 위반(스팸/어뷰징)
         * - `CHANNEL`: 채널 운영정책 위반
         *
         * 알려진 값: `BZM`, `CHANNEL`
         */
        public ?string $reasonType = null,
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
            restrictedAt: $in->string('restrictedAt'),
            senderKey: $in->string('senderKey'),
            templateCode: $in->string('templateCode'),
            groupKey: $in->string('groupKey'),
            senderKeyType: $in->string('senderKeyType'),
            reasonType: $in->string('reasonType'),
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
            'restrictedAt' => $this->restrictedAt,
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'groupKey' => $this->groupKey,
            'senderKeyType' => $this->senderKeyType,
            'reasonType' => $this->reasonType,
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
        return Mask::describe('KakaoSanction', get_object_vars($this));
    }
}
