<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 발송 결과 수신 웹훅(`{고객 Webhook URL}/cstalk/result`) 본문입니다. Plain/Rich 발송과 상담 종료 요청의 처리 결과가 전달되며,
 * `msgKey`·`ref`로 발송 건과 대조합니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselResultWebhookPayload implements ResponseModel
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
        'reportCode',
        'reportText',
        'kakaoResCreatedAt',
        'ref',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['msgKey', 'userKey', 'senderKey', 'serviceType', 'requestType', 'sendTime', 'reportTime'];

    /**
     * @param string|null $msgKey 발송 API 응답으로 받은 메시지 키입니다.
     * @param string|null $userKey 상담톡 사용자 키입니다.
     * @param string|null $senderKey 메시지를 수신한 발신프로필 키입니다.
     * @param string|null $serviceType 서비스 타입입니다.
     * @param string|null $msgType 메시지 타입입니다.
     * @param string|null $requestType 요청 타입입니다.
     * @param string|null $sendTime 비즈고가 이벤트를 수신한 시각입니다.
     * @param string|null $reportTime 비즈고가 웹훅을 전송한 시각입니다.
     * @param string|null $kakaoTime 카카오(상담톡 서버)가 전달한 시각입니다.
     * @param string|null $reportCode 발송 결과 코드입니다.
     * @param string|null $reportText 리포트 메시지입니다.
     * @param string|null $kakaoResCreatedAt 카카오 응답 생성 일시입니다(ISO 8601).
     * @param string|null $ref 발송 요청 시 전달한 참조 필드입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 발송 API 응답으로 받은 메시지 키입니다.
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
         * 메시지 타입입니다.
         */
        public ?string $msgType = null,
        /**
         * 요청 타입입니다. `write`(메시지 발송), `end`(상담 종료), `endwithbot`(상담 종료 및 봇 전환) 중 하나입니다.
         *
         * 값: `write`, `end`, `endwithbot`
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
         * 발송 결과 코드입니다. 카카오 응답 코드를 비즈고 코드로 변환한 값이며 `A000`이면 성공입니다.
         */
        public ?string $reportCode = null,
        /**
         * 리포트 메시지입니다.
         */
        public ?string $reportText = null,
        /**
         * 카카오 응답 생성 일시입니다(ISO 8601).
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ss.SSSXXX`
         *
         * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없습니다.
         */
        public ?string $kakaoResCreatedAt = null,
        /**
         * 발송 요청 시 전달한 참조 필드입니다.
         */
        public ?string $ref = null,
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
            reportCode: $in->string('reportCode'),
            reportText: $in->string('reportText'),
            kakaoResCreatedAt: $in->string('kakaoResCreatedAt'),
            ref: $in->string('ref'),
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
            'reportCode' => $this->reportCode,
            'reportText' => $this->reportText,
            'kakaoResCreatedAt' => $this->kakaoResCreatedAt,
            'ref' => $this->ref,
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
        return Mask::describe('CounselResultWebhookPayload', get_object_vars($this));
    }
}
