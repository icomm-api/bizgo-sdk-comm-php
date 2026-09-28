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
 * `$client->counsel->certs`: get().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Certs
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 카카오톡 인증 상태 조회
     *
     * 인증 트랜잭션 ID로 사용자의 카카오톡 인증(전자서명) 진행 상태를 조회합니다. 최종 결과(암호화된 인증정보)는 `counselCertResult` 웹훅으로 받습니다.
     *
     * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없는 엔드포인트입니다.
     *
     * `GET /api/comm/v1/center/cstalk/cert/status` (operationId `getCounselCertStatus`). 429·5xx·네트워크 오류 시 자동 재시도.
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string $certTxId 인증 트랜잭션 ID입니다. (쿼리)
     *
     * @return Model\CounselCertStatusResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $certTxId,
    ): Model\CounselCertStatusResult {
        $query = [
            'certTxId' => $certTxId,
        ];

        $response = $this->transport->call(
            'getCounselCertStatus',
            query: $query,
            response: Model\CounselCertStatusResponse::class,
        );

        return $response->data->data ?? new Model\CounselCertStatusResult();
    }
}
