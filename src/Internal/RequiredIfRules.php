<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Internal;

/**
 * `x-sdk-required-if` rules of the spec, evaluated by {@see RequiredIf} in the request model constructors.
 *
 * - MODELS: rules of each request model: own fields (`required`) and paths relative to the model (`paths`).
 * - ROOTS: for each request body root, the rules below it with `$.` paths: `at` is the location pattern of
 *   the object the condition reads (`name[]` = every element), `paths` are from the root (without `$.`).
 *
 * @internal
 *
 * @phpstan-type Condition array{field: string, op: 'equals'|'notEquals'|'in'|'notIn', values: non-empty-list<string>}
 * @phpstan-type ModelRule array{when: Condition, required: list<string>, paths: list<string>}
 * @phpstan-type RootRule array{at: string, when: Condition, paths: list<string>}
 */
final class RequiredIfRules
{
    /** @var array<string, list<ModelRule>> schema => rules */
    public const MODELS = [
        'AlimtalkMessage' => [
            [
                'when' => ['field' => 'sendType', 'op' => 'notEquals', 'values' => ['template']],
                'required' => ['msgType', 'text'],
                'paths' => [],
            ],
        ],
        'BrandMessage' => [
            [
                'when' => ['field' => 'sendType', 'op' => 'in', 'values' => ['basic', 'free']],
                'required' => ['msgType'],
                'paths' => [],
            ],
            [
                'when' => ['field' => 'sendType', 'op' => 'in', 'values' => ['basic', 'template']],
                'required' => ['templateCode', 'targeting'],
                'paths' => [],
            ],
        ],
        'BrandMessageButton' => [
            [
                'when' => ['field' => 'type', 'op' => 'equals', 'values' => ['WL']],
                'required' => ['urlPc', 'urlMobile'],
                'paths' => [],
            ],
            [
                'when' => ['field' => 'type', 'op' => 'equals', 'values' => ['AL']],
                'required' => ['urlMobile'],
                'paths' => [],
            ],
        ],
        'CounselPlainMessageRequest' => [
            [
                'when' => ['field' => 'msgType', 'op' => 'equals', 'values' => ['FILE']],
                'required' => [],
                'paths' => ['attachment.file.fileName', 'attachment.file.fileSize'],
            ],
        ],
        'CounselRichMessageRequest' => [
            [
                'when' => ['field' => 'msgType', 'op' => 'in', 'values' => ['IMAGE', 'WIDE']],
                'required' => [],
                'paths' => ['attachment.image'],
            ],
            [
                'when' => ['field' => 'msgType', 'op' => 'in', 'values' => ['ITEM_LIST', 'WIDE_ITEM_LIST']],
                'required' => [],
                'paths' => ['attachment.item'],
            ],
            [
                'when' => ['field' => 'msgType', 'op' => 'equals', 'values' => ['CAROUSEL_FEED']],
                'required' => ['carousel'],
                'paths' => [],
            ],
            [
                'when' => ['field' => 'msgType', 'op' => 'equals', 'values' => ['KAKAO_CERT']],
                'required' => ['certExpiry'],
                'paths' => [],
            ],
        ],
        'RcsMessage' => [
            [
                'when' => ['field' => 'header', 'op' => 'equals', 'values' => ['1']],
                'required' => ['footer'],
                'paths' => [],
            ],
        ],
    ];

    /** @var array<string, list<RootRule>> request body schema => rules with `$.` paths */
    public const ROOTS = [
        'ReservationCreateRequest' => [
            [
                'at' => 'messageFlow[].alimtalk',
                'when' => ['field' => 'sendType', 'op' => 'equals', 'values' => ['template']],
                'paths' => ['destinations[].replaceWords'],
            ],
            [
                'at' => 'messageFlow[].brandmessage',
                'when' => ['field' => 'sendType', 'op' => 'equals', 'values' => ['template']],
                'paths' => ['destinations[].replaceWords'],
            ],
        ],
        'SendOmniRequest' => [
            [
                'at' => 'messageFlow[].alimtalk',
                'when' => ['field' => 'sendType', 'op' => 'equals', 'values' => ['template']],
                'paths' => ['destinations[].replaceWords'],
            ],
            [
                'at' => 'messageFlow[].brandmessage',
                'when' => ['field' => 'sendType', 'op' => 'equals', 'values' => ['template']],
                'paths' => ['destinations[].replaceWords'],
            ],
        ],
    ];

    private function __construct()
    {
    }
}
