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
 * `$client->rcs->commonFormats`: list(), get().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class CommonFormats
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * RCS 공통 포맷 목록 조회
     *
     * RCS에서 사용할 수 있는 공통 포맷 목록을 조회합니다.
     *
     * 확인 필요(x-unverified): 응답에 pagination(offset/limit/total)이 있으나 요청 쪽 페이지 파라미터가 문서화되어 있지 않아 x-sdk-pagination을 두지
     * 않았습니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/common/list` (operationId `listRcsCommonFormats`). 429·5xx·네트워크 오류
     * 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @return Model\RcsMessagebaseCommonListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(): Model\RcsMessagebaseCommonListResult
    {
        $response = $this->transport->call(
            'listRcsCommonFormats',
            response: Model\RcsMessagebaseCommonListResponse::class,
        );

        return $response->data->data ?? new Model\RcsMessagebaseCommonListResult();
    }

    /**
     * RCS 공통 포맷 상세 조회
     *
     * 메시지베이스 ID를 기준으로 RCS 공통 포맷 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/common` (operationId `getRcsCommonFormat`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $messagebaseId 조회할 메시지베이스 ID입니다. (쿼리)
     *
     * @return Model\RcsMessagebaseFormDetailResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $messagebaseId,
    ): Model\RcsMessagebaseFormDetailResult {
        $query = [
            'messagebaseId' => $messagebaseId,
        ];

        $response = $this->transport->call(
            'getRcsCommonFormat',
            query: $query,
            response: Model\RcsMessagebaseFormDetailResponse::class,
        );

        return $response->data->data ?? new Model\RcsMessagebaseFormDetailResult();
    }
}
