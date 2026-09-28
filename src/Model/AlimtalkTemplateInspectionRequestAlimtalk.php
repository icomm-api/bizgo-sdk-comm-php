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
 * 검수 요청할 알림톡 템플릿 정보입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class AlimtalkTemplateInspectionRequestAlimtalk implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['senderKey', 'senderKeyType', 'templateCode', 'comment'];

    /**
     * @param string $senderKey 발신프로필 키입니다.
     * @param string $templateCode 템플릿 코드입니다.
     * @param string|null $senderKeyType 발신프로필 키 타입입니다.
     * @param string|null $comment 검수 의견 또는 문의 사항입니다.
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
         * 템플릿 코드입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $templateCode,
        /**
         * 발신프로필 키 타입입니다. 기본값은 `S`입니다.
         * - `G`: 그룹
         * - `S`: 발신프로필
         *
         * 허용 값: `G`, `S` · 서버 기본값 `'S'` (설정하지 않으면 보내지 않음)
         */
        #[\SensitiveParameter]
        public ?string $senderKeyType = null,
        /**
         * 검수 의견 또는 문의 사항입니다.
         */
        #[\SensitiveParameter]
        public ?string $comment = null,
    ) {
        Check::all(
            Check::enum('senderKeyType', $senderKeyType, ['G', 'S']),
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
        $senderKeyType = $in->string('senderKeyType');
        $comment = $in->string('comment');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            templateCode: $templateCode,
            senderKeyType: $senderKeyType,
            comment: $comment,
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
            'senderKeyType' => $this->senderKeyType,
            'templateCode' => $this->templateCode,
            'comment' => $this->comment,
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
            'senderKeyType' => $this->senderKeyType,
            'templateCode' => $this->templateCode,
            'comment' => $this->comment,
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
        return Mask::describe('AlimtalkTemplateInspectionRequestAlimtalk', get_object_vars($this));
    }
}
