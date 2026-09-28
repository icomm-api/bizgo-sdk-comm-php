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
 * 브랜드메시지 기본형 템플릿 동보 발송 요청입니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class BrandMessageGroupSendCreateRequest implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['brandmessage', 'paymentCode'];

    /**
     * @param BrandMessageGroupSendCreateRequestBrandmessage $brandmessage 기본형 템플릿 동보 발송 정보입니다.
     * @param string|null $paymentCode 정산용 부서 코드입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 기본형 템플릿 동보 발송 정보입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public BrandMessageGroupSendCreateRequestBrandmessage $brandmessage,
        /**
         * 정산용 부서 코드입니다. 최대 20자입니다.
         *
         * 최대 20자
         */
        #[\SensitiveParameter]
        public ?string $paymentCode = null,
    ) {
        Check::all(
            Check::maxLength('paymentCode', $paymentCode, 20),
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
        $brandmessage = $in->model('brandmessage', BrandMessageGroupSendCreateRequestBrandmessage::class, true);
        $paymentCode = $in->string('paymentCode');

        return $in->create(static fn (): static => new static(
            brandmessage: $brandmessage ?? throw new \LogicException('checked by RequestData::create()'),
            paymentCode: $paymentCode,
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
            'brandmessage' => $this->brandmessage->toArray(),
            'paymentCode' => $this->paymentCode,
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
            'brandmessage' => $this->brandmessage,
            'paymentCode' => $this->paymentCode,
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
        return Mask::describe('BrandMessageGroupSendCreateRequest', get_object_vars($this));
    }
}
