<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드메시지 동영상 정보입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageVideoInfo implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['vid', 'status', 'title', 'thumbnailUrl', 'videoUrl'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $vid 동영상 식별자입니다.
     * @param string|null $status 동영상 처리 상태입니다.
     * @param string|null $title 동영상 제목입니다.
     * @param string|null $thumbnailUrl 동영상 썸네일 URL입니다.
     * @param string|null $videoUrl 동영상 재생 URL입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 동영상 식별자입니다.
         */
        public ?string $vid = null,
        /**
         * 동영상 처리 상태입니다. 알려진 값: `REGISTERED`(업로드 등록됨), `ENCODING`(인코딩 중), `PUBLIC`(공개, 발송·템플릿 등록 가능),
         * `PRIVATE`(비공개, 템플릿 등록만 가능), `VIOLATED`(정책 위반), `ILLEGAL`(불법촬영물), `DELETED`(삭제됨), `ERROR`(업로드·인코딩
         * 오류).
         *
         * 알려진 값: `REGISTERED`, `ENCODING`, `PUBLIC`, `PRIVATE`, `VIOLATED`, `ILLEGAL`, `DELETED`, `ERROR`
         */
        public ?string $status = null,
        /**
         * 동영상 제목입니다.
         */
        public ?string $title = null,
        /**
         * 동영상 썸네일 URL입니다.
         */
        public ?string $thumbnailUrl = null,
        /**
         * 동영상 재생 URL입니다.
         */
        public ?string $videoUrl = null,
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
            vid: $in->string('vid'),
            status: $in->string('status'),
            title: $in->string('title'),
            thumbnailUrl: $in->string('thumbnailUrl'),
            videoUrl: $in->string('videoUrl'),
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
            'vid' => $this->vid,
            'status' => $this->status,
            'title' => $this->title,
            'thumbnailUrl' => $this->thumbnailUrl,
            'videoUrl' => $this->videoUrl,
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
        return Mask::describe('BrandMessageVideoInfo', get_object_vars($this));
    }
}
