<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 카카오 비즈메시지 발신프로필 정보입니다.
 * 발신프로필 등록(`POST /account/kakao/sender`), uuid·senderKey 조회(`GET /account/kakao/sender`),
 * 발신프로필 키로 조회(`GET /center/kakao/sender`) 응답에서 함께 씁니다. 엔드포인트마다 포함되는 필드가 조금씩 다릅니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class KakaoSenderProfile implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'uuid',
        'name',
        'status',
        'block',
        'dormant',
        'profileStatus',
        'createdAt',
        'modifiedAt',
        'categoryCode',
        'bizchat',
        'brandtalk',
        'brandMessage',
        'committalCompanyName',
        'channelKey',
        'businessProfile',
        'businessType',
        'unsubscribePhoneNumber',
        'unsubscribeAuthNumber',
        'profileSpamLevel',
        'profileMessageSpamLevel',
        'clearBlockUrl',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $uuid 카카오톡 채널 아이디(uuid)입니다.
     * @param string|null $name 카카오톡 채널 프로필명입니다.
     * @param string|null $status 발신프로필 상태값입니다.
     * @param bool|null $block 발신프로필 차단 여부입니다.
     * @param bool|null $dormant 발신프로필 휴면 여부입니다.
     * @param string|null $profileStatus 카카오톡 채널 상태입니다.
     * @param string|null $createdAt 등록일입니다.
     * @param string|null $modifiedAt 최종 수정일입니다.
     * @param string|null $categoryCode 발신프로필 카테고리 코드(11자리 숫자)입니다.
     * @param bool|null $bizchat 상담톡 사용 여부입니다.
     * @param bool|null $brandtalk 브랜드톡 사용 여부입니다.
     * @param bool|null $brandMessage 브랜드메시지 타겟팅 사용 여부입니다.
     * @param string|null $committalCompanyName 상담톡 위탁사 이름입니다.
     * @param string|null $channelKey 메시지 발송 결과 수신 채널키입니다.
     * @param bool|null $businessProfile 카카오톡 채널 비즈니스 인증 여부입니다.
     * @param string|null $businessType 비즈니스 유형입니다.
     * @param string|null $unsubscribePhoneNumber 무료수신거부 전화번호입니다.
     * @param string|null $unsubscribeAuthNumber 무료수신거부 인증번호입니다.
     * @param string|null $profileSpamLevel 카카오톡 채널 스팸 상태입니다.
     * @param string|null $profileMessageSpamLevel 카카오톡 메시지 스팸 상태입니다.
     * @param string|null $clearBlockUrl 알림톡 차단 해제 링크입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 카카오톡 채널 아이디(uuid)입니다. `@`로 시작하는 카카오톡 채널 검색용 아이디입니다.
         *
         * 확인 필요(x-unverified): account 경로 응답 예시는 `@`로 시작하는 채널 아이디, center 경로 응답 예시는 UUID 형식 문자열로 서로 다릅니다.
         */
        public ?string $uuid = null,
        /**
         * 카카오톡 채널 프로필명입니다.
         */
        public ?string $name = null,
        /**
         * 발신프로필 상태값입니다. 알려진 값은 `A`(정상)입니다.
         *
         * 알려진 값: `A`
         */
        public ?string $status = null,
        /**
         * 발신프로필 차단 여부입니다.
         */
        public ?bool $block = null,
        /**
         * 발신프로필 휴면 여부입니다. 휴면이면 휴면 해제(`POST /center/kakao/sender/recover`)로 복구합니다.
         */
        public ?bool $dormant = null,
        /**
         * 카카오톡 채널 상태입니다.
         * - `A`: activated
         * - `C`: deactivated
         * - `B`: block
         * - `E`: deleting
         *
         * 값: `A`, `C`, `B`, `E`
         *
         * 확인 필요(x-unverified): 상태값 목록은 Copy Markdown(Part B)에만 있습니다.
         */
        public ?string $profileStatus = null,
        /**
         * 등록일입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         *
         * 확인 필요(x-unverified): account 경로 응답 예시는 `yyyy-MM-dd HH:mm:ss`, center 경로 응답 예시는
         * `yyyy-MM-dd'T'HH:mm:ssXXX` 형식으로 서로 다릅니다.
         */
        public ?string $createdAt = null,
        /**
         * 최종 수정일입니다. 형식은 `createdAt`과 같습니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         *
         * 확인 필요(x-unverified): createdAt과 같은 형식 불일치가 있습니다.
         */
        public ?string $modifiedAt = null,
        /**
         * 발신프로필 카테고리 코드(11자리 숫자)입니다.
         */
        public ?string $categoryCode = null,
        /**
         * 상담톡 사용 여부입니다. 조회 응답에만 있고 등록 응답에는 없습니다.
         */
        public ?bool $bizchat = null,
        /**
         * 브랜드톡 사용 여부입니다. account 경로 응답에만 있습니다.
         */
        public ?bool $brandtalk = null,
        /**
         * 브랜드메시지 타겟팅 사용 여부입니다.
         *
         * 확인 필요(x-unverified): account 경로 문서는 타겟팅 M·N·O, center 경로 문서는 M·N 사용 여부로 설명합니다.
         */
        public ?bool $brandMessage = null,
        /**
         * 상담톡 위탁사 이름입니다. 조회 응답에만 있습니다.
         */
        public ?string $committalCompanyName = null,
        /**
         * 메시지 발송 결과 수신 채널키입니다.
         */
        public ?string $channelKey = null,
        /**
         * 카카오톡 채널 비즈니스 인증 여부입니다.
         */
        public ?bool $businessProfile = null,
        /**
         * 비즈니스 유형입니다. 알려진 값은 `BUSINESS`입니다. account 경로 응답에만 있습니다.
         *
         * 알려진 값: `BUSINESS`
         */
        public ?string $businessType = null,
        /**
         * 무료수신거부 전화번호입니다.
         */
        public ?string $unsubscribePhoneNumber = null,
        /**
         * 무료수신거부 인증번호입니다.
         */
        public ?string $unsubscribeAuthNumber = null,
        /**
         * 카카오톡 채널 스팸 상태입니다. 정상 / 경고제한(프로필 초기화, 상담·발송 가능) / 영구제한(상담·발송 불가)입니다.
         * 문서 예시 값은 `정상`입니다.
         *
         * 알려진 값: `정상`
         *
         * 확인 필요(x-unverified): 정상 외 상태의 실제 문자열 값이 문서에 없습니다(Part B 영문 설명만 있음).
         */
        public ?string $profileSpamLevel = null,
        /**
         * 카카오톡 메시지 스팸 상태입니다. 정상 / 경고제한(상담·발송 가능) / 활동제한(상담 가능, 발송 불가)입니다.
         * 문서 예시 값은 `정상`입니다.
         *
         * 알려진 값: `정상`
         *
         * 확인 필요(x-unverified): 정상 외 상태의 실제 문자열 값이 문서에 없습니다(Part B 영문 설명만 있음).
         */
        public ?string $profileMessageSpamLevel = null,
        /**
         * 알림톡 차단 해제 링크입니다.
         */
        public ?string $clearBlockUrl = null,
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
            uuid: $in->string('uuid'),
            name: $in->string('name'),
            status: $in->string('status'),
            block: $in->bool('block'),
            dormant: $in->bool('dormant'),
            profileStatus: $in->string('profileStatus'),
            createdAt: $in->string('createdAt'),
            modifiedAt: $in->string('modifiedAt'),
            categoryCode: $in->string('categoryCode'),
            bizchat: $in->bool('bizchat'),
            brandtalk: $in->bool('brandtalk'),
            brandMessage: $in->bool('brandMessage'),
            committalCompanyName: $in->string('committalCompanyName'),
            channelKey: $in->string('channelKey'),
            businessProfile: $in->bool('businessProfile'),
            businessType: $in->string('businessType'),
            unsubscribePhoneNumber: $in->string('unsubscribePhoneNumber'),
            unsubscribeAuthNumber: $in->string('unsubscribeAuthNumber'),
            profileSpamLevel: $in->string('profileSpamLevel'),
            profileMessageSpamLevel: $in->string('profileMessageSpamLevel'),
            clearBlockUrl: $in->string('clearBlockUrl'),
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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'status' => $this->status,
            'block' => $this->block,
            'dormant' => $this->dormant,
            'profileStatus' => $this->profileStatus,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt,
            'categoryCode' => $this->categoryCode,
            'bizchat' => $this->bizchat,
            'brandtalk' => $this->brandtalk,
            'brandMessage' => $this->brandMessage,
            'committalCompanyName' => $this->committalCompanyName,
            'channelKey' => $this->channelKey,
            'businessProfile' => $this->businessProfile,
            'businessType' => $this->businessType,
            'unsubscribePhoneNumber' => $this->unsubscribePhoneNumber,
            'unsubscribeAuthNumber' => $this->unsubscribeAuthNumber,
            'profileSpamLevel' => $this->profileSpamLevel,
            'profileMessageSpamLevel' => $this->profileMessageSpamLevel,
            'clearBlockUrl' => $this->clearBlockUrl,
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
        return Mask::describe('KakaoSenderProfile', get_object_vars($this));
    }
}
