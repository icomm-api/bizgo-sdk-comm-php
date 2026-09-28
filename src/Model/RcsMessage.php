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
 * RCS 메시지입니다. 같은 `rcs` 키로 **통합 RCS**(iOS·안드로이드 동시 지원, 신규 연동 권장)와
 * **안드로이드 RCS**(기존 안드로이드 채팅+ 연동 규격 유지용)를 모두 발송합니다.
 * `copyAllowed`, `header`, `footer`는 안드로이드 RCS 발송 문서에만 있는 필드입니다.
 *
 * 확인 필요(x-unverified): 통합 RCS와 안드로이드 RCS를 요청에서 어떻게 구분하는지(formatId 등) 문서에 명시되어 있지 않습니다.
 * copyAllowed/header/footer를 통합 RCS에 보냈을 때의 동작도 문서화되어 있지 않습니다.
 *
 * `messageFlow[].rcs`로 보냅니다. `$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.
 *
 * 조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): `header` == `1`이면 `footer`.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsMessage implements ChannelMessage
{
    /** `messageFlow` channel key of this message. */
    public const CHANNEL_KEY = 'rcs';

    /** API field names, in spec order. */
    public const FIELDS = [
        'from',
        'formatId',
        'brandKey',
        'body',
        'buttons',
        'brandId',
        'groupId',
        'expiryOption',
        'copyAllowed',
        'header',
        'footer',
        'agencyId',
        'agencyKey',
        'ttl',
    ];

    /**
     * @param string $from 발신번호입니다.
     * @param string $formatId RCS 메시지 포맷(템플릿) ID입니다.
     * @param string $brandKey RCS 브랜드 식별 키입니다.
     * @param RcsBody $body
     * @param list<RcsButton>|null $buttons RCS 버튼 목록입니다.
     * @param string|null $brandId RCS 브랜드 ID입니다.
     * @param string|null $groupId RCS 메시지 그룹 ID입니다.
     * @param string|null $expiryOption 전송 타임아웃 설정입니다.
     * @param string|null $copyAllowed (안드로이드 RCS) 메시지 복사 허용 여부입니다.
     * @param string|null $header (안드로이드 RCS) 광고 표시 레이블 여부입니다.
     * @param string|null $footer (안드로이드 RCS) 수신거부 번호입니다.
     * @param string|null $agencyId 대행사 ID입니다.
     * @param string|null $agencyKey 대행사 키입니다.
     * @param string|null $ttl 메시지 유효 시간(초)입니다.
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
         * RCS 메시지 포맷(템플릿) ID입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $formatId,
        /**
         * RCS 브랜드 식별 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $brandKey,
        /**
         *
         *
         * 필수
         */
        #[\SensitiveParameter]
        public RcsBody $body,
        /**
         * RCS 버튼 목록입니다.
         */
        #[\SensitiveParameter]
        public ?array $buttons = null,
        /**
         * RCS 브랜드 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $brandId = null,
        /**
         * RCS 메시지 그룹 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $groupId = null,
        /**
         * 전송 타임아웃 설정입니다. 기본값은 `1`입니다.
         * - `1`: 24시간
         * - `2`: 40초
         * - `3`: 3분 10초
         * - `4`: 1시간
         *
         * 허용 값: `1`, `2`, `3`, `4` · 서버 기본값 `'1'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $expiryOption = null,
        /**
         * (안드로이드 RCS) 메시지 복사 허용 여부입니다. `0`=허용 안 함, `1`=허용. 기본값은 `0`입니다.
         *
         * 허용 값: `0`, `1` · 서버 기본값 `'0'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $copyAllowed = null,
        /**
         * (안드로이드 RCS) 광고 표시 레이블 여부입니다. `0`=표시 안 함, `1`=표시. 기본값은 `0`입니다.
         *
         * 허용 값: `0`, `1` · 서버 기본값 `'0'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        /**
         * (안드로이드 RCS) 수신거부 번호입니다. `header`가 `1`이면 필수이며 최대 100자입니다.
         *
         * 최대 100자
         */
        #[\SensitiveParameter]
        public ?string $footer = null,
        /**
         * 대행사 ID입니다. 기본값은 `infobank`입니다.
         *
         * 서버 기본값 `'infobank'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $agencyId = null,
        /**
         * 대행사 키입니다.
         */
        #[\SensitiveParameter]
        public ?string $agencyKey = null,
        /**
         * 메시지 유효 시간(초)입니다. 기본값은 `86400`입니다.
         *
         * 형식 `^[0-9]+$` · 서버 기본값 `'86400'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $ttl = null,
    ) {
        Check::all(
            Check::listOf('buttons', $buttons, [RcsButton::class]),
            Check::enum('expiryOption', $expiryOption, ['1', '2', '3', '4']),
            Check::enum('copyAllowed', $copyAllowed, ['0', '1']),
            Check::enum('header', $header, ['0', '1']),
            Check::maxLength('footer', $footer, 100),
            Check::pattern('ttl', $ttl, '^[0-9]+$'),
        );
        Check::all(RequiredIf::check('RcsMessage', $this->toArray()));
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
        $formatId = $in->requiredString('formatId');
        $brandKey = $in->requiredString('brandKey');
        $body = $in->model('body', RcsBody::class, true);
        $buttons = $in->modelList('buttons', RcsButton::class);
        $brandId = $in->string('brandId');
        $groupId = $in->string('groupId');
        $expiryOption = $in->string('expiryOption');
        $copyAllowed = $in->string('copyAllowed');
        $header = $in->string('header');
        $footer = $in->string('footer');
        $agencyId = $in->string('agencyId');
        $agencyKey = $in->string('agencyKey');
        $ttl = $in->string('ttl');

        return $in->create(static fn (): static => new static(
            from: $from,
            formatId: $formatId,
            brandKey: $brandKey,
            body: $body ?? throw new \LogicException('checked by RequestData::create()'),
            buttons: $buttons,
            brandId: $brandId,
            groupId: $groupId,
            expiryOption: $expiryOption,
            copyAllowed: $copyAllowed,
            header: $header,
            footer: $footer,
            agencyId: $agencyId,
            agencyKey: $agencyKey,
            ttl: $ttl,
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
            'formatId' => $this->formatId,
            'brandKey' => $this->brandKey,
            'body' => $this->body->toArray(),
            'buttons' => Serialize::models($this->buttons),
            'brandId' => $this->brandId,
            'groupId' => $this->groupId,
            'expiryOption' => $this->expiryOption,
            'copyAllowed' => $this->copyAllowed,
            'header' => $this->header,
            'footer' => $this->footer,
            'agencyId' => $this->agencyId,
            'agencyKey' => $this->agencyKey,
            'ttl' => $this->ttl,
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
            'formatId' => $this->formatId,
            'brandKey' => $this->brandKey,
            'body' => $this->body,
            'buttons' => $this->buttons,
            'brandId' => $this->brandId,
            'groupId' => $this->groupId,
            'expiryOption' => $this->expiryOption,
            'copyAllowed' => $this->copyAllowed,
            'header' => $this->header,
            'footer' => $this->footer,
            'agencyId' => $this->agencyId,
            'agencyKey' => $this->agencyKey,
            'ttl' => $this->ttl,
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
        return Mask::describe('RcsMessage', get_object_vars($this));
    }
}
