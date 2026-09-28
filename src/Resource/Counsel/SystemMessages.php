<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Counsel;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->counsel->systemMessages`: list(), create(), delete(), requestApproval(), cancelApproval().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class SystemMessages
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 시스템 메시지 조회
     *
     * 시스템 메시지 목록을 조회합니다. `id`를 주면 해당 메시지만 조회합니다. 페이지네이션은 문서에 없습니다.
     *
     * `GET /api/comm/v1/center/cstalk/system/message` (operationId `listCounselSystemMessages`). 429·5xx·네트워크 오류 시
     * 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다. (쿼리)
     * @param string|null $id 시스템 메시지 ID입니다. (쿼리)
     *
     * @return Model\CounselSystemMessageListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $id = null,
    ): Model\CounselSystemMessageListResult {
        $query = [
            'senderKey' => $senderKey,
            'id' => $id,
        ];

        $response = $this->transport->call(
            'listCounselSystemMessages',
            query: $query,
            response: Model\CounselSystemMessageListResponse::class,
        );

        return $response->data->data ?? new Model\CounselSystemMessageListResult();
    }

    /**
     * 시스템 메시지 등록
     *
     * 시스템 메시지를 등록합니다.
     *
     * `POST /api/comm/v1/center/cstalk/system/message` (operationId `createCounselSystemMessage`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselSystemMessageCreateRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @return Model\CounselSystemMessageCreateResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\CounselSystemMessageCreateRequest|array $body,
    ): Model\CounselSystemMessageCreateResult {
        $body = is_array($body) ? Model\CounselSystemMessageCreateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createCounselSystemMessage',
            json: $body,
            response: Model\CounselSystemMessageCreateResponse::class,
        );

        return $response->data->data ?? new Model\CounselSystemMessageCreateResult();
    }

    /**
     * 시스템 메시지 삭제
     *
     * 시스템 메시지를 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/cstalk/system/message/senderKey/{senderKey}/id/{id}` (operationId
     * `deleteCounselSystemMessage`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string $senderKey 발신프로필 키입니다. (경로)
     * @param string $id 삭제할 시스템 메시지 ID입니다. (경로)
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
        string $id,
    ): void {
        $this->transport->call(
            'deleteCounselSystemMessage',
            path: ['senderKey' => $senderKey, 'id' => $id],
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 시스템 메시지 검수 요청
     *
     * 시스템 메시지 검수를 요청합니다.
     *
     * 확인 필요(x-unverified): 요청에 시스템 메시지 ID가 없어, 여러 개가 등록된 경우 어떤 메시지가 검수 대상인지 문서에 없습니다.
     *
     * `POST /api/comm/v1/center/cstalk/system/message/approval/request` (operationId
     * `requestCounselSystemMessageApproval`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselSenderRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function requestApproval(
        #[\SensitiveParameter]
        Model\CounselSenderRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselSenderRequest::fromArray($body) : $body;

        $this->transport->call(
            'requestCounselSystemMessageApproval',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 시스템 메시지 검수 취소
     *
     * 시스템 메시지 검수 요청을 취소합니다.
     *
     * 확인 필요(x-unverified): 요청에 시스템 메시지 ID가 없어, 여러 개가 등록된 경우 어떤 메시지가 검수 대상인지 문서에 없습니다.
     *
     * `POST /api/comm/v1/center/cstalk/system/message/approval/cancel` (operationId
     * `cancelCounselSystemMessageApproval`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselSenderRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function cancelApproval(
        #[\SensitiveParameter]
        Model\CounselSenderRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselSenderRequest::fromArray($body) : $body;

        $this->transport->call(
            'cancelCounselSystemMessageApproval',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
