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
 * 템플릿 버튼 액션 객체입니다. 발송용 `RcsSuggestion`과 달리 `displayText`가 `action` 안에 있고,
 * 클립보드 복사 액션(`clipboardAction`)을 쓸 수 있습니다.
 *
 * 확인 필요(x-unverified): 한 action에 액션 유형을 하나만 넣어야 하는지 문서에 명시되어 있지 않습니다.
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class RcsTemplateAction implements ResponseModel
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

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = ['displayText'];

    /**
     * @param string|null $displayText 버튼 표시 텍스트입니다.
     * @param RcsTemplateClipboardAction|null $clipboardAction
     * @param RcsUrlAction|null $urlAction
     * @param RcsDialerAction|null $dialerAction
     * @param RcsTemplateMapAction|null $mapAction
     * @param RcsCalendarAction|null $calendarAction
     * @param RcsTemplateComposeAction|null $composeAction
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 버튼 표시 텍스트입니다.
         */
        public ?string $displayText = null,
        public ?RcsTemplateClipboardAction $clipboardAction = null,
        public ?RcsUrlAction $urlAction = null,
        public ?RcsDialerAction $dialerAction = null,
        public ?RcsTemplateMapAction $mapAction = null,
        public ?RcsCalendarAction $calendarAction = null,
        public ?RcsTemplateComposeAction $composeAction = null,
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
            displayText: $in->string('displayText'),
            clipboardAction: $in->model('clipboardAction', RcsTemplateClipboardAction::class),
            urlAction: $in->model('urlAction', RcsUrlAction::class),
            dialerAction: $in->model('dialerAction', RcsDialerAction::class),
            mapAction: $in->model('mapAction', RcsTemplateMapAction::class),
            calendarAction: $in->model('calendarAction', RcsCalendarAction::class),
            composeAction: $in->model('composeAction', RcsTemplateComposeAction::class),
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
            'displayText' => $this->displayText,
            'clipboardAction' => $this->clipboardAction?->toArray(),
            'urlAction' => $this->urlAction?->toArray(),
            'dialerAction' => $this->dialerAction?->toArray(),
            'mapAction' => $this->mapAction?->toArray(),
            'calendarAction' => $this->calendarAction?->toArray(),
            'composeAction' => $this->composeAction?->toArray(),
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
        return Mask::describe('RcsTemplateAction', get_object_vars($this));
    }
}
