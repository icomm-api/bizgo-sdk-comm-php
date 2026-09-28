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
 * `$client->counsel->consultTime`: get(), save().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class ConsultTime
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 상담시간 조회
     *
     * 상담 운영 시간(요일별)을 조회합니다.
     *
     * `GET /api/comm/v1/center/cstalk/consult/time` (operationId `getCounselConsultTime`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다. (쿼리)
     *
     * @return Model\CounselConsultTimeResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\CounselConsultTimeResult {
        $query = [
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'getCounselConsultTime',
            query: $query,
            response: Model\CounselConsultTimeResponse::class,
        );

        return $response->data->data ?? new Model\CounselConsultTimeResult();
    }

    /**
     * 상담시간 저장
     *
     * 카카오톡 채널의 상담시간을 저장합니다. 한 번 등록하면 수정만 가능하며, 상담톡 이용을 해지하면 삭제됩니다.
     * 상담시간은 카카오톡 채널 홈에 노출되며, 확인하려면 채팅 기능이 활성화되어 있어야 합니다.
     *
     * `POST /api/comm/v1/center/cstalk/consult/time` (operationId `saveCounselConsultTime`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselConsultTimeSaveRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function save(
        #[\SensitiveParameter]
        Model\CounselConsultTimeSaveRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselConsultTimeSaveRequest::fromArray($body) : $body;

        $this->transport->call(
            'saveCounselConsultTime',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
