<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\BrandMessage;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->brandMessage->groupTags`: list(), get(), create(), update(), delete().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class GroupTags
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 브랜드메시지 전체 그룹태그 조회
     *
     * 발신프로필 키로 브랜드메시지 전체 그룹태그 목록을 조회합니다. 그룹태그 키는 자유형 발송의 `groupTagKey`에 씁니다.
     *
     * `GET /api/comm/v1/center/brandmessage/groupTag/list` (operationId `listBrandMessageGroupTags`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     *
     * @return Model\BrandMessageGroupTagListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\BrandMessageGroupTagListResult {
        $query = [
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'listBrandMessageGroupTags',
            query: $query,
            response: Model\BrandMessageGroupTagListResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupTagListResult();
    }

    /**
     * 브랜드메시지 그룹태그 조회
     *
     * 발신프로필 키와 그룹태그 키로 그룹태그 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/groupTag` (operationId `getBrandMessageGroupTag`). 429·5xx·네트워크 오류 시
     * 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $groupTagKey 그룹태그 키입니다. (쿼리)
     *
     * @return Model\BrandMessageGroupTagListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $groupTagKey,
    ): Model\BrandMessageGroupTagListResult {
        $query = [
            'senderKey' => $senderKey,
            'groupTagKey' => $groupTagKey,
        ];

        $response = $this->transport->call(
            'getBrandMessageGroupTag',
            query: $query,
            response: Model\BrandMessageGroupTagListResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupTagListResult();
    }

    /**
     * 브랜드메시지 그룹태그 등록
     *
     * 브랜드메시지 그룹태그를 등록합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/groupTag` (operationId `createBrandMessageGroupTag`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageGroupTagRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageGroupTagListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\BrandMessageGroupTagRequest|array $body,
    ): Model\BrandMessageGroupTagListResult {
        $body = is_array($body) ? Model\BrandMessageGroupTagRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createBrandMessageGroupTag',
            json: $body,
            response: Model\BrandMessageGroupTagListResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupTagListResult();
    }

    /**
     * 브랜드메시지 그룹태그 수정
     *
     * 등록된 브랜드메시지 그룹태그 정보를 수정합니다.
     *
     * `PUT /api/comm/v1/center/brandmessage/groupTag` (operationId `updateBrandMessageGroupTag`). 429·5xx·네트워크 오류
     * 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageGroupTagRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageGroupTagListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function update(
        #[\SensitiveParameter]
        Model\BrandMessageGroupTagRequest|array $body,
    ): Model\BrandMessageGroupTagListResult {
        $body = is_array($body) ? Model\BrandMessageGroupTagRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'updateBrandMessageGroupTag',
            json: $body,
            response: Model\BrandMessageGroupTagListResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupTagListResult();
    }

    /**
     * 브랜드메시지 그룹태그 삭제
     *
     * 발신프로필 키와 그룹태그 키로 그룹태그를 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/brandmessage/groupTag/senderKey/{senderKey}/groupTagKey/{groupTagKey}`
     * (operationId `deleteBrandMessageGroupTag`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (경로)
     * @param string $groupTagKey 삭제할 그룹태그 키입니다. (경로)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function delete(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $groupTagKey,
    ): void {
        $this->transport->call(
            'deleteBrandMessageGroupTag',
            path: ['senderKey' => $senderKey, 'groupTagKey' => $groupTagKey],
            response: Model\ApiResponse::class,
        );
    }
}
