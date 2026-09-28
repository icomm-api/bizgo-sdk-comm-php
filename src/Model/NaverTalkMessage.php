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
 * 네이버 톡톡(네이버 스마트알림) 메시지입니다. `messageFlow[].navertalk` 객체로 보냅니다.
 * 네이버 스마트알림 규격에 맞춘 템플릿 기반 정보성 메시지입니다.
 *
 * 수신번호 규칙(`destinations[].to`): 11자리 휴대폰 번호입니다(예: `01000000000`).
 *
 * `messageFlow[].navertalk`로 보냅니다. `$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class NaverTalkMessage implements ChannelMessage
{
    /** `messageFlow` channel key of this message. */
    public const CHANNEL_KEY = 'navertalk';

    /** API field names, in spec order. */
    public const FIELDS = [
        'partnerKey',
        'templateCode',
        'productCode',
        'userName',
        'text',
        'templateParams',
        'attachments',
        'groupKey',
    ];

    /**
     * @param string $partnerKey 네이버 톡톡 파트너 키입니다(접수코드 A601).
     * @param string $templateCode 네이버 톡톡 템플릿 코드입니다.
     * @param string $productCode 템플릿의 상품 코드입니다.
     * @param string|null $userName 전화번호 소유자의 실명입니다.
     * @param string|null $text 템플릿 본문입니다.
     * @param array<string, string>|null $templateParams 템플릿 치환 변수입니다.
     * @param NaverTalkAttachments|null $attachments
     * @param string|null $groupKey 네이버 톡톡 발송 그룹입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 네이버 톡톡 파트너 키입니다(접수코드 A601).
         *
         * 필수
         *
         * 확인 필요(x-unverified): 원문 필드표는 partnerKey를 필수로 표시하지만, 리포트 코드 71005는 '파트너키나 발송그룹키(groupKey)가 반드시
         * 있어야 합니다'로 둘 중 하나만 있어도 되는 것처럼 읽힙니다. 필드표(Part A)를 따라 필수로 둡니다.
         */
        #[\SensitiveParameter]
        public string $partnerKey,
        /**
         * 네이버 톡톡 템플릿 코드입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $templateCode,
        /**
         * 템플릿의 상품 코드입니다. 등록된 템플릿의 상품 코드와 같아야 합니다(접수코드 A602, 리포트 코드 71007).
         * 원문 요청 예시 값은 `INFORMATION`이며, 전체 값 목록은 원문에 없습니다.
         *
         * 필수 · 알려진 값: `INFORMATION`
         */
        #[\SensitiveParameter]
        public string $productCode,
        /**
         * 전화번호 소유자의 실명입니다.
         */
        #[\SensitiveParameter]
        public ?string $userName = null,
        /**
         * 템플릿 본문입니다. 최대 2,048자입니다.
         *
         * 최대 2048자
         */
        #[\SensitiveParameter]
        public ?string $text = null,
        /**
         * 템플릿 치환 변수입니다. 치환할 키-값 쌍을 넣습니다. 최대 150자입니다.
         *
         * 확인 필요(x-unverified): 원문은 'max: 150 chars'만 적혀 있어 값 하나당 150자인지, 전체 합계 150자인지 불명확합니다. 값의 타입(문자열)도
         * 원문에 명시되지 않았습니다.
         */
        #[\SensitiveParameter]
        public ?array $templateParams = null,
        #[\SensitiveParameter]
        public ?NaverTalkAttachments $attachments = null,
        /**
         * 네이버 톡톡 발송 그룹입니다. 요청 최상위의 `groupKey`(메시지 인사이트 통계용)와는 다른 필드입니다.
         */
        #[\SensitiveParameter]
        public ?string $groupKey = null,
    ) {
        Check::all(
            Check::maxLength('text', $text, 2048),
            Check::stringMap('templateParams', $templateParams),
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
        $partnerKey = $in->requiredString('partnerKey');
        $templateCode = $in->requiredString('templateCode');
        $productCode = $in->requiredString('productCode');
        $userName = $in->string('userName');
        $text = $in->string('text');
        $templateParams = $in->stringMap('templateParams');
        $attachments = $in->model('attachments', NaverTalkAttachments::class);
        $groupKey = $in->string('groupKey');

        return $in->create(static fn (): static => new static(
            partnerKey: $partnerKey,
            templateCode: $templateCode,
            productCode: $productCode,
            userName: $userName,
            text: $text,
            templateParams: $templateParams,
            attachments: $attachments,
            groupKey: $groupKey,
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
            'partnerKey' => $this->partnerKey,
            'templateCode' => $this->templateCode,
            'productCode' => $this->productCode,
            'userName' => $this->userName,
            'text' => $this->text,
            'templateParams' => $this->templateParams,
            'attachments' => $this->attachments?->toArray(),
            'groupKey' => $this->groupKey,
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
            'partnerKey' => $this->partnerKey,
            'templateCode' => $this->templateCode,
            'productCode' => $this->productCode,
            'userName' => $this->userName,
            'text' => $this->text,
            'templateParams' => Serialize::jsonObject($this->templateParams),
            'attachments' => $this->attachments,
            'groupKey' => $this->groupKey,
        ]);
    }

    public function channelKey(): string
    {
        return self::CHANNEL_KEY;
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
        return Mask::describe('NaverTalkMessage', get_object_vars($this));
    }
}
