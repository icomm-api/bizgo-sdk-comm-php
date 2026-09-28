<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 캐러셀 아이템 1개입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselCarouselItem implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['header', 'message', 'attachment'];

    /**
     * @param string|null $header 캐러셀 아이템 제목입니다.
     * @param string|null $message 캐러셀 아이템 메시지입니다.
     * @param CounselCarouselItemAttachment|null $attachment
     */
    public function __construct(
        /**
         * 캐러셀 아이템 제목입니다. CAROUSEL_FEED 타입에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        /**
         * 캐러셀 아이템 메시지입니다. CAROUSEL_FEED 타입에서 사용합니다.
         */
        #[\SensitiveParameter]
        public ?string $message = null,
        #[\SensitiveParameter]
        public ?CounselCarouselItemAttachment $attachment = null,
    ) {
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
        $header = $in->string('header');
        $message = $in->string('message');
        $attachment = $in->model('attachment', CounselCarouselItemAttachment::class);

        return $in->create(static fn (): static => new static(
            header: $header,
            message: $message,
            attachment: $attachment,
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
            'header' => $this->header,
            'message' => $this->message,
            'attachment' => $this->attachment?->toArray(),
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
            'header' => $this->header,
            'message' => $this->message,
            'attachment' => $this->attachment,
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
        return Mask::describe('CounselCarouselItem', get_object_vars($this));
    }
}
