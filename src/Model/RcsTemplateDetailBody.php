<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 상세 조회의 본문 영역입니다.
 *
 * 확인 필요(x-unverified): 등록 요청의 body는 검증 파라미터 배열인데 상세 조회의 body는 description·mTitle·title 객체라서 구조가 다릅니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateDetailBody implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['description', 'mTitle', 'title'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $description 본문 내용입니다.
     * @param string|null $mTitle 메인 타이틀입니다.
     * @param string|null $title 타이틀입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 본문 내용입니다. `#{변수}` 형태의 치환 변수를 포함할 수 있습니다.
         */
        public ?string $description = null,
        /**
         * 메인 타이틀입니다.
         */
        public ?string $mTitle = null,
        /**
         * 타이틀입니다.
         */
        public ?string $title = null,
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
            description: $in->string('description'),
            mTitle: $in->string('mTitle'),
            title: $in->string('title'),
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
            'description' => $this->description,
            'mTitle' => $this->mTitle,
            'title' => $this->title,
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
        return Mask::describe('RcsTemplateDetailBody', get_object_vars($this));
    }
}
