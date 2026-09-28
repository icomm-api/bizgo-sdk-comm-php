<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 예약 발송 건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class Reservation implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'seq',
        'resvKey',
        'paymentCode',
        'resvName',
        'productType',
        'status',
        'adYn',
        'resvSendTime',
        'resvData',
        'expectedCnt',
        'sentCnt',
        'successCnt',
        'failCnt',
        'updateDate',
        'regDate',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param int|null $seq 예약 순번입니다.
     * @param string|null $resvKey 예약 발송 키입니다.
     * @param string|null $paymentCode 정산 코드입니다.
     * @param string|null $resvName 예약명입니다.
     * @param string|null $productType 상품 유형입니다.
     * @param string|null $status 예약 상태입니다.
     * @param string|null $adYn 광고 메시지 여부입니다(Y/N).
     * @param string|null $resvSendTime 예약 발송 시각입니다(yyyy-MM-dd HH:mm:ss).
     * @param string|null $resvData 예약 발송 요청 본문(JSON 문자열)입니다.
     * @param int|null $expectedCnt 예상 발송 수량입니다.
     * @param int|null $sentCnt 발송 처리 수량입니다.
     * @param int|null $successCnt 성공 수량입니다.
     * @param int|null $failCnt 실패 수량입니다.
     * @param string|null $updateDate 수정 일시입니다(yyyy-MM-dd HH:mm:ss).
     * @param string|null $regDate 등록 일시입니다(yyyy-MM-dd HH:mm:ss).
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 예약 순번입니다. 목록 조회의 `lastSeq` 기준값으로 쓸 수 있습니다.
         */
        public ?int $seq = null,
        /**
         * 예약 발송 키입니다.
         */
        public ?string $resvKey = null,
        /**
         * 정산 코드입니다.
         */
        public ?string $paymentCode = null,
        /**
         * 예약명입니다.
         */
        public ?string $resvName = null,
        /**
         * 상품 유형입니다. 알려진 값은 `MESSAGE`입니다.
         *
         * 알려진 값: `MESSAGE`
         *
         * 확인 필요(x-unverified): productType 값 목록이 문서에 없습니다(예시에 MESSAGE만 있음).
         */
        public ?string $productType = null,
        /**
         * 예약 상태입니다. 알려진 값: `PENDING`(예약 대기), `PROCESSING`(발송 처리 중), `STOPPED`(예약 중지), `CANCELLED`(취소).
         * 수정은 PENDING, 취소는 PENDING·STOPPED, 중지는 PROCESSING, 재개는 STOPPED 상태에서만 할 수 있습니다(그 밖에는 A824).
         *
         * 알려진 값: `PENDING`, `PROCESSING`, `STOPPED`, `CANCELLED`
         *
         * 확인 필요(x-unverified): 예약 상태 전체 목록(발송 완료 등)이 문서에 없습니다. A824 적용 여부는 에러코드표 설명에서 추정했습니다.
         */
        public ?string $status = null,
        /**
         * 광고 메시지 여부입니다(Y/N).
         *
         * 알려진 값: `Y`, `N`
         */
        public ?string $adYn = null,
        /**
         * 예약 발송 시각입니다(yyyy-MM-dd HH:mm:ss).
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $resvSendTime = null,
        /**
         * 예약 발송 요청 본문(JSON 문자열)입니다. 수정 응답과 목록 응답 예시에는 없습니다.
         *
         * 확인 필요(x-unverified): resvData에 담기는 범위가 불명확합니다. 문서 예시는 messageFlow 항목 하나(sms 객체)만 담고 있습니다.
         */
        public ?string $resvData = null,
        /**
         * 예상 발송 수량입니다.
         */
        public ?int $expectedCnt = null,
        /**
         * 발송 처리 수량입니다.
         */
        public ?int $sentCnt = null,
        /**
         * 성공 수량입니다.
         */
        public ?int $successCnt = null,
        /**
         * 실패 수량입니다.
         */
        public ?int $failCnt = null,
        /**
         * 수정 일시입니다(yyyy-MM-dd HH:mm:ss).
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $updateDate = null,
        /**
         * 등록 일시입니다(yyyy-MM-dd HH:mm:ss).
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $regDate = null,
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
            seq: $in->int('seq'),
            resvKey: $in->string('resvKey'),
            paymentCode: $in->string('paymentCode'),
            resvName: $in->string('resvName'),
            productType: $in->string('productType'),
            status: $in->string('status'),
            adYn: $in->string('adYn'),
            resvSendTime: $in->string('resvSendTime'),
            resvData: $in->string('resvData'),
            expectedCnt: $in->int('expectedCnt'),
            sentCnt: $in->int('sentCnt'),
            successCnt: $in->int('successCnt'),
            failCnt: $in->int('failCnt'),
            updateDate: $in->string('updateDate'),
            regDate: $in->string('regDate'),
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
            'seq' => $this->seq,
            'resvKey' => $this->resvKey,
            'paymentCode' => $this->paymentCode,
            'resvName' => $this->resvName,
            'productType' => $this->productType,
            'status' => $this->status,
            'adYn' => $this->adYn,
            'resvSendTime' => $this->resvSendTime,
            'resvData' => $this->resvData,
            'expectedCnt' => $this->expectedCnt,
            'sentCnt' => $this->sentCnt,
            'successCnt' => $this->successCnt,
            'failCnt' => $this->failCnt,
            'updateDate' => $this->updateDate,
            'regDate' => $this->regDate,
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
        return Mask::describe('Reservation', get_object_vars($this));
    }
}
