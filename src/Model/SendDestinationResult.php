<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 수신자별 접수 결과입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class SendDestinationResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['to', 'msgKey', 'code', 'result'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $to 수신번호입니다.
     * @param string|null $msgKey 메시지 키입니다.
     * @param string|null $code 수신자별 접수 코드입니다.
     * @param string|null $result 수신자별 접수 결과입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 수신번호입니다.
         */
        public ?string $to = null,
        /**
         * 메시지 키입니다. 리포트·상태 조회에 씁니다. 끝 3자리를 뺀 값이 동보 요청의 `requestId`입니다.
         */
        public ?string $msgKey = null,
        /**
         * 수신자별 접수 코드입니다. `A000`이 아니면 해당 수신자는 접수되지 않은 것입니다.
         */
        public ?string $code = null,
        /**
         * 수신자별 접수 결과입니다.
         */
        public ?string $result = null,
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
            to: $in->string('to'),
            msgKey: $in->string('msgKey'),
            code: $in->string('code'),
            result: $in->string('result'),
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
            'to' => $this->to,
            'msgKey' => $this->msgKey,
            'code' => $this->code,
            'result' => $this->result,
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
        return Mask::describe('SendDestinationResult', get_object_vars($this));
    }
}
