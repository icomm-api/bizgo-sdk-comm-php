<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 네이버 톡톡 버튼입니다.
 * - `WEB_LINK` 버튼은 `mobileUrl`이 필수입니다(접수코드 A606).
 * - `APP_LINK` 버튼은 `aOsAppScheme`과 `iOsAppScheme`이 모두 필수입니다(접수코드 A607).
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class NaverTalkButton implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['buttonCode', 'mobileUrl', 'pcUrl', 'aOsAppScheme', 'iOsAppScheme'];

    /**
     * @param string $buttonCode 버튼 코드입니다.
     * @param string|null $mobileUrl 모바일 이동 URL입니다.
     * @param string|null $pcUrl PC 이동 URL입니다.
     * @param string|null $aOsAppScheme Android OS 환경에서 버튼 클릭 시 이동할 앱 링크입니다.
     * @param string|null $iOsAppScheme iOS 환경에서 버튼 클릭 시 이동할 앱 링크입니다.
     */
    public function __construct(
        /**
         * 버튼 코드입니다. 알려진 값은 `WEB_LINK`, `APP_LINK`입니다(요청 예시와 접수코드 A605~A607 기준). 전체 목록은 원문에 없습니다.
         *
         * 필수 · 알려진 값: `WEB_LINK`, `APP_LINK`
         */
        #[\SensitiveParameter]
        public string $buttonCode,
        /**
         * 모바일 이동 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $mobileUrl = null,
        /**
         * PC 이동 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $pcUrl = null,
        /**
         * Android OS 환경에서 버튼 클릭 시 이동할 앱 링크입니다.
         */
        #[\SensitiveParameter]
        public ?string $aOsAppScheme = null,
        /**
         * iOS 환경에서 버튼 클릭 시 이동할 앱 링크입니다.
         */
        #[\SensitiveParameter]
        public ?string $iOsAppScheme = null,
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
        $buttonCode = $in->requiredString('buttonCode');
        $mobileUrl = $in->string('mobileUrl');
        $pcUrl = $in->string('pcUrl');
        $aOsAppScheme = $in->string('aOsAppScheme');
        $iOsAppScheme = $in->string('iOsAppScheme');

        return $in->create(static fn (): static => new static(
            buttonCode: $buttonCode,
            mobileUrl: $mobileUrl,
            pcUrl: $pcUrl,
            aOsAppScheme: $aOsAppScheme,
            iOsAppScheme: $iOsAppScheme,
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
            'buttonCode' => $this->buttonCode,
            'mobileUrl' => $this->mobileUrl,
            'pcUrl' => $this->pcUrl,
            'aOsAppScheme' => $this->aOsAppScheme,
            'iOsAppScheme' => $this->iOsAppScheme,
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
            'buttonCode' => $this->buttonCode,
            'mobileUrl' => $this->mobileUrl,
            'pcUrl' => $this->pcUrl,
            'aOsAppScheme' => $this->aOsAppScheme,
            'iOsAppScheme' => $this->iOsAppScheme,
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
        return Mask::describe('NaverTalkButton', get_object_vars($this));
    }
}
