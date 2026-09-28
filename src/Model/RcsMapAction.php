<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 지도 검색·보여주기·위치 전송 액션입니다.
 *
 * 확인 필요(x-unverified): 문서상 showLocation이 필수로 표시되어 있어, requestLocationPush(위치 전송)만 쓰는 경우에도
 * showLocation이 필요한지 불명확합니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsMapAction implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['showLocation', 'requestLocationPush'];

    /**
     * @param RcsMapActionShowLocation $showLocation 지도 위치 표시 객체입니다.
     * @param array<string, mixed>|null $requestLocationPush 위치 전송 요청 객체입니다.
     */
    public function __construct(
        /**
         * 지도 위치 표시 객체입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public RcsMapActionShowLocation $showLocation,
        /**
         * 위치 전송 요청 객체입니다.
         *
         * 확인 필요(x-unverified): 하위 필드가 문서화되어 있지 않습니다.
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
        $showLocation = $in->model('showLocation', RcsMapActionShowLocation::class, true);
        $requestLocationPush = $in->object('requestLocationPush');

        return $in->create(static fn (): static => new static(
            showLocation: $showLocation ?? throw new \LogicException('checked by RequestData::create()'),
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
            'showLocation' => $this->showLocation->toArray(),
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
        return Mask::describe('RcsMapAction', get_object_vars($this));
    }
}
