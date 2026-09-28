<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * ITEM_LIST 타입의 아이템 하이라이트 영역입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class CounselItemHighlight implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'description', 'imgUrl'];

    /**
     * @param string $description 하이라이트 추가 설명입니다.
     * @param string|null $title 하이라이트 제목입니다.
     * @param string|null $imgUrl 하이라이트 이미지 URL입니다.
     */
    public function __construct(
        /**
         * 하이라이트 추가 설명입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $description,
        /**
         * 하이라이트 제목입니다.
         */
        #[\SensitiveParameter]
        public ?string $title = null,
        /**
         * 하이라이트 이미지 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $imgUrl = null,
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
        $description = $in->requiredString('description');
        $title = $in->string('title');
        $imgUrl = $in->string('imgUrl');

        return $in->create(static fn (): static => new static(
            description: $description,
            title: $title,
            imgUrl: $imgUrl,
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
            'imgUrl' => $this->imgUrl,
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
            'imgUrl' => $this->imgUrl,
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
        return Mask::describe('CounselItemHighlight', get_object_vars($this));
    }
}
