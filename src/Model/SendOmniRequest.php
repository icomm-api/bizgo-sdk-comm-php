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
 * 통합 발송 요청입니다. 모든 채널이 같은 형식을 쓰며, 채널은 `messageFlow` 안의 채널 키로 구분합니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class SendOmniRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'destinations',
        'messageFlow',
        'paymentCode',
        'groupKey',
        'idempotencyKey',
        'idempotencyTtl',
        'ref',
    ];

    /**
     * @param list<Destination> $destinations 수신자 목록입니다.
     * @param list<MessageFlowItem|ChannelMessage> $messageFlow 발송할 메시지 목록입니다.
     * @param string|null $paymentCode 정산용 부서 코드입니다.
     * @param string|null $groupKey 메시지 인사이트에서 통계를 그룹으로 묶어 보기 위한 키입니다.
     * @param string|null $idempotencyKey 중복 요청을 막는 멱등성 키입니다.
     * @param int|null $idempotencyTtl 멱등성 키 유효시간(초)입니다.
     * @param string|null $ref 요청 단위 참조 필드입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 수신자 목록입니다. 한 요청에 최대 200건까지 동보발송할 수 있습니다(초과 시 A318).
         *
         * 필수 · 항목 1~200개
         */
        #[\SensitiveParameter]
        public array $destinations,
        /**
         * 발송할 메시지 목록입니다. 순서대로 대체발송(Fallback)됩니다.
         *
         * 필수 · 항목 최소 1개
         */
        #[\SensitiveParameter]
        public array $messageFlow,
        /**
         * 정산용 부서 코드입니다.
         */
        #[\SensitiveParameter]
        public ?string $paymentCode = null,
        /**
         * 메시지 인사이트에서 통계를 그룹으로 묶어 보기 위한 키입니다.
         */
        #[\SensitiveParameter]
        public ?string $groupKey = null,
        /**
         * 중복 요청을 막는 멱등성 키입니다. 같은 키로 유효시간 안에 다시 요청하면 요청은 성공(HTTP 200)으로 오고 수신자별 결과(`destinations[].code`)가
         * A301이 됩니다(2026-09-28 sandbox 확인). SDK는 이 수신자를 실패가 아닌 `duplicates`로 돌려줍니다. 이 값을 보내면
         * `idempotencyTtl`도 함께 보내야 합니다.
         *
         * 최대 200자
         */
        #[\SensitiveParameter]
        public ?string $idempotencyKey = null,
        /**
         * 멱등성 키 유효시간(초)입니다. 공개 문서는 선택 항목으로 표기하지만 `idempotencyKey`를 보내면 **반드시 함께 보내야 합니다**(없으면 A309).
         * `idempotencyKey` 없이 이 값만 보내면 SDK는 받은 값을 그대로 보냅니다.
         * 공식 SDK는 `idempotencyKey`만 지정하고 이 값을 비워 두면 86400(24시간)을 채워 보냅니다. 직접 지정한 값(0 포함)은 그대로 보냅니다.
         *
         * 범위 0~86400
         */
        #[\SensitiveParameter]
        public ?int $idempotencyTtl = null,
        /**
         * 요청 단위 참조 필드입니다. 리포트에 그대로 담겨 요청·메시지를 구분하는 데 씁니다.
         */
        #[\SensitiveParameter]
        public ?string $ref = null,
    ) {
        Check::all(
            Check::items('destinations', $destinations, 1, 200),
            Check::listOf('destinations', $destinations, [Destination::class]),
            Check::items('messageFlow', $messageFlow, 1, null),
            Check::listOf('messageFlow', $messageFlow, [MessageFlowItem::class, ChannelMessage::class]),
            Check::maxLength('idempotencyKey', $idempotencyKey, 200),
            Check::range('idempotencyTtl', $idempotencyTtl, 0, 86400),
        );
        Check::all(RequiredIf::check('SendOmniRequest', $this->toArray()));
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
        $paymentCode = $in->string('paymentCode');
        $groupKey = $in->string('groupKey');
        $idempotencyKey = $in->string('idempotencyKey');
        $idempotencyTtl = $in->int('idempotencyTtl');
        $ref = $in->string('ref');

        return $in->create(static fn (): static => new static(
            destinations: $destinations ?? throw new \LogicException('checked by RequestData::create()'),
            messageFlow: $messageFlow ?? throw new \LogicException('checked by RequestData::create()'),
            paymentCode: $paymentCode,
            groupKey: $groupKey,
            idempotencyKey: $idempotencyKey,
            idempotencyTtl: $idempotencyTtl,
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
            'paymentCode' => $this->paymentCode,
            'groupKey' => $this->groupKey,
            'idempotencyKey' => $this->idempotencyKey,
            'idempotencyTtl' => $this->idempotencyTtl,
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
            'paymentCode' => $this->paymentCode,
            'groupKey' => $this->groupKey,
            'idempotencyKey' => $this->idempotencyKey,
            'idempotencyTtl' => $this->idempotencyTtl,
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
        return Mask::describe('SendOmniRequest', get_object_vars($this));
    }
}
