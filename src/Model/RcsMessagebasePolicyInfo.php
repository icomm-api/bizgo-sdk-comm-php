<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 리치카드, 오픈리치카드에 포함된 content에 대한 검증 정책입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsMessagebasePolicyInfo implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'buttonsAllowed',
        'adHeaderAllowed',
        'adBodyAllowed',
        'cardCount',
        'maxButtonCount',
        'maxDescriptionSize',
        'maxMediaSize',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param bool|null $buttonsAllowed 버튼 사용 가능 여부입니다.
     * @param bool|null $adHeaderAllowed 헤더 광고 문구 사용 여부입니다.
     * @param bool|null $adBodyAllowed 본문 광고 문구 사용 여부입니다.
     * @param int|null $cardCount 카드 개수입니다.
     * @param int|null $maxButtonCount 최대 버튼 개수입니다.
     * @param int|null $maxDescriptionSize description 영역 최대 글자 수입니다.
     * @param int|null $maxMediaSize 미디어 최대 크기입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 버튼 사용 가능 여부입니다.
         */
        public ?bool $buttonsAllowed = null,
        /**
         * 헤더 광고 문구 사용 여부입니다.
         */
        public ?bool $adHeaderAllowed = null,
        /**
         * 본문 광고 문구 사용 여부입니다.
         */
        public ?bool $adBodyAllowed = null,
        /**
         * 카드 개수입니다.
         */
        public ?int $cardCount = null,
        /**
         * 최대 버튼 개수입니다.
         */
        public ?int $maxButtonCount = null,
        /**
         * description 영역 최대 글자 수입니다.
         */
        public ?int $maxDescriptionSize = null,
        /**
         * 미디어 최대 크기입니다.
         *
         * 확인 필요(x-unverified): 단위(byte 등)가 문서화되어 있지 않습니다.
         */
        public ?int $maxMediaSize = null,
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
            buttonsAllowed: $in->bool('buttonsAllowed'),
            adHeaderAllowed: $in->bool('adHeaderAllowed'),
            adBodyAllowed: $in->bool('adBodyAllowed'),
            cardCount: $in->int('cardCount'),
            maxButtonCount: $in->int('maxButtonCount'),
            maxDescriptionSize: $in->int('maxDescriptionSize'),
            maxMediaSize: $in->int('maxMediaSize'),
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
            'buttonsAllowed' => $this->buttonsAllowed,
            'adHeaderAllowed' => $this->adHeaderAllowed,
            'adBodyAllowed' => $this->adBodyAllowed,
            'cardCount' => $this->cardCount,
            'maxButtonCount' => $this->maxButtonCount,
            'maxDescriptionSize' => $this->maxDescriptionSize,
            'maxMediaSize' => $this->maxMediaSize,
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
        return Mask::describe('RcsMessagebasePolicyInfo', get_object_vars($this));
    }
}
