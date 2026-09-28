<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Kakao;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->kakao->groups`: list(), addSender(), removeSender().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Groups
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 발신프로필키로 그룹 조회
     *
     * 발신프로필 키를 기준으로 연결된 발신프로필 그룹 정보를 조회합니다. `senderKey`를 생략하면 조회 가능한 그룹 정보를 기준으로 응답합니다.
     *
     * `GET /api/comm/v1/center/kakao/group` (operationId `listKakaoGroups`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param string|null $senderKey 조회 기준 발신프로필 키입니다. (쿼리)
     *
     * @return list<Model\KakaoGroup>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        ?string $senderKey = null,
    ): array {
        $query = [
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'listKakaoGroups',
            query: $query,
            response: Model\KakaoGroupListResponse::class,
        );

        return $response->data->data->kakao->groups ?? [];
    }

    /**
     * 그룹에 발신프로필 등록
     *
     * 발신프로필 그룹에 발신프로필을 등록합니다. 그룹 키와 등록할 발신프로필 키를 함께 전달합니다.
     *
     * `POST /api/comm/v1/center/kakao/group` (operationId `addKakaoGroupSender`). 429만 자동 재시도(두 번 실행되면 안 되는 요청).
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param Model\KakaoGroupSenderAddRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function addSender(
        #[\SensitiveParameter]
        Model\KakaoGroupSenderAddRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\KakaoGroupSenderAddRequest::fromArray($body) : $body;

        $this->transport->call(
            'addKakaoGroupSender',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 그룹에서 발신프로필 삭제
     *
     * 발신프로필 그룹에서 특정 발신프로필을 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/kakao/group/groupKey/{groupKey}/senderKey/{senderKey}` (operationId
     * `removeKakaoGroupSender`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param string $groupKey 그룹 키입니다. (경로)
     * @param string $senderKey 삭제할 발신프로필 키입니다. (경로)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function removeSender(
        #[\SensitiveParameter]
        string $groupKey,
        #[\SensitiveParameter]
        string $senderKey,
    ): void {
        $this->transport->call(
            'removeKakaoGroupSender',
            path: ['groupKey' => $groupKey, 'senderKey' => $senderKey],
            response: Model\ApiResponse::class,
        );
    }
}
