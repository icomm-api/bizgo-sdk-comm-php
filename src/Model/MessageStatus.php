<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 메시지 1건의 접수·발송·리포트 상태입니다. 대체발송이 일어나면 같은 msgKey로 채널별 항목이 여러 개 있습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class MessageStatus implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'msgKey',
        'serviceType',
        'msgType',
        'to',
        'fallback',
        'responseCode',
        'responseText',
        'requestTime',
        'sendTime',
        'reportTime',
        'reportType',
        'reportCode',
        'reportText',
        'userType',
        'carrier',
        'ref',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $msgKey 메시지 키입니다.
     * @param string|null $serviceType 서비스 타입입니다.
     * @param string|null $msgType 메시지 타입입니다.
     * @param string|null $to 수신번호입니다.
     * @param string|null $fallback 대체발송 여부입니다.
     * @param string|null $responseCode 접수 결과 코드입니다.
     * @param string|null $responseText 접수 결과 메시지입니다.
     * @param string|null $requestTime 요청 시각(ISO 8601)입니다.
     * @param string|null $sendTime 발송 시각(ISO 8601)입니다.
     * @param string|null $reportTime 리포트 시각(ISO 8601)입니다.
     * @param string|null $reportType 리포트 타입입니다.
     * @param string|null $reportCode 리포트 코드입니다.
     * @param string|null $reportText 리포트 결과 메시지입니다.
     * @param string|null $userType 채널 친구 여부입니다(브랜드메시지 전용).
     * @param string|null $carrier 이통사 코드입니다.
     * @param string|null $ref 요청 시 전달한 참조 필드입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 메시지 키입니다.
         */
        public ?string $msgKey = null,
        /**
         * 서비스 타입입니다.
         *
         * 알려진 값: `SMS`, `MMS`, `RCS`, `ALIMTALK`, `BRANDMESSAGE`
         */
        public ?string $serviceType = null,
        /**
         * 메시지 타입입니다.
         */
        public ?string $msgType = null,
        /**
         * 수신번호입니다.
         */
        public ?string $to = null,
        /**
         * 대체발송 여부입니다.
         *
         * 값: `Y`, `N`
         */
        public ?string $fallback = null,
        /**
         * 접수 결과 코드입니다.
         */
        public ?string $responseCode = null,
        /**
         * 접수 결과 메시지입니다.
         */
        public ?string $responseText = null,
        /**
         * 요청 시각(ISO 8601)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $requestTime = null,
        /**
         * 발송 시각(ISO 8601)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $sendTime = null,
        /**
         * 리포트 시각(ISO 8601)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $reportTime = null,
        /**
         * 리포트 타입입니다.
         */
        public ?string $reportType = null,
        /**
         * 리포트 코드입니다.
         */
        public ?string $reportCode = null,
        /**
         * 리포트 결과 메시지입니다.
         */
        public ?string $reportText = null,
        /**
         * 채널 친구 여부입니다(브랜드메시지 전용).
         */
        public ?string $userType = null,
        /**
         * 이통사 코드입니다.
         */
        public ?string $carrier = null,
        /**
         * 요청 시 전달한 참조 필드입니다.
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
            serviceType: $in->string('serviceType'),
            msgType: $in->string('msgType'),
            to: $in->string('to'),
            fallback: $in->string('fallback'),
            responseCode: $in->string('responseCode'),
            responseText: $in->string('responseText'),
            requestTime: $in->string('requestTime'),
            sendTime: $in->string('sendTime'),
            reportTime: $in->string('reportTime'),
            reportType: $in->string('reportType'),
            reportCode: $in->string('reportCode'),
            reportText: $in->string('reportText'),
            userType: $in->string('userType'),
            carrier: $in->string('carrier'),
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
            'serviceType' => $this->serviceType,
            'msgType' => $this->msgType,
            'to' => $this->to,
            'fallback' => $this->fallback,
            'responseCode' => $this->responseCode,
            'responseText' => $this->responseText,
            'requestTime' => $this->requestTime,
            'sendTime' => $this->sendTime,
            'reportTime' => $this->reportTime,
            'reportType' => $this->reportType,
            'reportCode' => $this->reportCode,
            'reportText' => $this->reportText,
            'userType' => $this->userType,
            'carrier' => $this->carrier,
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
        return Mask::describe('MessageStatus', get_object_vars($this));
    }
}
