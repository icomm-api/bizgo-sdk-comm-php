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
 * 카카오 알림톡 메시지입니다. 카카오가 승인한 템플릿을 기반으로 정보성 메시지를 발송합니다.
 *
 * 발송 방식은 2가지입니다.
 * - **전문 발송**: `msgType`(AT/AI), `text` 등 템플릿 전문을 본문에 직접 넣습니다. 이때 `msgType`과 `text`는 필수입니다.
 * - **템플릿 자동 치환 발송**: `sendType`을 `template`으로 두고 `senderKey`, `templateCode`만 보냅니다.
 *   Bizgo API가 템플릿 코드에 맞는 전문을 만든 뒤 `destinations[].replaceWords` 값을 치환합니다.
 *   이 방식에서는 `destinations[].replaceWords`가 필수이며, `msgType`·`text`·`attachment` 등 전문 필드는 문서에 없습니다.
 *
 * 두 방식의 조건부 필수 필드는 `x-sdk-required-if`에 있습니다. SDK는 보내기 전에 이 규칙을 검사합니다.
 *
 * `messageFlow[].alimtalk`로 보냅니다. `$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.
 *
 * 조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): `sendType` != `template`이면
 * `msgType`, `text`; `sendType` == `template`이면 `$.destinations[].replaceWords`.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkMessage implements ChannelMessage
{
    /** `messageFlow` channel key of this message. */
    public const CHANNEL_KEY = 'alimtalk';

    /** API field names, in spec order. */
    public const FIELDS = [
        'msgType',
        'senderKey',
        'templateCode',
        'sendType',
        'responseMethod',
        'timeout',
        'text',
        'title',
        'header',
        'link',
        'attachment',
        'supplement',
        'price',
        'currencyType',
    ];

    /**
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다.
     * @param string $templateCode 알림톡 템플릿 코드입니다.
     * @param string|null $msgType 카카오 비즈메시지 타입입니다.
     * @param string|null $sendType 템플릿 변수 자동 치환 발송 타입입니다.
     * @param string|null $responseMethod 카카오 응답 방식입니다.
     * @param string|null $timeout polling 발송 시 수신 결과를 기다리는 시간(초)입니다.
     * @param string|null $text 알림톡 본문입니다.
     * @param string|null $title 알림톡 제목입니다(강조표기형 템플릿).
     * @param string|null $header 메시지 상단에 표기할 제목입니다.
     * @param AlimtalkLink|null $link
     * @param AlimtalkAttachment|null $attachment
     * @param AlimtalkSupplement|null $supplement
     * @param string|null $price 메시지에 포함된 가격/금액/결제금액입니다.
     * @param string|null $currencyType 가격/금액/결제금액의 통화 단위입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 카카오 비즈메시지 발신프로필 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 알림톡 템플릿 코드입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $templateCode,
        /**
         * 카카오 비즈메시지 타입입니다. 전문 발송(`sendType`이 `template`이 아님) 시 필수이며, 빠지면 서버가 A523으로 거절합니다(템플릿 자동 치환 발송에서는
         * 생략).
         * - `AT`: 텍스트형
         * - `AI`: 이미지형
         *
         * 허용 값: `AT`, `AI`
         *
         * 확인 필요(x-unverified): 이미지형(AI) 발송 시 이미지를 지정하는 발송 필드가 문서에 없습니다. 템플릿에 등록된 이미지를 쓰는 것으로 추정됩니다.
         */
        #[\SensitiveParameter]
        public ?string $msgType = null,
        /**
         * 템플릿 변수 자동 치환 발송 타입입니다. 템플릿 자동 치환 발송 시 필수이며 `template`을 입력합니다. 전문 발송에서는 생략합니다.
         *
         * 허용 값: `template`
         */
        #[\SensitiveParameter]
        public ?string $sendType = null,
        /**
         * 카카오 응답 방식입니다. 기본값은 `push`입니다.
         * - `push`(권장): 활성 사용자 조건에 해당하면 ACK 수신 여부를 확인하지 않고 성공 처리합니다. 조건을 만족하지 않으면
         *   `NoSendAvailableException` 오류를 응답합니다.
         * - `polling`: `timeout` 안에 수신 결과가 도착하면 성공(`MS03`), 나머지는 성공불확실(`ME09`)로 처리합니다.
         *
         * 허용 값: `push`, `polling` · 서버 기본값 `'push'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $responseMethod = null,
        /**
         * polling 발송 시 수신 결과를 기다리는 시간(초)입니다. 10~86,400초이며 기본값은 180초입니다.
         *
         * 형식 `^[0-9]+$` · 서버 기본값 `'180'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $timeout = null,
        /**
         * 알림톡 본문입니다. 전문 발송(`sendType`이 `template`이 아님) 시 필수입니다. 최대 1,300자입니다.
         *
         * 최대 1300자
         */
        #[\SensitiveParameter]
        public ?string $text = null,
        /**
         * 알림톡 제목입니다(강조표기형 템플릿). 최대 50자입니다.
         *
         * 최대 50자
         */
        #[\SensitiveParameter]
        public ?string $title = null,
        /**
         * 메시지 상단에 표기할 제목입니다.
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        #[\SensitiveParameter]
        public ?AlimtalkLink $link = null,
        #[\SensitiveParameter]
        public ?AlimtalkAttachment $attachment = null,
        #[\SensitiveParameter]
        public ?AlimtalkSupplement $supplement = null,
        /**
         * 메시지에 포함된 가격/금액/결제금액입니다.
         */
        #[\SensitiveParameter]
        public ?string $price = null,
        /**
         * 가격/금액/결제금액의 통화 단위입니다. 알려진 값은 `KRW`, `USD`, `EUR`입니다.
         *
         * 알려진 값: `KRW`, `USD`, `EUR`
         */
        #[\SensitiveParameter]
        public ?string $currencyType = null,
    ) {
        Check::all(
            Check::enum('msgType', $msgType, ['AT', 'AI']),
            Check::enum('sendType', $sendType, ['template']),
            Check::enum('responseMethod', $responseMethod, ['push', 'polling']),
            Check::pattern('timeout', $timeout, '^[0-9]+$'),
            Check::maxLength('text', $text, 1300),
            Check::maxLength('title', $title, 50),
        );
        Check::all(RequiredIf::check('AlimtalkMessage', $this->toArray()));
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
        $msgType = $in->string('msgType');
        $sendType = $in->string('sendType');
        $responseMethod = $in->string('responseMethod');
        $timeout = $in->string('timeout');
        $text = $in->string('text');
        $title = $in->string('title');
        $header = $in->string('header');
        $link = $in->model('link', AlimtalkLink::class);
        $attachment = $in->model('attachment', AlimtalkAttachment::class);
        $supplement = $in->model('supplement', AlimtalkSupplement::class);
        $price = $in->string('price');
        $currencyType = $in->string('currencyType');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            templateCode: $templateCode,
            msgType: $msgType,
            sendType: $sendType,
            responseMethod: $responseMethod,
            timeout: $timeout,
            text: $text,
            title: $title,
            header: $header,
            link: $link,
            attachment: $attachment,
            supplement: $supplement,
            price: $price,
            currencyType: $currencyType,
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
            'msgType' => $this->msgType,
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'sendType' => $this->sendType,
            'responseMethod' => $this->responseMethod,
            'timeout' => $this->timeout,
            'text' => $this->text,
            'title' => $this->title,
            'header' => $this->header,
            'link' => $this->link?->toArray(),
            'attachment' => $this->attachment?->toArray(),
            'supplement' => $this->supplement?->toArray(),
            'price' => $this->price,
            'currencyType' => $this->currencyType,
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
            'msgType' => $this->msgType,
            'senderKey' => $this->senderKey,
            'templateCode' => $this->templateCode,
            'sendType' => $this->sendType,
            'responseMethod' => $this->responseMethod,
            'timeout' => $this->timeout,
            'text' => $this->text,
            'title' => $this->title,
            'header' => $this->header,
            'link' => $this->link,
            'attachment' => $this->attachment,
            'supplement' => $this->supplement,
            'price' => $this->price,
            'currencyType' => $this->currencyType,
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
        return Mask::describe('AlimtalkMessage', get_object_vars($this));
    }
}
