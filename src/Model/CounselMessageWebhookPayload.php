<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 사용자 메시지 수신 웹훅(`{고객 Webhook URL}/cstalk/message`) 본문입니다. 사용자가 보낸 상담 내용이 담기므로 개인정보로 취급합니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselMessageWebhookPayload implements ResponseModel
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
        'content',
        'extra',
        'contents',
        'attachment',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['msgKey', 'userKey', 'senderKey', 'serviceType', 'msgType', 'sendTime', 'reportTime'];

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
     * @param string|null $content 하위 호환을 위해 유지되는 본문 필드입니다.
     * @param string|null $extraField 카카오가 전달한 부가 정보입니다.
     * @param list<CounselMessageContent>|null $contents 사용자가 전송한 메시지 데이터 배열입니다.
     * @param CounselMessageAttachment|null $attachment
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
         * 메시지 타입입니다. 문서 예시 값은 `TEXT`입니다.
         *
         * 알려진 값: `TEXT`
         *
         * 확인 필요(x-unverified): 사용자 메시지의 msgType 값 목록이 문서에 없습니다.
         */
        public ?string $msgType = null,
        /**
         * 요청 타입입니다. 문서 예시 값은 `message`입니다. 이 웹훅에서만 필수가 아닙니다.
         *
         * 알려진 값: `message`
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
         *
         * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없습니다.
         */
        public ?string $sessionId = null,
        /**
         * 하위 호환을 위해 유지되는 본문 필드입니다. 2026년 1월 이후 제공되지 않으므로 `contents`를 사용합니다.
         *
         * 사용 중단(deprecated)
         */
        public ?string $content = null,
        /**
         * 카카오가 전달한 부가 정보입니다.
         *
         * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없습니다.
         */
        public ?string $extraField = null,
        /**
         * 사용자가 전송한 메시지 데이터 배열입니다.
         */
        public ?array $contents = null,
        public ?CounselMessageAttachment $attachment = null,
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
            content: $in->string('content'),
            extraField: $in->string('extra'),
            contents: $in->modelList('contents', CounselMessageContent::class),
            attachment: $in->model('attachment', CounselMessageAttachment::class),
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
            'content' => $this->content,
            'extra' => $this->extraField,
            'contents' => Serialize::models($this->contents),
            'attachment' => $this->attachment?->toArray(),
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
        return Mask::describe('CounselMessageWebhookPayload', get_object_vars($this));
    }
}
