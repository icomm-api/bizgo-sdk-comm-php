<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 예약 수정 요청입니다. 예약 대기(PENDING) 상태인 예약 건의 발송 시각과 예약명만 바꿀 수 있습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class ReservationUpdateRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['resvSendTime', 'resvName'];

    /**
     * @param string $resvSendTime 변경할 예약 발송 시각입니다(yyyy-MM-dd HH:mm:ss).
     * @param string|null $resvName 예약명입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 변경할 예약 발송 시각입니다(yyyy-MM-dd HH:mm:ss).
         *
         * 필수 · 시각 형식 `yyyy-MM-dd HH:mm:ss`
         *
         * 확인 필요(x-unverified): 수정 시 허용 형식(`T` 구분 형식 포함 여부)과 범위 제약(현재+10분~1년, 광고 야간 차단)이 등록 때와 같은지 문서에
         * 없습니다.
         */
        #[\SensitiveParameter]
        public string $resvSendTime,
        /**
         * 예약명입니다.
         *
         * 최대 100자
         */
        #[\SensitiveParameter]
        public ?string $resvName = null,
    ) {
        Check::all(
            Check::maxLength('resvName', $resvName, 100),
        );
    }

    /**
     * Builds the model from an array with API field names (for example decoded JSON).
     * Unknown keys, missing required fields and wrong types are reported together, by field path.
     *
     * @param array<array-key, mixed> $data
     *
     * @throws ValidationException
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new RequestData($data, $path, self::FIELDS);
        $resvSendTime = $in->requiredString('resvSendTime');
        $resvName = $in->string('resvName');

        return $in->create(static fn (): static => new static(
            resvSendTime: $resvSendTime,
            resvName: $resvName,
        ));
    }

    /**
     * The request body with API field names. Only fields you set are included.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Serialize::compact([
            'resvSendTime' => $this->resvSendTime,
            'resvName' => $this->resvName,
        ]);
    }

    /**
     * The value for json_encode(): like toArray(), with maps as JSON objects even when empty.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return Serialize::compact([
            'resvSendTime' => $this->resvSendTime,
            'resvName' => $this->resvName,
        ]);
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
        return Mask::describe('ReservationUpdateRequest', get_object_vars($this));
    }
}
