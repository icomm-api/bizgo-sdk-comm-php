<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * RCS 메시지 본문입니다. 입력할 수 있는 항목은 `formatId`로 지정한 포맷에 따라 다릅니다.
 *
 * 확인 필요(x-unverified): 포맷별로 title/description/media 중 어떤 항목이 필수인지와 길이 제한이 문서화되어 있지 않습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsBody implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['title', 'description', 'media'];

    /**
     * @param string|null $title 메시지 제목입니다.
     * @param string|null $description 메시지 본문입니다.
     * @param string|null $media 이미지입니다.
     */
    public function __construct(
        /**
         * 메시지 제목입니다.
         */
        #[\SensitiveParameter]
        public ?string $title = null,
        /**
         * 메시지 본문입니다.
         */
        #[\SensitiveParameter]
        public ?string $description = null,
        /**
         * 이미지입니다. 필드 설명은 "이미지 URL"이지만, 이미지 업로드(`POST /api/comm/v1/file/rcs`)
         * 응답의 `data.data.media` 값(미디어 키)을 넣도록 안내합니다.
         *
         * 확인 필요(x-unverified): 외부 이미지 URL을 직접 넣을 수 있는지, 업로드로 받은 미디어 키(maapfile://...)만 허용되는지 불명확합니다.
         */
        #[\SensitiveParameter]
        public ?string $media = null,
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
        $title = $in->string('title');
        $description = $in->string('description');
        $media = $in->string('media');

        return $in->create(static fn (): static => new static(
            title: $title,
            description: $description,
            media: $media,
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
            'media' => $this->media,
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
            'media' => $this->media,
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
        return Mask::describe('RcsBody', get_object_vars($this));
    }
}
