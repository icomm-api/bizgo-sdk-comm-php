<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 카카오(알림톡·브랜드메시지) 시간대별 반응 통계 1건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class InsightKakaoHourlyStatistics implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'hour',
        'successCount',
        'failCount',
        'readCount',
        'buttonClickCount',
        'listClickCount',
        'thumbnailClickCount',
        'etcClickCount',
        'sendRate',
        'readRate',
        'clickRate',
        'ctr',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $hour 집계 시간대(0~23)입니다.
     * @param int|null $successCount 발송 성공 건수입니다.
     * @param int|null $failCount 발송 실패 건수입니다.
     * @param int|null $readCount 읽음 건수입니다.
     * @param int|null $buttonClickCount 버튼 클릭 건수입니다.
     * @param int|null $listClickCount 목록 클릭 건수입니다.
     * @param int|null $thumbnailClickCount 썸네일 클릭 건수입니다.
     * @param int|null $etcClickCount 기타 클릭 건수입니다.
     * @param int|float|null $sendRate 발송 성공률입니다.
     * @param int|float|null $readRate 읽음률입니다.
     * @param int|float|null $clickRate 클릭률입니다.
     * @param int|float|null $ctr CTR(클릭률/읽음률)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 집계 시간대(0~23)입니다.
         *
         * 확인 필요(x-unverified): 한 자리 시간대를 0으로 채우는지(`9`/`09`) 문서에 없습니다.
         */
        public ?string $hour = null,
        /**
         * 발송 성공 건수입니다.
         */
        public ?int $successCount = null,
        /**
         * 발송 실패 건수입니다.
         */
        public ?int $failCount = null,
        /**
         * 읽음 건수입니다.
         */
        public ?int $readCount = null,
        /**
         * 버튼 클릭 건수입니다.
         */
        public ?int $buttonClickCount = null,
        /**
         * 목록 클릭 건수입니다.
         */
        public ?int $listClickCount = null,
        /**
         * 썸네일 클릭 건수입니다.
         */
        public ?int $thumbnailClickCount = null,
        /**
         * 기타 클릭 건수입니다.
         */
        public ?int $etcClickCount = null,
        /**
         * 발송 성공률입니다.
         */
        public int|float|null $sendRate = null,
        /**
         * 읽음률입니다.
         */
        public int|float|null $readRate = null,
        /**
         * 클릭률입니다.
         */
        public int|float|null $clickRate = null,
        /**
         * CTR(클릭률/읽음률)입니다.
         */
        public int|float|null $ctr = null,
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
            hour: $in->string('hour'),
            successCount: $in->int('successCount'),
            failCount: $in->int('failCount'),
            readCount: $in->int('readCount'),
            buttonClickCount: $in->int('buttonClickCount'),
            listClickCount: $in->int('listClickCount'),
            thumbnailClickCount: $in->int('thumbnailClickCount'),
            etcClickCount: $in->int('etcClickCount'),
            sendRate: $in->number('sendRate'),
            readRate: $in->number('readRate'),
            clickRate: $in->number('clickRate'),
            ctr: $in->number('ctr'),
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
            'hour' => $this->hour,
            'successCount' => $this->successCount,
            'failCount' => $this->failCount,
            'readCount' => $this->readCount,
            'buttonClickCount' => $this->buttonClickCount,
            'listClickCount' => $this->listClickCount,
            'thumbnailClickCount' => $this->thumbnailClickCount,
            'etcClickCount' => $this->etcClickCount,
            'sendRate' => $this->sendRate,
            'readRate' => $this->readRate,
            'clickRate' => $this->clickRate,
            'ctr' => $this->ctr,
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
        return Mask::describe('InsightKakaoHourlyStatistics', get_object_vars($this));
    }
}
