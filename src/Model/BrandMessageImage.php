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
 * 브랜드메시지 이미지 요소입니다. 최상위 `attachment.image`는 FI, FW, FM 타입에서, 캐러셀 아이템의 `attachment.image`는 FC, FA 타입에서
 * 필수입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageImage implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['imgUrl', 'imgLink'];

    /**
     * @param string $imgUrl 등록된 이미지 URL입니다.
     * @param string|null $imgLink 이미지 클릭 시 이동할 URL입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 등록된 이미지 URL입니다. 브랜드메시지 이미지 업로드 API(`/api/comm/v1/file/brandmessage/...`)로 발급받은 `imgUrl`을 씁니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $imgUrl,
        /**
         * 이미지 클릭 시 이동할 URL입니다. 생략하면 카카오 뷰어로 열립니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $imgLink = null,
    ) {
        Check::all(
            Check::maxLength('imgLink', $imgLink, 1000),
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
        $imgUrl = $in->requiredString('imgUrl');
        $imgLink = $in->string('imgLink');

        return $in->create(static fn (): static => new static(
            imgUrl: $imgUrl,
            imgLink: $imgLink,
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
            'imgUrl' => $this->imgUrl,
            'imgLink' => $this->imgLink,
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
            'imgUrl' => $this->imgUrl,
            'imgLink' => $this->imgLink,
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
        return Mask::describe('BrandMessageImage', get_object_vars($this));
    }
}
