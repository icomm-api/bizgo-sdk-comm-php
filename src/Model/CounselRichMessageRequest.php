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
 * 상담톡 Rich 메시지 발송 요청입니다. 상담 세션이 열려 있는 사용자에게만 보낼 수 있습니다.
 * 본문과 첨부는 최종 사용자와의 상담 내용이므로 개인정보로 취급하고 로그에 남기지 않습니다.
 *
 * 조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): `msgType` in `[IMAGE, WIDE]`이면
 * `attachment.image`; `msgType` in `[ITEM_LIST, WIDE_ITEM_LIST]`이면 `attachment.item`; `msgType` ==
 * `CAROUSEL_FEED`이면 `carousel`; `msgType` == `KAKAO_CERT`이면 `certExpiry`.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselRichMessageRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'userKey',
        'senderKey',
        'msgType',
        'message',
        'description',
        'header',
        'attachment',
        'carousel',
        'autoAnswer',
        'lock',
        'certExpiry',
        'ref',
    ];

    /**
     * @param string $userKey 상담톡 사용자 키입니다.
     * @param string $senderKey 발신프로필 키입니다.
     * @param string $msgType 메시지 풍선 타입입니다.
     * @param string|null $message 사용자에게 전달할 메시지입니다.
     * @param string|null $description 사용자에게 전달할 부가 메시지입니다.
     * @param string|null $header 헤더입니다.
     * @param CounselRichAttachment|null $attachment
     * @param CounselCarousel|null $carousel
     * @param string|null $autoAnswer 시스템 자동 응답 메시지입니다.
     * @param bool|null $lock 보안 메시지 여부입니다.
     * @param int|null $certExpiry 본인인증 유효 시간(분)입니다.
     * @param string|null $ref 참조 필드입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 상담톡 사용자 키입니다. 카카오톡 채널별로 다르며 대소문자를 구분합니다. 1~20자이며 비어 있거나 20자를 넘으면 `A507`이 반환됩니다.
         *
         * 필수 · 최대 20자 · 최소 1자
         */
        #[\SensitiveParameter]
        public string $userKey,
        /**
         * 발신프로필 키입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 메시지 풍선 타입입니다. 문서가 지원 타입으로 나열한 값은 TEXT, IMAGE, WIDE, ITEM_LIST, WIDE_ITEM_LIST, CAROUSEL_FEED,
         * PERSONAL이며,
         * 본인인증 요청은 `KAKAO_CERT`입니다. `KAKAO_CERT`는 해당 채널이 본인인증 화이트리스트에 사전 등록되어 있어야 합니다
         * (이용문의 또는 영업담당자를 통해 신청하며, CI 활용 여부 증적 자료와 채널 정보를 제출합니다).
         *
         * 필수 · 알려진 값: `TEXT`, `IMAGE`, `WIDE`, `ITEM_LIST`, `WIDE_ITEM_LIST`, `CAROUSEL_FEED`, `PERSONAL`,
         * `KAKAO_CERT`
         *
         * 확인 필요(x-unverified): KAKAO_CERT는 한국어 페이지(Part A)의 안내 박스에만 있고 지원 타입 목록에는 없습니다. PERSONAL 타입에 필요한
         * 요청 필드도 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public string $msgType,
        /**
         * 사용자에게 전달할 메시지입니다.
         *
         * 확인 필요(x-unverified): Rich 메시지 본문의 최대 길이가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $message = null,
        /**
         * 사용자에게 전달할 부가 메시지입니다.
         */
        #[\SensitiveParameter]
        public ?string $description = null,
        /**
         * 헤더입니다.
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        #[\SensitiveParameter]
        public ?CounselRichAttachment $attachment = null,
        #[\SensitiveParameter]
        public ?CounselCarousel $carousel = null,
        /**
         * 시스템 자동 응답 메시지입니다.
         */
        #[\SensitiveParameter]
        public ?string $autoAnswer = null,
        /**
         * 보안 메시지 여부입니다.
         */
        #[\SensitiveParameter]
        public ?bool $lock = null,
        /**
         * 본인인증 유효 시간(분)입니다. `msgType`이 `KAKAO_CERT`일 때 필수입니다.
         *
         * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없습니다. 허용 범위가 문서에 없습니다.
         */
        #[\SensitiveParameter]
        public ?int $certExpiry = null,
        /**
         * 참조 필드입니다. 최대 200자이며 발송 결과 웹훅(`cstalk/result`)에서 함께 반환됩니다.
         *
         * 최대 200자
         */
        #[\SensitiveParameter]
        public ?string $ref = null,
    ) {
        Check::all(
            Check::maxLength('userKey', $userKey, 20),
            Check::minLength('userKey', $userKey, 1),
            Check::maxLength('ref', $ref, 200),
        );
        Check::all(RequiredIf::check('CounselRichMessageRequest', $this->toArray()));
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
        $userKey = $in->requiredString('userKey');
        $senderKey = $in->requiredString('senderKey');
        $msgType = $in->requiredString('msgType');
        $message = $in->string('message');
        $description = $in->string('description');
        $header = $in->string('header');
        $attachment = $in->model('attachment', CounselRichAttachment::class);
        $carousel = $in->model('carousel', CounselCarousel::class);
        $autoAnswer = $in->string('autoAnswer');
        $lock = $in->bool('lock');
        $certExpiry = $in->int('certExpiry');
        $ref = $in->string('ref');

        return $in->create(static fn (): static => new static(
            userKey: $userKey,
            senderKey: $senderKey,
            msgType: $msgType,
            message: $message,
            description: $description,
            header: $header,
            attachment: $attachment,
            carousel: $carousel,
            autoAnswer: $autoAnswer,
            lock: $lock,
            certExpiry: $certExpiry,
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
            'userKey' => $this->userKey,
            'senderKey' => $this->senderKey,
            'msgType' => $this->msgType,
            'message' => $this->message,
            'description' => $this->description,
            'header' => $this->header,
            'attachment' => $this->attachment?->toArray(),
            'carousel' => $this->carousel?->toArray(),
            'autoAnswer' => $this->autoAnswer,
            'lock' => $this->lock,
            'certExpiry' => $this->certExpiry,
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
            'userKey' => $this->userKey,
            'senderKey' => $this->senderKey,
            'msgType' => $this->msgType,
            'message' => $this->message,
            'description' => $this->description,
            'header' => $this->header,
            'attachment' => $this->attachment,
            'carousel' => $this->carousel,
            'autoAnswer' => $this->autoAnswer,
            'lock' => $this->lock,
            'certExpiry' => $this->certExpiry,
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
        return Mask::describe('CounselRichMessageRequest', get_object_vars($this));
    }
}
