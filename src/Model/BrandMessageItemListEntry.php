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
 * 브랜드메시지 와이드 아이템 목록의 항목 1개입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageItemListEntry implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'imgUrl', 'urlMobile', 'urlPc', 'schemeIos', 'schemeAndroid'];

    /**
     * @param string|null $title 아이템 제목입니다.
     * @param string|null $imgUrl 아이템 이미지 URL입니다.
     * @param string|null $urlMobile 모바일 클릭 URL입니다.
     * @param string|null $urlPc PC 클릭 URL입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 아이템 제목입니다. 첫 번째 아이템은 최대 25자(줄바꿈 1회), 2~4번째 아이템은 최대 30자(줄바꿈 1회)입니다.
         *
         * 최대 30자
         */
        #[\SensitiveParameter]
        public ?string $title = null,
        /**
         * 아이템 이미지 URL입니다. 첫 번째 아이템은 와이드 리스트 첫번째 이미지 업로드, 2~4번째는 와이드 리스트 이미지 업로드로 발급받은 URL을 씁니다.
         */
        #[\SensitiveParameter]
        public ?string $imgUrl = null,
        /**
         * 모바일 클릭 URL입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * PC 클릭 URL입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * iOS 커스텀 스킴입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 커스텀 스킴입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
    ) {
        Check::all(
            Check::maxLength('title', $title, 30),
            Check::maxLength('urlMobile', $urlMobile, 1000),
            Check::maxLength('urlPc', $urlPc, 1000),
            Check::maxLength('schemeIos', $schemeIos, 1000),
            Check::maxLength('schemeAndroid', $schemeAndroid, 1000),
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
        $title = $in->string('title');
        $imgUrl = $in->string('imgUrl');
        $urlMobile = $in->string('urlMobile');
        $urlPc = $in->string('urlPc');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');

        return $in->create(static fn (): static => new static(
            title: $title,
            imgUrl: $imgUrl,
            urlMobile: $urlMobile,
            urlPc: $urlPc,
            schemeIos: $schemeIos,
            schemeAndroid: $schemeAndroid,
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
            'title' => $this->title,
            'imgUrl' => $this->imgUrl,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
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
            'title' => $this->title,
            'imgUrl' => $this->imgUrl,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeIos' => $this->schemeIos,
            'schemeAndroid' => $this->schemeAndroid,
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
        return Mask::describe('BrandMessageItemListEntry', get_object_vars($this));
    }
}
