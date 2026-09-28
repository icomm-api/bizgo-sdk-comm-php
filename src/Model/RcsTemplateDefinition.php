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
 * 등록·수정할 RCS 템플릿 정보입니다.
 *
 * 확인 필요(x-unverified): 템플릿 양식(messagebaseformId/formatId)을 지정하는 필드와, 수정 시 대상 템플릿(messagebaseId)을 지정하는
 * 필드가 문서에 없습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsTemplateDefinition implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['brandId', 'templateName', 'body', 'buttons'];

    /**
     * @param string $brandId 브랜드 ID입니다.
     * @param string $templateName 템플릿 이름입니다.
     * @param list<RcsTemplateBodyParam> $body 본문 영역 정의입니다.
     * @param list<RcsTemplateButton> $buttons 버튼 정의입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 브랜드 ID입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $brandId,
        /**
         * 템플릿 이름입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public string $templateName,
        /**
         * 본문 영역 정의입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public array $body,
        /**
         * 버튼 정의입니다.
         *
         * 필수
         */
        #[\SensitiveParameter]
        public array $buttons,
    ) {
        Check::all(
            Check::listOf('body', $body, [RcsTemplateBodyParam::class]),
            Check::listOf('buttons', $buttons, [RcsTemplateButton::class]),
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
        $brandId = $in->requiredString('brandId');
        $templateName = $in->requiredString('templateName');
        $body = $in->modelList('body', RcsTemplateBodyParam::class, true);
        $buttons = $in->modelList('buttons', RcsTemplateButton::class, true);

        return $in->create(static fn (): static => new static(
            brandId: $brandId,
            templateName: $templateName,
            body: $body ?? throw new \LogicException('checked by RequestData::create()'),
            buttons: $buttons ?? throw new \LogicException('checked by RequestData::create()'),
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
            'brandId' => $this->brandId,
            'templateName' => $this->templateName,
            'body' => Serialize::models($this->body),
            'buttons' => Serialize::models($this->buttons),
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
            'brandId' => $this->brandId,
            'templateName' => $this->templateName,
            'body' => $this->body,
            'buttons' => $this->buttons,
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
        return Mask::describe('RcsTemplateDefinition', get_object_vars($this));
    }
}
