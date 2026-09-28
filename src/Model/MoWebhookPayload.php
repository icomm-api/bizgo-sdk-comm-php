<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * MO(인증/투표) 수신 웹훅 본문입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class MoWebhookPayload implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'msgKey',
        'serviceType',
        'msgType',
        'to',
        'from',
        'carrier',
        'originator',
        'content',
        'occurredTime',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['msgKey', 'serviceType', 'msgType', 'to', 'from', 'carrier', 'originator', 'content', 'occurredTime'];

    /**
     * @param string|null $msgKey 메시지 키입니다.
     * @param string|null $serviceType 서비스 타입입니다.
     * @param string|null $msgType 메시지 타입입니다.
     * @param string|null $to 수신번호(MO 번호)입니다.
     * @param string|null $from MO 발신자가 입력한 번호입니다(기본은 단말기 번호).
     * @param string|null $carrier 이통사 코드입니다.
     * @param string|null $originator MO 발신 단말기 번호입니다.
     * @param string|null $content MO 메시지 본문입니다.
     * @param string|null $occurredTime MO 발생 시각(ISO 8601)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 메시지 키입니다.
         */
        public ?string $msgKey = null,
        /**
         * 서비스 타입입니다. MO로 고정입니다.
         *
         * 값: `MO`
         */
        public ?string $serviceType = null,
        /**
         * 메시지 타입입니다. SM으로 고정입니다.
         *
         * 값: `SM`
         */
        public ?string $msgType = null,
        /**
         * 수신번호(MO 번호)입니다.
         */
        public ?string $to = null,
        /**
         * MO 발신자가 입력한 번호입니다(기본은 단말기 번호).
         */
        public ?string $from = null,
        /**
         * 이통사 코드입니다.
         */
        public ?string $carrier = null,
        /**
         * MO 발신 단말기 번호입니다.
         */
        public ?string $originator = null,
        /**
         * MO 메시지 본문입니다.
         */
        public ?string $content = null,
        /**
         * MO 발생 시각(ISO 8601)입니다.
         *
         * 시각 형식 `yyyy-MM-dd'T'HH:mm:ssXXX`
         */
        public ?string $occurredTime = null,
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
            from: $in->string('from'),
            carrier: $in->string('carrier'),
            originator: $in->string('originator'),
            content: $in->string('content'),
            occurredTime: $in->string('occurredTime'),
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
            'from' => $this->from,
            'carrier' => $this->carrier,
            'originator' => $this->originator,
            'content' => $this->content,
            'occurredTime' => $this->occurredTime,
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
        return Mask::describe('MoWebhookPayload', get_object_vars($this));
    }
}
