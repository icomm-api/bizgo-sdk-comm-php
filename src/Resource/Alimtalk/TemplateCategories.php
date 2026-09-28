<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Alimtalk;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->alimtalk->templateCategories`: list().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class TemplateCategories
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 알림톡 템플릿 카테고리 조회
     *
     * 알림톡 템플릿 카테고리를 조회합니다. 문서는 같은 경로에 두 기능을 둡니다.
     * - `categoryCode` 없이 호출: 템플릿 카테고리 전체 목록(`data.data.categories`)
     * - `categoryCode`를 넣어 호출: 해당 카테고리 상세(`data.data.category`)
     *
     * 확인 필요(x-unverified): 전체 조회와 상세 조회가 같은 GET 경로로 문서화되어 있어 한 operation으로 합쳤습니다. 상세 조회 응답의 `category`는 표에서 Object
     * Array, 예시에서 Object로 서로 다릅니다.
     *
     * `GET /api/comm/v1/center/alimtalk/category` (operationId `listAlimtalkTemplateCategories`). 429·5xx·네트워크 오류
     * 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string|null $categoryCode 조회할 템플릿 카테고리 코드입니다. (쿼리)
     *
     * @return Model\AlimtalkTemplateCategoryResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        ?string $categoryCode = null,
    ): Model\AlimtalkTemplateCategoryResult {
        $query = [
            'categoryCode' => $categoryCode,
        ];

        $response = $this->transport->call(
            'listAlimtalkTemplateCategories',
            query: $query,
            response: Model\AlimtalkTemplateCategoryResponse::class,
        );

        return $response->data->data ?? new Model\AlimtalkTemplateCategoryResult();
    }
}
