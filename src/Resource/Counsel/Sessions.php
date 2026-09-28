<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Counsel;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->counsel->sessions`: end(), endWithBot(), get().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Sessions
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 상담 종료
     *
     * 현재 열려 있는 상담 세션을 종료합니다. 처리 결과는 발송 결과 웹훅(`counselResult`, requestType `end`)으로도 전달됩니다.
     *
     * `POST /api/comm/v1/cstalk/end` (operationId `endCounselSession`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselEndRequest|array<string, mixed> $body request body: a model, or an array with API field
     *     names (unknown fields are rejected)
     *
     * @return Model\CounselEndResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function end(
        #[\SensitiveParameter]
        Model\CounselEndRequest|array $body,
    ): Model\CounselEndResult {
        $body = is_array($body) ? Model\CounselEndRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'endCounselSession',
            json: $body,
            response: Model\CounselEndResponse::class,
        );

        return $response->data->data ?? new Model\CounselEndResult();
    }

    /**
     * 상담 종료 및 봇 전환
     *
     * 상담 세션을 종료한 뒤 봇 이벤트 말블록을 실행합니다. 처리 결과는 발송 결과 웹훅(`counselResult`, requestType `endwithbot`)으로도 전달됩니다.
     *
     * `POST /api/comm/v1/cstalk/endWithBot` (operationId `endCounselSessionWithBot`). 429만 자동 재시도(두 번 실행되면 안 되는
     * 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselEndWithBotRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @return Model\CounselEndResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function endWithBot(
        #[\SensitiveParameter]
        Model\CounselEndWithBotRequest|array $body,
    ): Model\CounselEndResult {
        $body = is_array($body) ? Model\CounselEndWithBotRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'endCounselSessionWithBot',
            json: $body,
            response: Model\CounselEndResponse::class,
        );

        return $response->data->data ?? new Model\CounselEndResult();
    }

    /**
     * 세션 조회
     *
     * 사용자 키 기준으로 현재 상담 세션 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/cstalk/session` (operationId `getCounselSession`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string $userKey 상담톡 사용자 키입니다. (쿼리, 최대 20자)
     *
     * @return Model\CounselSessionResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $userKey,
    ): Model\CounselSessionResult {
        $query = [
            'userKey' => $userKey,
        ];
        Check::all(
            Check::maxLength('userKey', $query['userKey'], 20),
            Check::minLength('userKey', $query['userKey'], 1),
        );

        $response = $this->transport->call(
            'getCounselSession',
            query: $query,
            response: Model\CounselSessionResponse::class,
        );

        return $response->data->data ?? new Model\CounselSessionResult();
    }
}
