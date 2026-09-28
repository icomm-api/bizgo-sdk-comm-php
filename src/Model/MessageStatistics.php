<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 일자별 접수·리포트 통계입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class MessageStatistics implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'statDate',
        'recvTotalCnt',
        'recvSuccCnt',
        'recvFailCnt',
        'reportTotalCnt',
        'reportSuccCnt',
        'reportFailCnt',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $statDate 집계일입니다.
     * @param int|null $recvTotalCnt 접수 전체 건수입니다.
     * @param int|null $recvSuccCnt 접수 성공 건수입니다.
     * @param int|null $recvFailCnt 접수 실패 건수입니다.
     * @param int|null $reportTotalCnt 리포트 전체 건수입니다.
     * @param int|null $reportSuccCnt 리포트 성공 건수입니다.
     * @param int|null $reportFailCnt 리포트 실패 건수입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 집계일입니다.
         *
         * 형식 `^[0-9]{8}$` · 시각 형식 `YYYYMMDD`
         */
        public ?string $statDate = null,
        /**
         * 접수 전체 건수입니다.
         */
        public ?int $recvTotalCnt = null,
        /**
         * 접수 성공 건수입니다.
         */
        public ?int $recvSuccCnt = null,
        /**
         * 접수 실패 건수입니다.
         */
        public ?int $recvFailCnt = null,
        /**
         * 리포트 전체 건수입니다.
         */
        public ?int $reportTotalCnt = null,
        /**
         * 리포트 성공 건수입니다.
         */
        public ?int $reportSuccCnt = null,
        /**
         * 리포트 실패 건수입니다.
         */
        public ?int $reportFailCnt = null,
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
            statDate: $in->string('statDate'),
            recvTotalCnt: $in->int('recvTotalCnt'),
            recvSuccCnt: $in->int('recvSuccCnt'),
            recvFailCnt: $in->int('recvFailCnt'),
            reportTotalCnt: $in->int('reportTotalCnt'),
            reportSuccCnt: $in->int('reportSuccCnt'),
            reportFailCnt: $in->int('reportFailCnt'),
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
            'statDate' => $this->statDate,
            'recvTotalCnt' => $this->recvTotalCnt,
            'recvSuccCnt' => $this->recvSuccCnt,
            'recvFailCnt' => $this->recvFailCnt,
            'reportTotalCnt' => $this->reportTotalCnt,
            'reportSuccCnt' => $this->reportSuccCnt,
            'reportFailCnt' => $this->reportFailCnt,
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
        return Mask::describe('MessageStatistics', get_object_vars($this));
    }
}
