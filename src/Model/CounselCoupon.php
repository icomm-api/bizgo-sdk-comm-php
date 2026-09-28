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
 * 메시지(또는 캐러셀 아이템) 최하단에 노출되는 쿠폰 요소입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselCoupon implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'description', 'urlPc', 'urlMobile', 'schemeIos', 'schemeAndroid'];

    /**
     * @param string $title 쿠폰 이름입니다.
     * @param string $description 쿠폰 상세 설명입니다.
     * @param string|null $urlPc PC 환경에서 쿠폰 클릭 시 이동할 URL입니다.
     * @param string|null $urlMobile 모바일 환경에서 쿠폰 클릭 시 이동할 URL입니다.
     * @param string|null $schemeIos iOS 환경에서 쿠폰 클릭 시 실행할 커스텀 스킴입니다.
     * @param string|null $schemeAndroid Android 환경에서 쿠폰 클릭 시 실행할 커스텀 스킴입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 쿠폰 이름입니다. 문서상 정해진 5가지 형식 중 하나여야 합니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): 문서가 5가지 형식 중 하나라고만 하고 형식 목록을 싣지 않았습니다.
         */
        #[\SensitiveParameter]
        public string $title,
        /**
         * 쿠폰 상세 설명입니다. WIDE, WIDE_ITEM_LIST 타입은 최대 18자, 그 외 타입은 최대 12자입니다(캐러셀 쿠폰 설명에는 PREMIUM_VIDEO도 18자로
         * 적혀 있습니다).
         *
         * 필수 · 최대 18자
         */
        #[\SensitiveParameter]
        public string $description,
        /**
         * PC 환경에서 쿠폰 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlPc = null,
        /**
         * 모바일 환경에서 쿠폰 클릭 시 이동할 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $urlMobile = null,
        /**
         * iOS 환경에서 쿠폰 클릭 시 실행할 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeIos = null,
        /**
         * Android 환경에서 쿠폰 클릭 시 실행할 커스텀 스킴입니다.
         */
        #[\SensitiveParameter]
        public ?string $schemeAndroid = null,
    ) {
        Check::all(
            Check::maxLength('description', $description, 18),
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
        $title = $in->requiredString('title');
        $description = $in->requiredString('description');
        $urlPc = $in->string('urlPc');
        $urlMobile = $in->string('urlMobile');
        $schemeIos = $in->string('schemeIos');
        $schemeAndroid = $in->string('schemeAndroid');

        return $in->create(static fn (): static => new static(
            title: $title,
            description: $description,
            urlPc: $urlPc,
            urlMobile: $urlMobile,
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
            'description' => $this->description,
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
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
            'description' => $this->description,
            'urlPc' => $this->urlPc,
            'urlMobile' => $this->urlMobile,
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
        return Mask::describe('CounselCoupon', get_object_vars($this));
    }
}
