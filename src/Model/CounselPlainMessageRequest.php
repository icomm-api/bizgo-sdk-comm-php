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
 * 상담톡 Plain 메시지 발송 요청입니다. 상담 세션이 열려 있는 사용자에게만 보낼 수 있습니다.
 * 본문(`message`)과 첨부는 최종 사용자와의 상담 내용이므로 개인정보로 취급하고 로그에 남기지 않습니다.
 *
 * 조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): `msgType` == `FILE`이면
 * `attachment.file.fileName`, `attachment.file.fileSize`.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselPlainMessageRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['userKey', 'senderKey', 'msgType', 'message', 'attachment', 'ref'];

    /**
     * @param string $userKey 상담톡 사용자 키입니다.
     * @param string $senderKey 발신프로필 키입니다.
     * @param string $msgType 메시지 타입입니다.
     * @param string $message 사용자에게 전달할 메시지입니다.
     * @param CounselPlainAttachment|null $attachment
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
         * 메시지 타입입니다. IMAGE는 `attachment.image`, VIDEO·AUDIO·FILE은 `attachment.file`을 사용합니다.
         *
         * 필수 · 허용 값: `TEXT`, `IMAGE`, `VIDEO`, `AUDIO`, `FILE`
         */
        #[\SensitiveParameter]
        public string $msgType,
        /**
         * 사용자에게 전달할 메시지입니다. 최대 1,000자입니다.
         *
         * 필수 · 최대 1000자
         */
        #[\SensitiveParameter]
        public string $message,
        #[\SensitiveParameter]
        public ?CounselPlainAttachment $attachment = null,
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
            Check::enum('msgType', $msgType, ['TEXT', 'IMAGE', 'VIDEO', 'AUDIO', 'FILE']),
            Check::maxLength('message', $message, 1000),
            Check::maxLength('ref', $ref, 200),
        );
        Check::all(RequiredIf::check('CounselPlainMessageRequest', $this->toArray()));
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
        $message = $in->requiredString('message');
        $attachment = $in->model('attachment', CounselPlainAttachment::class);
        $ref = $in->string('ref');

        return $in->create(static fn (): static => new static(
            userKey: $userKey,
            senderKey: $senderKey,
            msgType: $msgType,
            message: $message,
            attachment: $attachment,
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
            'attachment' => $this->attachment?->toArray(),
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
            'attachment' => $this->attachment,
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
        return Mask::describe('CounselPlainMessageRequest', get_object_vars($this));
    }
}
