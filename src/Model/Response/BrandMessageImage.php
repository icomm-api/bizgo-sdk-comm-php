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
 * 브랜드메시지 이미지 요소입니다. 최상위 `attachment.image`는 FI, FW, FM 타입에서, 캐러셀 아이템의 `attachment.image`는 FC, FA 타입에서
 * 필수입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageImage implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['imgUrl', 'imgLink'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['imgUrl'];

    /**
     * @param string|null $imgUrl 등록된 이미지 URL입니다.
     * @param string|null $imgLink 이미지 클릭 시 이동할 URL입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 등록된 이미지 URL입니다. 브랜드메시지 이미지 업로드 API(`/api/comm/v1/file/brandmessage/...`)로 발급받은 `imgUrl`을 씁니다.
         */
        public ?string $imgUrl = null,
        /**
         * 이미지 클릭 시 이동할 URL입니다. 생략하면 카카오 뷰어로 열립니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        public ?string $imgLink = null,
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
            imgUrl: $in->string('imgUrl'),
            imgLink: $in->string('imgLink'),
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
            'imgUrl' => $this->imgUrl,
            'imgLink' => $this->imgLink,
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
        return Mask::describe('BrandMessageImage', get_object_vars($this));
    }
}
