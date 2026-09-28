<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model\Response;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;
use Bizgo\Model\ResponseModel;

/**
 * 캐러셀 아이템 1개입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageCarouselItem implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['header', 'message', 'additionalContent', 'attachment'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $header 캐러셀 아이템 제목입니다.
     * @param string|null $message 캐러셀 아이템 본문입니다.
     * @param string|null $additionalContent 부가 정보입니다.
     * @param BrandMessageCarouselItemAttachment|null $attachment
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 캐러셀 아이템 제목입니다. FC 타입에서 필수, FA 타입에서는 사용할 수 없습니다. 줄바꿈 불가, 최대 20자입니다.
         *
         * 최대 20자
         */
        public ?string $header = null,
        /**
         * 캐러셀 아이템 본문입니다. FC 타입에서 필수, FA 타입에서는 사용할 수 없습니다. 줄바꿈 최대 2회, 최대 180자입니다.
         *
         * 최대 180자
         */
        public ?string $message = null,
        /**
         * 부가 정보입니다. FA 타입에서는 사용할 수 없습니다. 줄바꿈 최대 1회, 최대 34자입니다.
         *
         * 최대 34자
         */
        public ?string $additionalContent = null,
        public ?BrandMessageCarouselItemAttachment $attachment = null,
        /** Fields the spec does not describe (new server fields), by API name. */
        public array $extra = [],
    ) {
    }

    /**
     * @param array<array-key, mixed> $data decoded JSON object
     *
     * @throws InvalidResponseException when a field has an incompatible type
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new ResponseData($data, $path, self::FIELDS);

        return new static(
            header: $in->string('header'),
            message: $in->string('message'),
            additionalContent: $in->string('additionalContent'),
            attachment: $in->model('attachment', BrandMessageCarouselItemAttachment::class),
            extra: $in->extra(),
        );
    }

    /**
     * The fields with API names, followed by the unknown fields from `$extra`. Unset fields are omitted.
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
        ]) + $this->extra;
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
