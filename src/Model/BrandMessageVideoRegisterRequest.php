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
 * 기존 동영상 등록 요청입니다. 카카오톡 채널에 이미 올라가 있는 `PUBLIC` 또는 `PRIVATE` 상태의 동영상을 재업로드 없이 등록합니다.
 *
 * 확인 필요(x-unverified): Part A는 `vid`·`videoUrl` 중 정확히 하나만 입력해야 하며 둘 다 입력하거나 비우면 `A502`라고 하나, Part B 규칙
 * 설명은 '하나 이상, 둘 다 입력 시 videoUrl 우선'이라고 합니다. Part A를 따릅니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageVideoRegisterRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['senderKey', 'vid', 'videoUrl'];

    /**
     * @param string $senderKey 발신프로필 키입니다.
     * @param string|null $vid 등록할 동영상 식별자입니다.
     * @param string|null $videoUrl 등록할 채널 동영상 URL입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 발신프로필 키입니다. 최대 40자입니다.
         *
         * 필수 · 최대 40자
         */
        #[\SensitiveParameter]
        public string $senderKey,
        /**
         * 등록할 동영상 식별자입니다. `videoUrl`과 함께 사용할 수 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $vid = null,
        /**
         * 등록할 채널 동영상 URL입니다. 형식: `https://business.kakao.com/{채널식별자}/videos/{vid}`. `vid`와 함께 사용할 수 없습니다.
         */
        #[\SensitiveParameter]
        public ?string $videoUrl = null,
    ) {
        Check::all(
            Check::maxLength('senderKey', $senderKey, 40),
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
        $vid = $in->string('vid');
        $videoUrl = $in->string('videoUrl');

        return $in->create(static fn (): static => new static(
            senderKey: $senderKey,
            vid: $vid,
            videoUrl: $videoUrl,
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
            'vid' => $this->vid,
            'videoUrl' => $this->videoUrl,
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
            'vid' => $this->vid,
            'videoUrl' => $this->videoUrl,
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
        return Mask::describe('BrandMessageVideoRegisterRequest', get_object_vars($this));
    }
}
