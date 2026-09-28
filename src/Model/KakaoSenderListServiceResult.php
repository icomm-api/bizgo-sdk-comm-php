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
final readonly class KakaoSenderListServiceResult implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'code',
        'result',
        'ref',
        'resultMsg',
        'page',
        'totalPage',
        'totalCount',
        'hasNext',
        'channels',
    ];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['code', 'result'];

    /**
     * @param string|null $code 처리 결과 코드입니다.
     * @param string|null $result 처리 결과 설명입니다.
     * @param string|null $ref 요청 시 전달한 참조값입니다.
     * @param string|null $resultMsg 호출 결과 메시지입니다.
     * @param int|null $page 현재 페이지 번호입니다.
     * @param int|null $totalPage 전체 페이지 수입니다.
     * @param int|null $totalCount 전체 조회 건수입니다.
     * @param bool|null $hasNext 다음 페이지 존재 여부입니다.
     * @param list<KakaoSenderChannel>|null $channels 발신프로필 목록입니다.
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
        /**
         * 호출 결과 메시지입니다.
         */
        public ?string $resultMsg = null,
        /**
         * 현재 페이지 번호입니다.
         */
        public ?int $page = null,
        /**
         * 전체 페이지 수입니다.
         */
        public ?int $totalPage = null,
        /**
         * 전체 조회 건수입니다.
         */
        public ?int $totalCount = null,
        /**
         * 다음 페이지 존재 여부입니다.
         */
        public ?bool $hasNext = null,
        /**
         * 발신프로필 목록입니다.
         */
        public ?array $channels = null,
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
            resultMsg: $in->string('resultMsg'),
            page: $in->int('page'),
            totalPage: $in->int('totalPage'),
            totalCount: $in->int('totalCount'),
            hasNext: $in->bool('hasNext'),
            channels: $in->modelList('channels', KakaoSenderChannel::class),
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
            'resultMsg' => $this->resultMsg,
            'page' => $this->page,
            'totalPage' => $this->totalPage,
            'totalCount' => $this->totalCount,
            'hasNext' => $this->hasNext,
            'channels' => Serialize::models($this->channels),
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
        return Mask::describe('KakaoSenderListServiceResult', get_object_vars($this));
    }
}
