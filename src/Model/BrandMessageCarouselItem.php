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
 * 캐러셀 아이템 1개입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCarouselItem implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['header', 'message', 'additionalContent', 'attachment'];

    /**
     * @param string|null $header 캐러셀 아이템 제목입니다.
     * @param string|null $message 캐러셀 아이템 본문입니다.
     * @param string|null $additionalContent 부가 정보입니다.
     * @param BrandMessageCarouselItemAttachment|null $attachment
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 캐러셀 아이템 제목입니다. FC 타입에서 필수, FA 타입에서는 사용할 수 없습니다. 줄바꿈 불가, 최대 20자입니다.
         *
         * 최대 20자
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        /**
         * 캐러셀 아이템 본문입니다. FC 타입에서 필수, FA 타입에서는 사용할 수 없습니다. 줄바꿈 최대 2회, 최대 180자입니다.
         *
         * 최대 180자
         */
        #[\SensitiveParameter]
        public ?string $message = null,
        /**
         * 부가 정보입니다. FA 타입에서는 사용할 수 없습니다. 줄바꿈 최대 1회, 최대 34자입니다.
         *
         * 최대 34자
         */
        #[\SensitiveParameter]
        public ?string $additionalContent = null,
        #[\SensitiveParameter]
        public ?BrandMessageCarouselItemAttachment $attachment = null,
    ) {
        Check::all(
            Check::maxLength('header', $header, 20),
            Check::maxLength('message', $message, 180),
            Check::maxLength('additionalContent', $additionalContent, 34),
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
        $header = $in->string('header');
        $message = $in->string('message');
        $additionalContent = $in->string('additionalContent');
        $attachment = $in->model('attachment', BrandMessageCarouselItemAttachment::class);

        return $in->create(static fn (): static => new static(
            header: $header,
            message: $message,
            additionalContent: $additionalContent,
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
            'additionalContent' => $this->additionalContent,
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
            'additionalContent' => $this->additionalContent,
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
        return Mask::describe('BrandMessageCarouselItem', get_object_vars($this));
    }
}
