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
 * 기본형 템플릿 동보 발송 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageGroupSendCreateRequestBrandmessage implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'senderKey',
        'templateCode',
        'friendGroupKey',
        'sendStartAt',
        'campaignId',
        'tpsLimit',
        'pushAlarm',
        'originCID',
    ];

    /**
     * @param string $senderKey 발신프로필 키입니다.
     * @param string $templateCode 기본형 템플릿 코드입니다.
     * @param string $sendStartAt 동보 발송 시작 일시(`yyyy-MM-dd HH:mm:ss`, KST)입니다.
     * @param string|null $friendGroupKey 친구 그룹 등록으로 발급받은 키입니다.
     * @param string|null $campaignId 고객 캠페인 식별자입니다.
     * @param int|null $tpsLimit 초당 발송 건수 상한입니다.
     * @param string|null $pushAlarm 메시지 푸시 알림 발송 여부입니다.
     * @param string|null $originCID 최초 발신사업자 식별코드입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신프로필 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 기본형 템플릿 코드입니다. 변수가 없고 상태가 등록(`A`)인 템플릿이어야 합니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $templateCode,
        /**
         * 동보 발송 시작 일시(`yyyy-MM-dd HH:mm:ss`, KST)입니다. 요청 시점으로부터 10분 이후, 08:00~20:50 범위로 설정합니다. 20:50~익일
         * 08:00에는 자동 중지 후 익일 08:00 이후 자동 재개됩니다.
         *
         * 필수 · 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        #[\SensitiveParameter]
        public string $sendStartAt,
        /**
         * 친구 그룹 등록으로 발급받은 키입니다. 생략하면 전체 친구를 대상으로 합니다. 친구 그룹을 쓰면 그룹 상태가 완료이고 등록 유저 수(`userCount`)가 10 이상이어야
         * 합니다.
         */
        #[\SensitiveParameter]
        public ?string $friendGroupKey = null,
        /**
         * 고객 캠페인 식별자입니다. 동일 캠페인의 중복 발송을 방지하는 데 사용합니다.
         *
         * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없는 필드입니다. 중복 방지 동작(같은
         * campaignId 재요청 시 결과)도 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $campaignId = null,
        /**
         * 초당 발송 건수 상한입니다.
         *
         * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없는 필드입니다. 허용 범위가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?int $tpsLimit = null,
        /**
         * 메시지 푸시 알림 발송 여부입니다. 기본값은 `Y`입니다.
         *
         * 허용 값: `Y`, `N` · 서버 기본값 `'Y'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $pushAlarm = null,
        /**
         * 최초 발신사업자 식별코드입니다. 재판매사·특수부가통신사업자는 필수입니다. 최대 9자입니다.
         *
         * 최대 9자
         */
        #[\SensitiveParameter]
        public ?string $originCID = null,
    ) {
        Check::all(
            Check::enum('pushAlarm', $pushAlarm, ['Y', 'N']),
            Check::maxLength('originCID', $originCID, 9),
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
        $senderKey = $in->requiredString('senderKey');
        $templateCode = $in->requiredString('templateCode');
        $sendStartAt = $in->requiredString('sendStartAt');
        $friendGroupKey = $in->string('friendGroupKey');
        $campaignId = $in->string('campaignId');
        $tpsLimit = $in->int('tpsLimit');
        $pushAlarm = $in->string('pushAlarm');
        $originCID = $in->string('originCID');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            templateCode: $templateCode,
            sendStartAt: $sendStartAt,
            friendGroupKey: $friendGroupKey,
            campaignId: $campaignId,
            tpsLimit: $tpsLimit,
            pushAlarm: $pushAlarm,
            originCID: $originCID,
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
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'friendGroupKey' => $this->friendGroupKey,
            'sendStartAt' => $this->sendStartAt,
            'campaignId' => $this->campaignId,
            'tpsLimit' => $this->tpsLimit,
            'pushAlarm' => $this->pushAlarm,
            'originCID' => $this->originCID,
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
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'friendGroupKey' => $this->friendGroupKey,
            'sendStartAt' => $this->sendStartAt,
            'campaignId' => $this->campaignId,
            'tpsLimit' => $this->tpsLimit,
            'pushAlarm' => $this->pushAlarm,
            'originCID' => $this->originCID,
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
        return Mask::describe('BrandMessageGroupSendCreateRequestBrandmessage', get_object_vars($this));
    }
}
