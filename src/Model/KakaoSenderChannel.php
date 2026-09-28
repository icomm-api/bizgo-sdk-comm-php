<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 발신프로필 목록 조회(`GET /account/kakao/sender/profiles`)의 항목 1개입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class KakaoSenderChannel implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'senderKeyType',
        'channelName',
        'channelId',
        'channelKey',
        'categoryCode',
        'status',
        'profileStatus',
        'dormant',
        'block',
        'businessProfile',
        'businessType',
        'createdAt',
        'regDate',
        'updateDate',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $senderKeyType 발신키 유형입니다.
     * @param string|null $channelName 카카오톡 채널 이름입니다.
     * @param string|null $channelId 카카오톡 채널 검색용 아이디입니다.
     * @param string|null $channelKey 카카오톡 채널 키입니다.
     * @param string|null $categoryCode 발신프로필 카테고리 코드입니다.
     * @param string|null $status 발신프로필 상태입니다.
     * @param string|null $profileStatus 카카오톡 채널 프로필 상태입니다.
     * @param bool|null $dormant 휴면 상태 여부입니다.
     * @param bool|null $block 차단 상태 여부입니다.
     * @param bool|null $businessProfile 비즈니스 인증 채널 여부입니다.
     * @param string|null $businessType 비즈니스 인증 유형입니다.
     * @param string|null $createdAt 발신프로필 생성 일시(yyyy-MM-dd HH:mm:ss)입니다.
     * @param string|null $regDate 등록 일시(yyyy-MM-dd HH:mm:ss)입니다.
     * @param string|null $updateDate 수정 일시(yyyy-MM-dd HH:mm:ss)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 발신키 유형입니다. 문서 예시 값은 `S`입니다.
         *
         * 알려진 값: `S`
         *
         * 확인 필요(x-unverified): 이 응답의 senderKeyType 값 목록이 문서에 없습니다(다른 API는 S: 발신프로필, G: 그룹).
         */
        public ?string $senderKeyType = null,
        /**
         * 카카오톡 채널 이름입니다.
         */
        public ?string $channelName = null,
        /**
         * 카카오톡 채널 검색용 아이디입니다.
         */
        public ?string $channelId = null,
        /**
         * 카카오톡 채널 키입니다.
         */
        public ?string $channelKey = null,
        /**
         * 발신프로필 카테고리 코드입니다.
         */
        public ?string $categoryCode = null,
        /**
         * 발신프로필 상태입니다.
         */
        public ?string $status = null,
        /**
         * 카카오톡 채널 프로필 상태입니다.
         */
        public ?string $profileStatus = null,
        /**
         * 휴면 상태 여부입니다.
         */
        public ?bool $dormant = null,
        /**
         * 차단 상태 여부입니다.
         */
        public ?bool $block = null,
        /**
         * 비즈니스 인증 채널 여부입니다.
         */
        public ?bool $businessProfile = null,
        /**
         * 비즈니스 인증 유형입니다.
         */
        public ?string $businessType = null,
        /**
         * 발신프로필 생성 일시(yyyy-MM-dd HH:mm:ss)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $createdAt = null,
        /**
         * 등록 일시(yyyy-MM-dd HH:mm:ss)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $regDate = null,
        /**
         * 수정 일시(yyyy-MM-dd HH:mm:ss)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $updateDate = null,
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
            channelName: $in->string('channelName'),
            channelId: $in->string('channelId'),
            channelKey: $in->string('channelKey'),
            categoryCode: $in->string('categoryCode'),
            status: $in->string('status'),
            profileStatus: $in->string('profileStatus'),
            dormant: $in->bool('dormant'),
            block: $in->bool('block'),
            businessProfile: $in->bool('businessProfile'),
            businessType: $in->string('businessType'),
            createdAt: $in->string('createdAt'),
            regDate: $in->string('regDate'),
            updateDate: $in->string('updateDate'),
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
            'channelName' => $this->channelName,
            'channelId' => $this->channelId,
            'channelKey' => $this->channelKey,
            'categoryCode' => $this->categoryCode,
            'status' => $this->status,
            'profileStatus' => $this->profileStatus,
            'dormant' => $this->dormant,
            'block' => $this->block,
            'businessProfile' => $this->businessProfile,
            'businessType' => $this->businessType,
            'createdAt' => $this->createdAt,
            'regDate' => $this->regDate,
            'updateDate' => $this->updateDate,
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
        return Mask::describe('KakaoSenderChannel', get_object_vars($this));
    }
}
