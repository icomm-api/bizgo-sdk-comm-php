<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 버튼 액션 객체입니다. 아래 액션 유형 객체를 넣습니다.
 *
 * 확인 필요(x-unverified): 한 action에 액션 유형을 하나만 넣어야 하는지 문서에 명시되어 있지 않습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsAction implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['urlAction', 'dialerAction', 'mapAction', 'calendarAction', 'composeAction'];

    /**
     * @param RcsUrlAction|null $urlAction
     * @param RcsDialerAction|null $dialerAction
     * @param RcsMapAction|null $mapAction
     * @param RcsCalendarAction|null $calendarAction
     * @param RcsComposeAction|null $composeAction
     */
    public function __construct(
        #[\SensitiveParameter]
        public ?RcsUrlAction $urlAction = null,
        #[\SensitiveParameter]
        public ?RcsDialerAction $dialerAction = null,
        #[\SensitiveParameter]
        public ?RcsMapAction $mapAction = null,
        #[\SensitiveParameter]
        public ?RcsCalendarAction $calendarAction = null,
        #[\SensitiveParameter]
        public ?RcsComposeAction $composeAction = null,
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
        $urlAction = $in->model('urlAction', RcsUrlAction::class);
        $dialerAction = $in->model('dialerAction', RcsDialerAction::class);
        $mapAction = $in->model('mapAction', RcsMapAction::class);
        $calendarAction = $in->model('calendarAction', RcsCalendarAction::class);
        $composeAction = $in->model('composeAction', RcsComposeAction::class);

        return $in->create(static fn (): static => new static(
            urlAction: $urlAction,
            dialerAction: $dialerAction,
            mapAction: $mapAction,
            calendarAction: $calendarAction,
            composeAction: $composeAction,
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
            'urlAction' => $this->urlAction?->toArray(),
            'dialerAction' => $this->dialerAction?->toArray(),
            'mapAction' => $this->mapAction?->toArray(),
            'calendarAction' => $this->calendarAction?->toArray(),
            'composeAction' => $this->composeAction?->toArray(),
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
            'urlAction' => $this->urlAction,
            'dialerAction' => $this->dialerAction,
            'mapAction' => $this->mapAction,
            'calendarAction' => $this->calendarAction,
            'composeAction' => $this->composeAction,
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
        return Mask::describe('RcsAction', get_object_vars($this));
    }
}
