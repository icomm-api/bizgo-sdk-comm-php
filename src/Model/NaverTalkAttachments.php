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
 * 네이버 톡톡 첨부 정보 객체입니다(이미지, 버튼, 쿠폰).
 * 쿠폰(`gift`)을 첨부하면 이미지는 첨부되지 않습니다(리포트 코드 72105).
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class NaverTalkAttachments implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['imageUrl', 'imageHashId', 'buttons', 'gift'];

    /**
     * @param string|null $imageUrl 첨부 이미지 URL입니다.
     * @param string|null $imageHashId 첨부 이미지 hashId입니다.
     * @param list<NaverTalkButton>|null $buttons 버튼 목록입니다.
     * @param NaverTalkGift|null $gift
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 첨부 이미지 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $imageUrl = null,
        /**
         * 첨부 이미지 hashId입니다. 이미지 hashId에 해당하는 파트너 키나 템플릿 그룹키를 써야 합니다(리포트 코드 72011, 72104).
         */
        #[\SensitiveParameter]
        public ?string $imageHashId = null,
        /**
         * 버튼 목록입니다. 템플릿에 필요한 버튼 개수와 같아야 합니다(리포트 코드 72004).
         */
        #[\SensitiveParameter]
        public ?array $buttons = null,
        #[\SensitiveParameter]
        public ?NaverTalkGift $gift = null,
    ) {
        Check::all(
            Check::listOf('buttons', $buttons, [NaverTalkButton::class]),
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
        $imageUrl = $in->string('imageUrl');
        $imageHashId = $in->string('imageHashId');
        $buttons = $in->modelList('buttons', NaverTalkButton::class);
        $gift = $in->model('gift', NaverTalkGift::class);

        return $in->create(static fn (): static => new static(
            imageUrl: $imageUrl,
            imageHashId: $imageHashId,
            buttons: $buttons,
            gift: $gift,
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
            'imageUrl' => $this->imageUrl,
            'imageHashId' => $this->imageHashId,
            'buttons' => Serialize::models($this->buttons),
            'gift' => $this->gift?->toArray(),
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
            'imageUrl' => $this->imageUrl,
            'imageHashId' => $this->imageHashId,
            'buttons' => $this->buttons,
            'gift' => $this->gift,
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
        return Mask::describe('NaverTalkAttachments', get_object_vars($this));
    }
}
