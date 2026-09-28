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
 * SMS(단문) 메시지입니다. 제목 필드는 없습니다.
 *
 * `messageFlow[].sms`로 보냅니다. `$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class SmsMessage implements ChannelMessage
{
    /** `messageFlow` channel key of this message. */
    public const CHANNEL_KEY = 'sms';

    /** API field names, in spec order. */
    public const FIELDS = ['from', 'text', 'ttl', 'originCID'];

    /**
     * @param string $from 발신번호입니다.
     * @param string $text SMS 본문입니다.
     * @param string|null $ttl 이통사 전달까지 유효한 최대 시간(초)입니다.
     * @param string|null $originCID 최초 발신사업자 식별코드(9자리)입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신번호입니다. 비즈고에 미리 등록한 번호여야 합니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $from,
        /**
         * SMS 본문입니다. 최대 90byte이며, 이통사 규격상 EUC-KR 범위 밖 문자는 접수 오류가 날 수 있습니다.
         *
         * 필수 · 최대 90byte(EUC-KR)
         */
        #[\SensitiveParameter]
        public string $text,
        /**
         * 이통사 전달까지 유효한 최대 시간(초)입니다. 시간이 지나면 실패 처리됩니다.
         *
         * 형식 `^[0-9]+$`
         */
        #[\SensitiveParameter]
        public ?string $ttl = null,
        /**
         * 최초 발신사업자 식별코드(9자리)입니다. 일반 고객은 입력하지 않습니다.
         */
        #[\SensitiveParameter]
        public ?string $originCID = null,
    ) {
        Check::all(
            Check::maxBytes('text', $text, 90, 'EUC-KR'),
            Check::pattern('ttl', $ttl, '^[0-9]+$'),
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
        $from = $in->requiredString('from');
        $text = $in->requiredString('text');
        $ttl = $in->string('ttl');
        $originCID = $in->string('originCID');

        return $in->create(static fn (): static => new static(
            from: $from,
            text: $text,
            ttl: $ttl,
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
            'from' => $this->from,
            'text' => $this->text,
            'ttl' => $this->ttl,
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
            'from' => $this->from,
            'text' => $this->text,
            'ttl' => $this->ttl,
            'originCID' => $this->originCID,
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
        return Mask::describe('SmsMessage', get_object_vars($this));
    }
}
