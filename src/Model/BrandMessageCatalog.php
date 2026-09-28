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
 * 브랜드메시지 카탈로그 요소입니다. `msgType`이 `FG`일 때 사용합니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageCatalog implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['list'];

    /**
     * @param list<BrandMessageCatalogItem>|null $list 카탈로그 아이템 목록입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 카탈로그 아이템 목록입니다. FG 타입에서 필수이며 최소 3개, 최대 7개입니다.
         * 아이템이 홀수 개(3·5·7)이면 첫 번째 아이템만 2:1 가로형 이미지(카탈로그 홀수형 첫번째 이미지 업로드)를, 나머지는 1:1 정사각형 이미지(카탈로그 이미지
         * 업로드)를 씁니다. 짝수 개(4·6)이면 전부 1:1입니다.
         *
         * 항목 3~7개
         */
        #[\SensitiveParameter]
        public ?array $list = null,
    ) {
        Check::all(
            Check::items('list', $list, 3, 7),
            Check::listOf('list', $list, [BrandMessageCatalogItem::class]),
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
        $list = $in->modelList('list', BrandMessageCatalogItem::class);

        return $in->create(static fn (): static => new static(
            list: $list,
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
            'list' => Serialize::models($this->list),
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
            'list' => $this->list,
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
        return Mask::describe('BrandMessageCatalog', get_object_vars($this));
    }
}
