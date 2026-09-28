<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 버튼의 지도 액션입니다. 발송용 `RcsMapAction`과 달리 `showLocation`이 선택이고
 * `fallbackUrl`이 `mapAction` 바로 아래에 있으며, 위치 `label`이 문자열입니다.
 *
 * 확인 필요(x-unverified): 발송 API의 mapAction(showLocation 필수, fallbackUrl이 showLocation 아래)과 구조가 다릅니다. 어느
 * 쪽이 맞는지, requestLocationPush의 하위 필드는 무엇인지 문서화되어 있지 않습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsTemplateMapAction implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['showLocation', 'fallbackUrl', 'requestLocationPush'];

    /**
     * @param RcsTemplateMapActionShowLocation|null $showLocation 지도 위치 표시 객체입니다.
     * @param string|null $fallbackUrl 위치 조회 실패 시 이동할 fallback URL입니다.
     * @param array<string, mixed>|null $requestLocationPush 위치 전송 요청 객체입니다.
     */
    public function __construct(
        /**
         * 지도 위치 표시 객체입니다.
         */
        #[\SensitiveParameter]
        public ?RcsTemplateMapActionShowLocation $showLocation = null,
        /**
         * 위치 조회 실패 시 이동할 fallback URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $fallbackUrl = null,
        /**
         * 위치 전송 요청 객체입니다.
         */
        #[\SensitiveParameter]
        public ?array $requestLocationPush = null,
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
        $showLocation = $in->model('showLocation', RcsTemplateMapActionShowLocation::class);
        $fallbackUrl = $in->string('fallbackUrl');
        $requestLocationPush = $in->object('requestLocationPush');

        return $in->create(static fn (): static => new static(
            showLocation: $showLocation,
            fallbackUrl: $fallbackUrl,
            requestLocationPush: $requestLocationPush,
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
            'showLocation' => $this->showLocation?->toArray(),
            'fallbackUrl' => $this->fallbackUrl,
            'requestLocationPush' => $this->requestLocationPush,
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
            'showLocation' => $this->showLocation,
            'fallbackUrl' => $this->fallbackUrl,
            'requestLocationPush' => Serialize::jsonObject($this->requestLocationPush),
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
        return Mask::describe('RcsTemplateMapAction', get_object_vars($this));
    }
}
