<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * 템플릿 버튼 액션 객체입니다. 발송용 `RcsSuggestion`과 달리 `displayText`가 `action` 안에 있고,
 * 클립보드 복사 액션(`clipboardAction`)을 쓸 수 있습니다.
 *
 * 확인 필요(x-unverified): 한 action에 액션 유형을 하나만 넣어야 하는지 문서에 명시되어 있지 않습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsTemplateAction implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'displayText',
        'clipboardAction',
        'urlAction',
        'dialerAction',
        'mapAction',
        'calendarAction',
        'composeAction',
    ];

    /**
     * @param string $displayText 버튼 표시 텍스트입니다.
     * @param RcsTemplateClipboardAction|null $clipboardAction
     * @param RcsUrlAction|null $urlAction
     * @param RcsDialerAction|null $dialerAction
     * @param RcsTemplateMapAction|null $mapAction
     * @param RcsCalendarAction|null $calendarAction
     * @param RcsTemplateComposeAction|null $composeAction
     */
    public function __construct(
        /**
         * 버튼 표시 텍스트입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $displayText,
        #[\SensitiveParameter]
        public ?RcsTemplateClipboardAction $clipboardAction = null,
        #[\SensitiveParameter]
        public ?RcsUrlAction $urlAction = null,
        #[\SensitiveParameter]
        public ?RcsDialerAction $dialerAction = null,
        #[\SensitiveParameter]
        public ?RcsTemplateMapAction $mapAction = null,
        #[\SensitiveParameter]
        public ?RcsCalendarAction $calendarAction = null,
        #[\SensitiveParameter]
        public ?RcsTemplateComposeAction $composeAction = null,
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
        $displayText = $in->requiredString('displayText');
        $clipboardAction = $in->model('clipboardAction', RcsTemplateClipboardAction::class);
        $urlAction = $in->model('urlAction', RcsUrlAction::class);
        $dialerAction = $in->model('dialerAction', RcsDialerAction::class);
        $mapAction = $in->model('mapAction', RcsTemplateMapAction::class);
        $calendarAction = $in->model('calendarAction', RcsCalendarAction::class);
        $composeAction = $in->model('composeAction', RcsTemplateComposeAction::class);

        return $in->create(static fn (): static => new static(
            displayText: $displayText,
            clipboardAction: $clipboardAction,
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
            'displayText' => $this->displayText,
            'clipboardAction' => $this->clipboardAction?->toArray(),
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
            'displayText' => $this->displayText,
            'clipboardAction' => $this->clipboardAction,
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
        return Mask::describe('RcsTemplateAction', get_object_vars($this));
    }
}
