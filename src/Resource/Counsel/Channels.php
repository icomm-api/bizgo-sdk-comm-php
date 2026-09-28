<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Counsel;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Model;

/**
 * `$client->counsel->channels`: activate(), deactivate(), activateChat(), deactivateChat().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Channels
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 상담톡 이용 활성화
     *
     * 발신프로필의 상담톡 이용을 활성화합니다.
     *
     * `POST /api/comm/v1/center/cstalk/sender/activate` (operationId `activateCounsel`). 429만 자동 재시도(두 번 실행되면 안 되는
     * 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselSenderActivationRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function activate(
        #[\SensitiveParameter]
        Model\CounselSenderActivationRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselSenderActivationRequest::fromArray($body) : $body;

        $this->transport->call(
            'activateCounsel',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 상담톡 이용 비활성화
     *
     * 발신프로필의 상담톡 이용을 비활성화합니다. 해지하면 저장된 상담시간이 삭제됩니다.
     *
     * `POST /api/comm/v1/center/cstalk/sender/deactivate` (operationId `deactivateCounsel`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselSenderActivationRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function deactivate(
        #[\SensitiveParameter]
        Model\CounselSenderActivationRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselSenderActivationRequest::fromArray($body) : $body;

        $this->transport->call(
            'deactivateCounsel',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 채팅 기능 활성화
     *
     * 카카오톡 채널의 채팅 기능을 활성화합니다.
     *
     * `POST /api/comm/v1/center/cstalk/sender/chat/activate` (operationId `activateCounselChat`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
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
    public function activateChat(
        #[\SensitiveParameter]
        Model\CounselSenderRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselSenderRequest::fromArray($body) : $body;

        $this->transport->call(
            'activateCounselChat',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 채팅 기능 비활성화
     *
     * 카카오톡 채널의 채팅 기능을 비활성화합니다.
     *
     * `POST /api/comm/v1/center/cstalk/sender/chat/deactivate` (operationId `deactivateCounselChat`). 429만 자동
     * 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
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
    public function deactivateChat(
        #[\SensitiveParameter]
        Model\CounselSenderRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselSenderRequest::fromArray($body) : $body;

        $this->transport->call(
            'deactivateCounselChat',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
