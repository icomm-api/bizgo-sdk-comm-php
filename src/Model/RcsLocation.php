<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 지도 위치입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsLocation implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['latitude', 'longitude', 'label', 'query'];

    /**
     * @param int|float $latitude 위도입니다.
     * @param int|float $longitude 경도입니다.
     * @param string $query 검색 내용입니다.
     * @param int|float|null $label 라벨입니다.
     */
    public function __construct(
        /**
         * 위도입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public int|float $latitude,
        /**
         * 경도입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public int|float $longitude,
        /**
         * 검색 내용입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $query,
        /**
         * 라벨입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Number이지만 라벨은 문자열일 가능성이 있습니다.
         */
        #[\SensitiveParameter]
        public int|float|null $label = null,
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
        $latitude = $in->requiredNumber('latitude');
        $longitude = $in->requiredNumber('longitude');
        $query = $in->requiredString('query');
        $label = $in->number('label');

        return $in->create(static fn (): static => new static(
            latitude: $latitude,
            longitude: $longitude,
            query: $query,
            label: $label,
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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'label' => $this->label,
            'query' => $this->query,
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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'label' => $this->label,
            'query' => $this->query,
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
        return Mask::describe('RcsLocation', get_object_vars($this));
    }
}
