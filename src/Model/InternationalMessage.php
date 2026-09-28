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
 * 국제메시지(해외 수신자 대상 문자)입니다. `messageFlow[].international` 객체로 보냅니다.
 * 본문은 UTF-8을 지원하며, 실제 단말 전달 여부와 무관하게 접수 기준으로 과금됩니다.
 *
 * 수신번호 규칙(`destinations[].to`): 국가번호를 포함한 E.164 형식에서 `+`를 뺀 숫자로 입력합니다
 * (예: `821000000000`, 길이 8~15자리). 국가번호 목록이나 국가별 길이·세그먼트(분할) 규칙은 원문에 없습니다.
 *
 * 국제문자에서는 `title`, `fileKey`를 쓸 수 없습니다(접수코드 A321).
 *
 * `messageFlow[].international`로 보냅니다. `$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class InternationalMessage implements ChannelMessage
{
    /** `messageFlow` channel key of this message. */
    public const CHANNEL_KEY = 'international';

    /** API field names, in spec order. */
    public const FIELDS = ['from', 'text', 'ttl', 'clientSubId'];

    /**
     * @param string $from 발신번호입니다.
     * @param string $text 국제메시지 본문입니다.
     * @param string|null $ttl 메시지 유효 시간(초)입니다.
     * @param string|null $clientSubId Sender ID, 메시지 서명을 복수로 지정하기 위한 구분자입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신번호입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $from,
        /**
         * 국제메시지 본문입니다. 최대 1,000자이며 UTF-8로 인코딩됩니다(글자 수 기준, 바이트 제한 아님).
         *
         * 필수 · 최대 1000자
         */
        #[\SensitiveParameter]
        public string $text,
        /**
         * 메시지 유효 시간(초)입니다. 생략하면 86400(24시간)입니다.
         *
         * 서버 기본값 `'86400'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $ttl = null,
        /**
         * Sender ID, 메시지 서명을 복수로 지정하기 위한 구분자입니다. 최대 20자입니다.
         *
         * 최대 20자
         */
        #[\SensitiveParameter]
        public ?string $clientSubId = null,
    ) {
        Check::all(
            Check::maxLength('text', $text, 1000),
            Check::maxLength('clientSubId', $clientSubId, 20),
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
        $clientSubId = $in->string('clientSubId');

        return $in->create(static fn (): static => new static(
            from: $from,
            text: $text,
            ttl: $ttl,
            clientSubId: $clientSubId,
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
            'clientSubId' => $this->clientSubId,
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
            'clientSubId' => $this->clientSubId,
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
        return Mask::describe('InternationalMessage', get_object_vars($this));
    }
}
