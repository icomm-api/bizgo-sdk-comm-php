<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 상담톡 웹훅 수신 응답입니다. 리포트·MO 웹훅(`{"msgKey": ...}`)과 달리 `code`/`result`를 돌려줍니다.
 * HTTP 200과 이 규격의 응답을 받아야 정상 처리되며, 응답이 없거나 규격이 다르면 최대 3회 재시도합니다.
 *
 * 확인 필요(x-unverified): code/result가 필수인지, A000 외의 code를 보내면 실패로 보고 재시도하는지 문서에 없습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class CounselWebhookAck implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['code', 'result'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $code 웹훅 처리 결과 코드입니다.
     * @param string|null $result 웹훅 처리 결과 메시지입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 웹훅 처리 결과 코드입니다. 문서 예시 값은 `A000`입니다.
         */
        public ?string $code = null,
        /**
         * 웹훅 처리 결과 메시지입니다. 문서 예시 값은 `Success`입니다.
         */
        public ?string $result = null,
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
        return Mask::describe('CounselWebhookAck', get_object_vars($this));
    }
}
