<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Rcs;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->rcs->templates`: list(), get(), create(), update(), cancel(), delete().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Templates
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * RCS 템플릿 목록 조회
     *
     * 브랜드 ID를 기준으로 등록된 RCS 템플릿 목록을 조회합니다.
     *
     * 확인 필요(x-unverified): 설명은 브랜드 ID 기준 조회라고 하나 brandId 등 요청 파라미터가 문서화되어 있지 않습니다. 응답에 pagination이 있으나 페이지 파라미터도
     * 없어 x-sdk-pagination을 두지 않았습니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/list` (operationId `listRcsTemplates`). 429·5xx·네트워크 오류 시 자동 재시도.
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @return Model\RcsTemplateListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(): Model\RcsTemplateListResult
    {
        $response = $this->transport->call(
            'listRcsTemplates',
            response: Model\RcsTemplateListResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateListResult();
    }

    /**
     * RCS 템플릿 상세 조회
     *
     * 메시지베이스 ID를 기준으로 등록된 RCS 템플릿 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase` (operationId `getRcsTemplate`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $messagebaseId 메시지베이스 ID입니다. (쿼리)
     *
     * @return Model\RcsTemplateResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $messagebaseId,
    ): Model\RcsTemplateResult {
        $query = [
            'messagebaseId' => $messagebaseId,
        ];

        $response = $this->transport->call(
            'getRcsTemplate',
            query: $query,
            response: Model\RcsTemplateResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateResult();
    }

    /**
     * RCS 템플릿 등록
     *
     * 브랜드에 RCS 템플릿을 등록합니다. 응답의 `messagebaseId`를 발송 시 `formatId` 필드에 넣습니다(영문 문서 기준).
     *
     * `POST /api/comm/v1/center/rcs/messagebase` (operationId `createRcsTemplate`). 429만 자동 재시도(두 번 실행되면 안 되는 요청).
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param Model\RcsTemplateRequest|array<string, mixed> $body request body: a model, or an array with API field
     *     names (unknown fields are rejected)
     *
     * @return Model\RcsTemplateIdResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\RcsTemplateRequest|array $body,
    ): Model\RcsTemplateIdResult {
        $body = is_array($body) ? Model\RcsTemplateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createRcsTemplate',
            json: $body,
            response: Model\RcsTemplateIdResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateIdResult();
    }

    /**
     * RCS 템플릿 수정
     *
     * 등록된 RCS 템플릿 정보를 수정합니다.
     *
     * 확인 필요(x-unverified): 요청 본문에 수정 대상 템플릿(messagebaseId)을 지정하는 필드가 문서화되어 있지 않습니다.
     *
     * `PUT /api/comm/v1/center/rcs/messagebase` (operationId `updateRcsTemplate`). 429·5xx·네트워크 오류 시 자동 재시도. 속도
     * 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param Model\RcsTemplateRequest|array<string, mixed> $body request body: a model, or an array with API field
     *     names (unknown fields are rejected)
     *
     * @return Model\RcsTemplateIdResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function update(
        #[\SensitiveParameter]
        Model\RcsTemplateRequest|array $body,
    ): Model\RcsTemplateIdResult {
        $body = is_array($body) ? Model\RcsTemplateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'updateRcsTemplate',
            json: $body,
            response: Model\RcsTemplateIdResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateIdResult();
    }

    /**
     * RCS 템플릿 승인 취소
     *
     * 승인 진행 중인 RCS 템플릿의 승인 요청을 취소합니다.
     *
     * `PUT /api/comm/v1/center/rcs/brandId/{brandId}/messagebaseId/{messagebaseId}/cancel` (operationId
     * `cancelRcsTemplateApproval`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 브랜드 ID입니다. (경로)
     * @param string $messagebaseId 메시지베이스 ID입니다. (경로)
     *
     * @return Model\RcsTemplateIdResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function cancel(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        string $messagebaseId,
    ): Model\RcsTemplateIdResult {
        $response = $this->transport->call(
            'cancelRcsTemplateApproval',
            path: ['brandId' => $brandId, 'messagebaseId' => $messagebaseId],
            response: Model\RcsTemplateIdResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateIdResult();
    }

    /**
     * RCS 템플릿 삭제
     *
     * 등록된 RCS 템플릿을 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/rcs/brandId/{brandId}/messagebaseId/{messagebaseId}` (operationId
     * `deleteRcsTemplate`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 브랜드 ID입니다. (경로)
     * @param string $messagebaseId 메시지베이스 ID입니다. (경로)
     *
     * @return Model\RcsTemplateIdResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function delete(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        string $messagebaseId,
    ): Model\RcsTemplateIdResult {
        $response = $this->transport->call(
            'deleteRcsTemplate',
            path: ['brandId' => $brandId, 'messagebaseId' => $messagebaseId],
            response: Model\RcsTemplateIdResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateIdResult();
    }
}
