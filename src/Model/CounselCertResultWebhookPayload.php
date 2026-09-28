<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 본인인증 결과 수신 웹훅(`{고객 Webhook URL}/cstalk/cert_result`) 본문입니다. `KAKAO_CERT` 말풍선으로 요청한 카카오톡 본인인증(전자서명)이
 * 완료되면
 * 상담 세션 상태와 관계없이 전달됩니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselCertResultWebhookPayload implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'msgKey',
        'userKey',
        'senderKey',
        'serviceType',
        'msgType',
        'requestType',
        'sendTime',
        'reportTime',
        'kakaoTime',
        'sessionId',
        'certTxId',
        'certResult',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['msgKey', 'userKey', 'senderKey', 'serviceType', 'msgType', 'requestType', 'sendTime', 'reportTime', 'certTxId', 'certResult'];

    /**
     * @param string|null $msgKey 메시지 키입니다.
     * @param string|null $userKey 상담톡 사용자 키입니다.
     * @param string|null $senderKey 메시지를 수신한 발신프로필 키입니다.
     * @param string|null $serviceType 서비스 타입입니다.
     * @param string|null $msgType 메시지 타입입니다.
     * @param string|null $requestType 요청 타입입니다.
     * @param string|null $sendTime 비즈고가 이벤트를 수신한 시각입니다.
     * @param string|null $reportTime 비즈고가 웹훅을 전송한 시각입니다.
     * @param string|null $kakaoTime 카카오(상담톡 서버)가 전달한 시각입니다.
     * @param string|null $sessionId 상담 세션 ID입니다.
     * @param string|null $certTxId 인증 트랜잭션 ID입니다.
     * @param string|null $certResult AES256/CTR/NoPadding으로 암호화한 뒤 Base64로 인코딩한 본인인증 결과입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 메시지 키입니다. 발송 결과(`result`)에서는 발송 API 응답의 msgKey와 같고, 수신 6종에서는 이벤트마다 새로 발급된 값이라 발송 건과 대응하지 않습니다.
         * 재시도로 같은 이벤트가 다시 올 수 있으므로 msgKey 기준으로 멱등 처리합니다.
         */
        public ?string $msgKey = null,
        /**
         * 상담톡 사용자 키입니다. 카카오톡 채널별로 다르며 대소문자를 구분하고, 사용자가 탈퇴 후 재가입하면 바뀝니다.
         */
        public ?string $userKey = null,
        /**
         * 메시지를 수신한 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 서비스 타입입니다. 문서 예시 값은 `CSTALK`입니다.
         *
         * 알려진 값: `CSTALK`
         */
        public ?string $serviceType = null,
        /**
         * 메시지 타입입니다. 문서 예시 값은 `KAKAO_CERT`입니다.
         *
         * 알려진 값: `KAKAO_CERT`
         */
        public ?string $msgType = null,
        /**
         * 요청 타입입니다. `cert_result`로 전달됩니다.
         *
         * 값: `cert_result`
         */
        public ?string $requestType = null,
        /**
         * 비즈고가 이벤트를 수신한 시각입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss.SSSXXX`
         */
        public ?string $sendTime = null,
        /**
         * 비즈고가 웹훅을 전송한 시각입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss.SSSXXX`
         */
        public ?string $reportTime = null,
        /**
         * 카카오(상담톡 서버)가 전달한 시각입니다. 사용자가 메시지를 입력한 시각이 아닙니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss.SSSXXX`
         */
        public ?string $kakaoTime = null,
        /**
         * 상담 세션 ID입니다.
         */
        public ?string $sessionId = null,
        /**
         * 인증 트랜잭션 ID입니다. 인증 상태 조회 API(`/api/comm/v1/center/cstalk/cert/status`)의 `certTxId`와 같은 값입니다.
         */
        public ?string $certTxId = null,
        /**
         * AES256/CTR/NoPadding으로 암호화한 뒤 Base64로 인코딩한 본인인증 결과입니다. 복호화하면 이름·전화번호·생년월일·성별·내외국인·CI가 들어 있습니다
         * (`ci`는 취급 검증을 마친 발신프로필에만 제공). 복호화 키는 카카오가 이메일로 별도 전달하며 Base64로 인코딩되어 있습니다.
         * 카카오 정책상 저장하지 말고 사용자를 식별한 즉시 파기해야 하며, 로그·APM·에러 리포트에 남지 않도록 마스킹합니다.
         */
        public ?string $certResult = null,
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
            msgKey: $in->string('msgKey'),
            userKey: $in->string('userKey'),
            senderKey: $in->string('senderKey'),
            serviceType: $in->string('serviceType'),
            msgType: $in->string('msgType'),
            requestType: $in->string('requestType'),
            sendTime: $in->string('sendTime'),
            reportTime: $in->string('reportTime'),
            kakaoTime: $in->string('kakaoTime'),
            sessionId: $in->string('sessionId'),
            certTxId: $in->string('certTxId'),
            certResult: $in->string('certResult'),
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
            'msgKey' => $this->msgKey,
            'userKey' => $this->userKey,
            'senderKey' => $this->senderKey,
            'serviceType' => $this->serviceType,
            'msgType' => $this->msgType,
            'requestType' => $this->requestType,
            'sendTime' => $this->sendTime,
            'reportTime' => $this->reportTime,
            'kakaoTime' => $this->kakaoTime,
            'sessionId' => $this->sessionId,
            'certTxId' => $this->certTxId,
            'certResult' => $this->certResult,
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
        return Mask::describe('CounselCertResultWebhookPayload', get_object_vars($this));
    }
}
