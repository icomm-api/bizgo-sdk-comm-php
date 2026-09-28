<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 메시지 발송 결과 리포트 1건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class Report implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'msgKey',
        'serviceType',
        'msgType',
        'sendTime',
        'reportTime',
        'reportType',
        'reportCode',
        'reportText',
        'carrier',
        'userType',
        'resCnt',
        'ref',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $msgKey 메시지 키입니다.
     * @param string|null $serviceType 서비스 타입입니다.
     * @param string|null $msgType 메시지 타입입니다(예 SM, RS, AT, FT).
     * @param string|null $sendTime 전송 처리 일시입니다.
     * @param string|null $reportTime 리포트 수신 일시입니다.
     * @param string|null $reportType 리포트 종류입니다.
     * @param string|null $reportCode 리포트 코드입니다.
     * @param string|null $reportText 리포트 상세 내용입니다.
     * @param string|null $carrier (문자메시지) 이통사 코드입니다.
     * @param string|null $userType (카카오 브랜드메시지) 메시지 발송 처리 타입입니다.
     * @param string|null $resCnt (국제메시지) 메시지 분할 수입니다.
     * @param string|null $ref 요청 시 입력한 참조 필드입니다.
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
         * 메시지 타입입니다(예 SM, RS, AT, FT).
         */
        public ?string $msgType = null,
        /**
         * 전송 처리 일시입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $sendTime = null,
        /**
         * 리포트 수신 일시입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $reportTime = null,
        /**
         * 리포트 종류입니다.
         */
        public ?string $reportType = null,
        /**
         * 리포트 코드입니다. `10000`이면 성공입니다.
         */
        public ?string $reportCode = null,
        /**
         * 리포트 상세 내용입니다.
         */
        public ?string $reportText = null,
        /**
         * (문자메시지) 이통사 코드입니다.
         */
        public ?string $carrier = null,
        /**
         * (카카오 브랜드메시지) 메시지 발송 처리 타입입니다.
         */
        public ?string $userType = null,
        /**
         * (국제메시지) 메시지 분할 수입니다.
         */
        public ?string $resCnt = null,
        /**
         * 요청 시 입력한 참조 필드입니다.
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
            sendTime: $in->string('sendTime'),
            reportTime: $in->string('reportTime'),
            reportType: $in->string('reportType'),
            reportCode: $in->string('reportCode'),
            reportText: $in->string('reportText'),
            carrier: $in->string('carrier'),
            userType: $in->string('userType'),
            resCnt: $in->string('resCnt'),
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
            'sendTime' => $this->sendTime,
            'reportTime' => $this->reportTime,
            'reportType' => $this->reportType,
            'reportCode' => $this->reportCode,
            'reportText' => $this->reportText,
            'carrier' => $this->carrier,
            'userType' => $this->userType,
            'resCnt' => $this->resCnt,
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
        return Mask::describe('Report', get_object_vars($this));
    }
}
