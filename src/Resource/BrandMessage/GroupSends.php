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
 * `$client->brandMessage->groupSends`: create(), get(), resume(), pause(), terminate().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class GroupSends
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 브랜드메시지 동보 발송
     *
     * 기본형 템플릿을 기준으로 친구 그룹 또는 전체 친구에게 동보 발송을 예약합니다. 템플릿은 변수가 없고 상태가 등록(`A`)이어야 합니다. 발송 수는 요청 시점의 친구 관계로 정해지고, 실제 발송
     * 시점의 친구 관계에 따라 그 수 안에서 순차 발송됩니다.
     *
     * `POST /api/comm/v1/center/brandmessage/groupMessage` (operationId `createBrandMessageGroupSend`). 429만 자동
     * 재시도(두 번 실행되면 안 되는 요청). 속도 제한: send 버킷(수신자 수 기준).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageGroupSendCreateRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageGroupSendResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\BrandMessageGroupSendCreateRequest|array $body,
    ): Model\BrandMessageGroupSendResult {
        $body = is_array($body) ? Model\BrandMessageGroupSendCreateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createBrandMessageGroupSend',
            json: $body,
            response: Model\BrandMessageGroupSendResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupSendResult();
    }

    /**
     * 브랜드메시지 동보 발송 조회
     *
     * 발신프로필 키와 요청 아이디로 특정 동보 발송 요청의 상태를 조회합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/groupMessage` (operationId `getBrandMessageGroupSend`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param int $requestId 동보 발송 요청 아이디입니다. (쿼리)
     *
     * @return Model\BrandMessageGroupSendResult
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
        int $requestId,
    ): Model\BrandMessageGroupSendResult {
        $query = [
            'senderKey' => $senderKey,
            'requestId' => $requestId,
        ];

        $response = $this->transport->call(
            'getBrandMessageGroupSend',
            query: $query,
            response: Model\BrandMessageGroupSendResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupSendResult();
    }

    /**
     * 브랜드메시지 동보 발송 재개
     *
     * 중지된 기본형 템플릿 동보 발송 요청을 다시 시작합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/groupMessage/resume` (operationId `resumeBrandMessageGroupSend`).
     * 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageGroupSendControlRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageGroupSendResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function resume(
        #[\SensitiveParameter]
        Model\BrandMessageGroupSendControlRequest|array $body,
    ): Model\BrandMessageGroupSendResult {
        $body = is_array($body) ? Model\BrandMessageGroupSendControlRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'resumeBrandMessageGroupSend',
            json: $body,
            response: Model\BrandMessageGroupSendResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupSendResult();
    }

    /**
     * 브랜드메시지 동보 발송 중지
     *
     * 진행 중인 기본형 템플릿 동보 발송 요청을 일시 중지합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/groupMessage/pause` (operationId `pauseBrandMessageGroupSend`). 429만
     * 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageGroupSendControlRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageGroupSendResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function pause(
        #[\SensitiveParameter]
        Model\BrandMessageGroupSendControlRequest|array $body,
    ): Model\BrandMessageGroupSendResult {
        $body = is_array($body) ? Model\BrandMessageGroupSendControlRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'pauseBrandMessageGroupSend',
            json: $body,
            response: Model\BrandMessageGroupSendResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupSendResult();
    }

    /**
     * 브랜드메시지 동보 발송 종료
     *
     * 예약 또는 진행 중인 기본형 템플릿 동보 발송 요청을 종료합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/groupMessage/terminate` (operationId
     * `terminateBrandMessageGroupSend`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageGroupSendControlRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageGroupSendResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function terminate(
        #[\SensitiveParameter]
        Model\BrandMessageGroupSendControlRequest|array $body,
    ): Model\BrandMessageGroupSendResult {
        $body = is_array($body) ? Model\BrandMessageGroupSendControlRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'terminateBrandMessageGroupSend',
            json: $body,
            response: Model\BrandMessageGroupSendResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageGroupSendResult();
    }
}
