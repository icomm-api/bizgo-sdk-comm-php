<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 동보 발송 요청 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageGroupSend implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'requestId',
        'status',
        'sendStartAt',
        'campaignId',
        'tpsLimit',
        'pushAlarm',
        'expectedCount',
        'sendCount',
        'senderKey',
        'msgType',
        'templateCode',
        'friendGroupKey',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $requestId 동보 발송 요청 아이디입니다.
     * @param string|null $status 동보 발송 상태입니다.
     * @param string|null $sendStartAt 동보 발송 시작 일시(`yyyy-MM-dd HH:mm:ss`, KST)입니다.
     * @param string|null $campaignId 고객 캠페인 식별자입니다.
     * @param int|null $tpsLimit 초당 발송 건수 상한입니다.
     * @param string|null $pushAlarm 메시지 푸시 알림 발송 여부입니다.
     * @param int|null $expectedCount 예상 발송 수입니다.
     * @param int|null $sendCount 실제 발송 처리 수입니다.
     * @param string|null $senderKey 발신프로필 키입니다.
     * @param string|null $msgType 브랜드메시지 메시지 타입입니다.
     * @param string|null $templateCode 기본형 템플릿 코드입니다.
     * @param string|null $friendGroupKey 친구 그룹 키입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 동보 발송 요청 아이디입니다.
         */
        public ?string $requestId = null,
        /**
         * 동보 발송 상태입니다. 알려진 값: `READY`(발송 대기), `SENDING`(발송 중), `PAUSED`(발송 중지), `DONE`(발송 완료),
         * `TERMINATED`(발송 종료), `BLOCKED`(발송 차단).
         *
         * 알려진 값: `READY`, `SENDING`, `PAUSED`, `DONE`, `TERMINATED`, `BLOCKED`
         */
        public ?string $status = null,
        /**
         * 동보 발송 시작 일시(`yyyy-MM-dd HH:mm:ss`, KST)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $sendStartAt = null,
        /**
         * 고객 캠페인 식별자입니다.
         */
        public ?string $campaignId = null,
        /**
         * 초당 발송 건수 상한입니다.
         */
        public ?int $tpsLimit = null,
        /**
         * 메시지 푸시 알림 발송 여부입니다.
         *
         * 값: `Y`, `N`
         */
        public ?string $pushAlarm = null,
        /**
         * 예상 발송 수입니다.
         */
        public ?int $expectedCount = null,
        /**
         * 실제 발송 처리 수입니다.
         */
        public ?int $sendCount = null,
        /**
         * 발신프로필 키입니다.
         */
        public ?string $senderKey = null,
        /**
         * 브랜드메시지 메시지 타입입니다.
         */
        public ?string $msgType = null,
        /**
         * 기본형 템플릿 코드입니다.
         */
        public ?string $templateCode = null,
        /**
         * 친구 그룹 키입니다. 전체 친구 대상이면 없을 수 있습니다.
         */
        public ?string $friendGroupKey = null,
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
            requestId: $in->string('requestId'),
            status: $in->string('status'),
            sendStartAt: $in->string('sendStartAt'),
            campaignId: $in->string('campaignId'),
            tpsLimit: $in->int('tpsLimit'),
            pushAlarm: $in->string('pushAlarm'),
            expectedCount: $in->int('expectedCount'),
            sendCount: $in->int('sendCount'),
            senderKey: $in->string('senderKey'),
            msgType: $in->string('msgType'),
            templateCode: $in->string('templateCode'),
            friendGroupKey: $in->string('friendGroupKey'),
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
            'requestId' => $this->requestId,
            'status' => $this->status,
            'sendStartAt' => $this->sendStartAt,
            'campaignId' => $this->campaignId,
            'tpsLimit' => $this->tpsLimit,
            'pushAlarm' => $this->pushAlarm,
            'expectedCount' => $this->expectedCount,
            'sendCount' => $this->sendCount,
            'senderKey' => $this->senderKey,
            'msgType' => $this->msgType,
            'templateCode' => $this->templateCode,
            'friendGroupKey' => $this->friendGroupKey,
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
        return Mask::describe('BrandMessageGroupSend', get_object_vars($this));
    }
}
