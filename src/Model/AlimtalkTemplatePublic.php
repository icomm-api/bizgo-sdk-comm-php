<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 카카오가 제공하는 공용템플릿 1건입니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class AlimtalkTemplatePublic implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'templateName',
        'templateCode',
        'status',
        'categoryCode',
        'releaseDate',
        'previewImageUrl',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $templateName 공용 템플릿 이름입니다.
     * @param string|null $templateCode 공용 템플릿 코드입니다.
     * @param string|null $status 공용 템플릿 상태입니다.
     * @param string|null $categoryCode 카테고리 코드입니다.
     * @param string|null $releaseDate 공용 템플릿 배포 일시(yyyy-MM-dd HH:mm:ss)입니다.
     * @param string|null $previewImageUrl 미리보기 이미지 URL입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 공용 템플릿 이름입니다.
         */
        public ?string $templateName = null,
        /**
         * 공용 템플릿 코드입니다.
         */
        public ?string $templateCode = null,
        /**
         * 공용 템플릿 상태입니다. 문서 예시 값은 `APR`입니다.
         *
         * 알려진 값: `APR`
         */
        public ?string $status = null,
        /**
         * 카테고리 코드입니다.
         */
        public ?string $categoryCode = null,
        /**
         * 공용 템플릿 배포 일시(yyyy-MM-dd HH:mm:ss)입니다.
         *
         * 시각 형식 `yyyy-MM-dd HH:mm:ss`
         */
        public ?string $releaseDate = null,
        /**
         * 미리보기 이미지 URL입니다.
         */
        public ?string $previewImageUrl = null,
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
            templateName: $in->string('templateName'),
            templateCode: $in->string('templateCode'),
            status: $in->string('status'),
            categoryCode: $in->string('categoryCode'),
            releaseDate: $in->string('releaseDate'),
            previewImageUrl: $in->string('previewImageUrl'),
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
            'templateName' => $this->templateName,
            'templateCode' => $this->templateCode,
            'status' => $this->status,
            'categoryCode' => $this->categoryCode,
            'releaseDate' => $this->releaseDate,
            'previewImageUrl' => $this->previewImageUrl,
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
        return Mask::describe('AlimtalkTemplatePublic', get_object_vars($this));
    }
}
