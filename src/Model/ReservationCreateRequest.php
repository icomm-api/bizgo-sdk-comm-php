<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\RequiredIf;
use Bizgo\Internal\Serialize;

/**
 * 예약 발송 등록 요청입니다. 통합 발송 요청과 같은 `destinations`·`messageFlow` 구조에 예약 시각(`resvSendTime`)과
 * 예약명(`resvName`)을 더합니다.
 * 예약 발송 문서에는 `groupKey`, `idempotencyKey`, `idempotencyTtl`이 없으므로 넣지 않았습니다.
 *
 * 확인 필요(x-unverified): 예약 발송 요청이 idempotencyKey·idempotencyTtl·groupKey를 받는지 문서에 없습니다. 멱등성 키가 없으면 네트워크
 * 오류 뒤 재시도할 때 예약이 중복 등록될 수 있습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class ReservationCreateRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['destinations', 'messageFlow', 'resvSendTime', 'resvName', 'paymentCode', 'ref'];

    /**
     * @param list<Destination> $destinations 수신자 목록입니다.
     * @param list<MessageFlowItem|ChannelMessage> $messageFlow 예약 발송할 메시지 목록입니다.
     * @param string $resvSendTime 예약 발송 시각입니다.
     * @param string|null $resvName 예약 건을 식별하기 위한 이름입니다.
     * @param string|null $paymentCode 정산용 부서 코드입니다.
     * @param string|null $ref 요청 단위 참조 필드입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 수신자 목록입니다. 한 요청에 최대 200건까지 동보발송할 수 있습니다. 국제메시지는 국가번호를 포함한 E.164 형식(+ 제외)을 씁니다.
         *
         * 필수 · 항목 1~200개
         */
        #[\SensitiveParameter]
        public array $destinations,
        /**
         * 예약 발송할 메시지 목록입니다. 순서대로 대체발송(Fallback)됩니다.
         *
         * 필수 · 항목 최소 1개 · 채널 키: `sms`, `mms`, `international`, `rcs`, `alimtalk`, `brandmessage`
         */
        #[\SensitiveParameter]
        public array $messageFlow,
        /**
         * 예약 발송 시각입니다. `yyyy-MM-dd HH:mm:ss` 또는 `yyyy-MM-dd'T'HH:mm:ss` 형식을 씁니다.
         * 현재 시각 + 10분부터 1년 이내로 지정할 수 있습니다(너무 이르면 A316, 1년 초과 시 A331).
         * 광고성 메시지는 야간 차단 시간대(20:00~08:00 KST)에 예약할 수 없습니다(A330).
         *
         * 필수 · 시각 형식 `yyyy-MM-dd HH:mm:ss`
         *
         * 확인 필요(x-unverified): 시각의 기준 시간대가 명시되지 않았습니다(야간 차단 규칙만 KST로 표기). 형식·범위 제약은 영문 문서(Part B)에만 있습니다.
         */
        #[\SensitiveParameter]
        public string $resvSendTime,
        /**
         * 예약 건을 식별하기 위한 이름입니다.
         *
         * 최대 100자
         */
        #[\SensitiveParameter]
        public ?string $resvName = null,
        /**
         * 정산용 부서 코드입니다.
         *
         * 최대 20자
         *
         * 확인 필요(x-unverified): 한국어 문서(Part A)는 SMS/MMS·알림톡 예약 발송에만 paymentCode를 표기하고 국제·RCS·브랜드메시지 예약 발송에는
         * 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $paymentCode = null,
        /**
         * 요청 단위 참조 필드입니다. 리포트에 그대로 담겨 요청·메시지를 구분하는 데 씁니다.
         *
         * 최대 200자
         */
        #[\SensitiveParameter]
        public ?string $ref = null,
    ) {
        Check::all(
            Check::items('destinations', $destinations, 1, 200),
            Check::listOf('destinations', $destinations, [Destination::class]),
            Check::items('messageFlow', $messageFlow, 1, null),
            Check::listOf('messageFlow', $messageFlow, [MessageFlowItem::class, ChannelMessage::class]),
            Check::channels('messageFlow', $messageFlow, ['sms', 'mms', 'international', 'rcs', 'alimtalk', 'brandmessage']),
            Check::maxLength('resvName', $resvName, 100),
            Check::maxLength('paymentCode', $paymentCode, 20),
            Check::maxLength('ref', $ref, 200),
        );
        Check::all(RequiredIf::check('ReservationCreateRequest', $this->toArray()));
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
        $destinations = $in->modelList('destinations', Destination::class, true);
        $messageFlow = $in->modelList('messageFlow', MessageFlowItem::class, true);
        $resvSendTime = $in->requiredString('resvSendTime');
        $resvName = $in->string('resvName');
        $paymentCode = $in->string('paymentCode');
        $ref = $in->string('ref');

        return $in->create(static fn (): static => new static(
            destinations: $destinations ?? throw new \LogicException('checked by RequestData::create()'),
            messageFlow: $messageFlow ?? throw new \LogicException('checked by RequestData::create()'),
            resvSendTime: $resvSendTime,
            resvName: $resvName,
            paymentCode: $paymentCode,
            ref: $ref,
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
            'destinations' => Serialize::models($this->destinations),
            'messageFlow' => MessageFlowItem::listToArray($this->messageFlow),
            'resvSendTime' => $this->resvSendTime,
            'resvName' => $this->resvName,
            'paymentCode' => $this->paymentCode,
            'ref' => $this->ref,
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
            'destinations' => $this->destinations,
            'messageFlow' => MessageFlowItem::wrapAll($this->messageFlow),
            'resvSendTime' => $this->resvSendTime,
            'resvName' => $this->resvName,
            'paymentCode' => $this->paymentCode,
            'ref' => $this->ref,
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
        return Mask::describe('ReservationCreateRequest', get_object_vars($this));
    }
}
