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
 * 캐러셀 인트로 정보입니다. FC 타입에서는 사용할 수 없습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCarouselHead implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['header', 'content', 'imageUrl', 'urlMobile', 'urlPc', 'schemeAndroid', 'schemeIos'];

    /**
     * @param string|null $header 캐러셀 인트로 헤더입니다.
     * @param string|null $content 캐러셀 인트로 내용입니다.
     * @param string|null $imageUrl 캐러셀 인트로 이미지 URL입니다.
     * @param string|null $urlMobile 모바일 클릭 URL입니다.
     * @param string|null $urlPc PC 클릭 URL입니다.
     * @param string|null $schemeAndroid Android 커스텀 스킴입니다.
     * @param string|null $schemeIos iOS 커스텀 스킴입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 캐러셀 인트로 헤더입니다. 줄바꿈 불가, 최대 20자입니다.
         *
         * 최대 20자
         */
        #[\SensitiveParameter]
        public ?string $header = null,
        /**
         * 캐러셀 인트로 내용입니다. 줄바꿈 최대 2회, 최대 50자입니다.
         *
         * 최대 50자
         */
        #[\SensitiveParameter]
        public ?string $content = null,
        /**
         * 캐러셀 인트로 이미지 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $imageUrl = null,
        /**
         * 모바일 클릭 URL입니다. URL 또는 스킴 중 하나라도 입력하면 필수입니다. 최대 1,000자입니다.
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
         * Android 커스텀 스킴입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
        /**
         * iOS 커스텀 스킴입니다. 최대 1,000자입니다.
         *
         * 최대 1000자
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
    ) {
        Check::all(
            Check::maxLength('header', $header, 20),
            Check::maxLength('content', $content, 50),
            Check::maxLength('urlMobile', $urlMobile, 1000),
            Check::maxLength('urlPc', $urlPc, 1000),
            Check::maxLength('schemeAndroid', $schemeAndroid, 1000),
            Check::maxLength('schemeIos', $schemeIos, 1000),
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
        $content = $in->string('content');
        $imageUrl = $in->string('imageUrl');
        $urlMobile = $in->string('urlMobile');
        $urlPc = $in->string('urlPc');
        $schemeAndroid = $in->string('schemeAndroid');
        $schemeIos = $in->string('schemeIos');

        return $in->create(static fn (): static => new static(
            header: $header,
            content: $content,
            imageUrl: $imageUrl,
            urlMobile: $urlMobile,
            urlPc: $urlPc,
            schemeAndroid: $schemeAndroid,
            schemeIos: $schemeIos,
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
            'content' => $this->content,
            'imageUrl' => $this->imageUrl,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeAndroid' => $this->schemeAndroid,
            'schemeIos' => $this->schemeIos,
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
            'content' => $this->content,
            'imageUrl' => $this->imageUrl,
            'urlMobile' => $this->urlMobile,
            'urlPc' => $this->urlPc,
            'schemeAndroid' => $this->schemeAndroid,
            'schemeIos' => $this->schemeIos,
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
        return Mask::describe('BrandMessageCarouselHead', get_object_vars($this));
    }
}
