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
 * 네이버 톡톡 쿠폰 정보 객체입니다(접수코드 A608).
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class NaverTalkGift implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'code',
        'imageUrl',
        'endDate',
        'name',
        'publisher',
        'couponDescription',
        'label',
        'value',
    ];

    /**
     * @param string $code 쿠폰 코드입니다.
     * @param string $imageUrl 쿠폰 이미지 URL입니다.
     * @param string $endDate 쿠폰 종료일입니다.
     * @param string|null $name 쿠폰 이름입니다.
     * @param string|null $publisher 쿠폰 발급자입니다.
     * @param string|null $couponDescription 쿠폰 설명입니다.
     * @param string|null $label 쿠폰 라벨입니다.
     * @param string|null $value 쿠폰 내용입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 쿠폰 코드입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $code,
        /**
         * 쿠폰 이미지 URL입니다. 쿠폰을 첨부하면 반드시 필요합니다(리포트 코드 72106).
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $imageUrl,
        /**
         * 쿠폰 종료일입니다. 오늘 이후 날짜를 `YYYY-MM-DD` 형식으로 입력합니다(리포트 코드 72107).
         *
         * 필수 · 형식 `^[0-9]{4}-[0-9]{2}-[0-9]{2}$` · 시각 형식 `YYYY-MM-DD`
         */
        #[\SensitiveParameter]
        public string $endDate,
        /**
         * 쿠폰 이름입니다. 생략하면 템플릿에 등록된 이름으로 발송됩니다.
         */
        #[\SensitiveParameter]
        public ?string $name = null,
        /**
         * 쿠폰 발급자입니다.
         */
        #[\SensitiveParameter]
        public ?string $publisher = null,
        /**
         * 쿠폰 설명입니다.
         */
        #[\SensitiveParameter]
        public ?string $couponDescription = null,
        /**
         * 쿠폰 라벨입니다.
         */
        #[\SensitiveParameter]
        public ?string $label = null,
        /**
         * 쿠폰 내용입니다.
         */
        #[\SensitiveParameter]
        public ?string $value = null,
    ) {
        Check::all(
            Check::pattern('endDate', $endDate, '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'),
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
        $code = $in->requiredString('code');
        $imageUrl = $in->requiredString('imageUrl');
        $endDate = $in->requiredString('endDate');
        $name = $in->string('name');
        $publisher = $in->string('publisher');
        $couponDescription = $in->string('couponDescription');
        $label = $in->string('label');
        $value = $in->string('value');

        return $in->create(static fn (): static => new static(
            code: $code,
            imageUrl: $imageUrl,
            endDate: $endDate,
            name: $name,
            publisher: $publisher,
            couponDescription: $couponDescription,
            label: $label,
            value: $value,
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
            'code' => $this->code,
            'imageUrl' => $this->imageUrl,
            'endDate' => $this->endDate,
            'name' => $this->name,
            'publisher' => $this->publisher,
            'couponDescription' => $this->couponDescription,
            'label' => $this->label,
            'value' => $this->value,
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
            'code' => $this->code,
            'imageUrl' => $this->imageUrl,
            'endDate' => $this->endDate,
            'name' => $this->name,
            'publisher' => $this->publisher,
            'couponDescription' => $this->couponDescription,
            'label' => $this->label,
            'value' => $this->value,
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
        return Mask::describe('NaverTalkGift', get_object_vars($this));
    }
}
