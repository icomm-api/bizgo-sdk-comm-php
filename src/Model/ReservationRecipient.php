<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 예약 건에 등록된 수신자입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class ReservationRecipient implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['msgKey', 'destSeq', 'to', 'destData', 'status', 'responseCode', 'responseText'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $msgKey 메시지 키입니다.
     * @param int|null $destSeq 수신자 순번입니다.
     * @param string|null $to 수신번호입니다.
     * @param string|null $destData 수신자별 치환 데이터입니다.
     * @param string|null $status 수신자 상태입니다.
     * @param string|null $responseCode 처리 결과 코드입니다.
     * @param string|null $responseText 처리 결과 메시지입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 메시지 키입니다. 수신자 삭제에 씁니다.
         */
        public ?string $msgKey = null,
        /**
         * 수신자 순번입니다.
         */
        public ?int $destSeq = null,
        /**
         * 수신번호입니다.
         */
        public ?string $to = null,
        /**
         * 수신자별 치환 데이터입니다.
         *
         * 확인 필요(x-unverified): destData 형식이 불명확합니다. 요청은 replaceWords(JSON 객체)인데 응답 예시는 `name=홍길동` 형태의
         * 문자열입니다.
         */
        public ?string $destData = null,
        /**
         * 수신자 상태입니다. 알려진 값은 `PENDING`입니다.
         *
         * 알려진 값: `PENDING`
         *
         * 확인 필요(x-unverified): 수신자 상태 값 목록이 문서에 없습니다.
         */
        public ?string $status = null,
        /**
         * 처리 결과 코드입니다.
         */
        public ?string $responseCode = null,
        /**
         * 처리 결과 메시지입니다.
         */
        public ?string $responseText = null,
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
            destSeq: $in->int('destSeq'),
            to: $in->string('to'),
            destData: $in->string('destData'),
            status: $in->string('status'),
            responseCode: $in->string('responseCode'),
            responseText: $in->string('responseText'),
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
            'destSeq' => $this->destSeq,
            'to' => $this->to,
            'destData' => $this->destData,
            'status' => $this->status,
            'responseCode' => $this->responseCode,
            'responseText' => $this->responseText,
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
        return Mask::describe('ReservationRecipient', get_object_vars($this));
    }
}
