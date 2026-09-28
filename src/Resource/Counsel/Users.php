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
 * `$client->counsel->users`: block(), unblock().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Users
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 사용자 수신 차단
     *
     * 특정 사용자의 상담톡 수신을 차단합니다. 차단하면 해당 사용자의 상담 세션도 종료됩니다.
     *
     * `POST /api/comm/v1/center/cstalk/profile/user/block` (operationId `blockCounselUser`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselUserBlockRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function block(
        #[\SensitiveParameter]
        Model\CounselUserBlockRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselUserBlockRequest::fromArray($body) : $body;

        $this->transport->call(
            'blockCounselUser',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 사용자 수신 차단 해제
     *
     * 차단된 사용자의 상담톡 수신 차단을 해제합니다.
     *
     * `POST /api/comm/v1/center/cstalk/profile/user/unblock` (operationId `unblockCounselUser`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselUserBlockRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function unblock(
        #[\SensitiveParameter]
        Model\CounselUserBlockRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselUserBlockRequest::fromArray($body) : $body;

        $this->transport->call(
            'unblockCounselUser',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
