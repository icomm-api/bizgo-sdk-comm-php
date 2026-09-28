<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 *
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselCertStatusServiceResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['code', 'result', 'ref', 'data'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['code', 'result'];

    /**
     * @param string|null $code 처리 결과 코드입니다.
     * @param string|null $result 처리 결과 설명입니다.
     * @param string|null $ref 요청 시 전달한 참조값입니다.
     * @param CounselCertStatusResult|null $data
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 처리 결과 코드입니다. `A000`이면 성공입니다. 코드 목록은 에러코드 문서를 참고합니다.
         */
        public ?string $code = null,
        /**
         * 처리 결과 설명입니다.
         */
        public ?string $result = null,
        /**
         * 요청 시 전달한 참조값입니다.
         */
        public ?string $ref = null,
        public ?CounselCertStatusResult $data = null,
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
            code: $in->string('code'),
            result: $in->string('result'),
            ref: $in->string('ref'),
            data: $in->model('data', CounselCertStatusResult::class),
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
            'code' => $this->code,
            'result' => $this->result,
            'ref' => $this->ref,
            'data' => $this->data?->toArray(),
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
        return Mask::describe('CounselCertStatusServiceResult', get_object_vars($this));
    }
}
