<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model\Response;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;
use Bizgo\Model\ResponseModel;

/**
 * 템플릿 버튼의 지도 액션입니다. 발송용 `RcsMapAction`과 달리 `showLocation`이 선택이고
 * `fallbackUrl`이 `mapAction` 바로 아래에 있으며, 위치 `label`이 문자열입니다.
 *
 * 확인 필요(x-unverified): 발송 API의 mapAction(showLocation 필수, fallbackUrl이 showLocation 아래)과 구조가 다릅니다. 어느
 * 쪽이 맞는지, requestLocationPush의 하위 필드는 무엇인지 문서화되어 있지 않습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateMapAction implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['showLocation', 'fallbackUrl', 'requestLocationPush'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param RcsTemplateMapActionShowLocation|null $showLocation 지도 위치 표시 객체입니다.
     * @param string|null $fallbackUrl 위치 조회 실패 시 이동할 fallback URL입니다.
     * @param array<string, mixed>|null $requestLocationPush 위치 전송 요청 객체입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 지도 위치 표시 객체입니다.
         */
        public ?RcsTemplateMapActionShowLocation $showLocation = null,
        /**
         * 위치 조회 실패 시 이동할 fallback URL입니다.
         */
        public ?string $fallbackUrl = null,
        /**
         * 위치 전송 요청 객체입니다.
         */
        public ?array $requestLocationPush = null,
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
            showLocation: $in->model('showLocation', RcsTemplateMapActionShowLocation::class),
            fallbackUrl: $in->string('fallbackUrl'),
            requestLocationPush: $in->object('requestLocationPush'),
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
            'showLocation' => $this->showLocation?->toArray(),
            'fallbackUrl' => $this->fallbackUrl,
            'requestLocationPush' => $this->requestLocationPush,
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
        return Mask::describe('RcsTemplateMapAction', get_object_vars($this));
    }
}
