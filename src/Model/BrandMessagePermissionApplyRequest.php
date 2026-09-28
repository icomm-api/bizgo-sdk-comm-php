<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 브랜드메시지 발송권한(고객사 회원 대상 발송, targeting M·N·O) 신청 요청입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessagePermissionApplyRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['brandMessage'];

    /**
     * @param BrandMessagePermissionApplyRequestBrandMessage $brandMessage 발송 권한 신청 정보입니다.
     */
    public function __construct(
        /**
         * 발송 권한 신청 정보입니다. 다른 브랜드메시지 API와 달리 키 이름이 `brandMessage`(대문자 M)입니다.
         *
         * 필수
         *
         * 확인 필요(x-unverified): 다른 API는 `brandmessage`(소문자)를 쓰는데 이 API만 `brandMessage`로 표기되어 있습니다(Part A·B
         * 동일). 서버가 대소문자를 구분하는지 확인이 필요합니다.
         */
        #[\SensitiveParameter]
        public BrandMessagePermissionApplyRequestBrandMessage $brandMessage,
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
        $brandMessage = $in->model('brandMessage', BrandMessagePermissionApplyRequestBrandMessage::class, true);

        return $in->create(static fn (): static => new static(
            brandMessage: $brandMessage ?? throw new \LogicException('checked by RequestData::create()'),
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
            'brandMessage' => $this->brandMessage->toArray(),
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
            'brandMessage' => $this->brandMessage,
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
        return Mask::describe('BrandMessagePermissionApplyRequest', get_object_vars($this));
    }
}
