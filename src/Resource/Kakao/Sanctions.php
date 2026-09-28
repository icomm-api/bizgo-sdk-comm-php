<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Kakao;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->kakao->sanctions`: getSender(), getGroupTemplateExclusion(), getTemplate().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Sanctions
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 발신프로필 제재 조회
     *
     * 특정 일자에 발신프로필에 적용된 제재 정보를 조회합니다. 발송 전 운영 상태를 확인할 때 사용합니다.
     *
     * `GET /api/comm/v1/center/kakao/abusing/block/sender` (operationId `getKakaoSenderSanction`). 429·5xx·네트워크 오류
     * 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param \DateTimeInterface|string $date 조회 기준 날짜(YYYYMMDD)입니다. (쿼리, 형식 `YYYYMMDD`(DateTimeInterface는 KST로
     *     변환))
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     *
     * @return Model\KakaoSanction
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getSender(
        #[\SensitiveParameter]
        \DateTimeInterface|string $date,
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\KakaoSanction {
        $query = [
            'date' => Params::date('date', $date, 'YYYYMMDD'),
            'senderKey' => $senderKey,
        ];
        Check::all(
            Check::pattern('date', $query['date'], '^[0-9]{8}$'),
        );

        $response = $this->transport->call(
            'getKakaoSenderSanction',
            query: $query,
            response: Model\KakaoSanctionResponse::class,
        );

        return $response->data->data->kakao->abusing ?? new Model\KakaoSanction();
    }

    /**
     * 그룹템플릿 발신프로필 제외 조회
     *
     * 그룹템플릿에서 특정 발신프로필이 제외(제한)된 제재 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/kakao/abusing/block/senderGroup` (operationId
     * `getKakaoGroupTemplateSenderExclusion`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param \DateTimeInterface|string $date 조회 기준 날짜(YYYYMMDD)입니다. (쿼리, 형식 `YYYYMMDD`(DateTimeInterface는 KST로
     *     변환))
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $groupKey 그룹 발신프로필 키입니다. (쿼리)
     * @param string $templateCode 템플릿 코드입니다. (쿼리)
     *
     * @return Model\KakaoSanction
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getGroupTemplateExclusion(
        #[\SensitiveParameter]
        \DateTimeInterface|string $date,
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $groupKey,
        #[\SensitiveParameter]
        string $templateCode,
    ): Model\KakaoSanction {
        $query = [
            'date' => Params::date('date', $date, 'YYYYMMDD'),
            'senderKey' => $senderKey,
            'groupKey' => $groupKey,
            'templateCode' => $templateCode,
        ];
        Check::all(
            Check::pattern('date', $query['date'], '^[0-9]{8}$'),
        );

        $response = $this->transport->call(
            'getKakaoGroupTemplateSenderExclusion',
            query: $query,
            response: Model\KakaoSanctionResponse::class,
        );

        return $response->data->data->kakao->abusing ?? new Model\KakaoSanction();
    }

    /**
     * 템플릿 제재 조회
     *
     * 특정 일자에 템플릿에 적용된 제재 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/kakao/abusing/block/template` (operationId `getKakaoTemplateSanction`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param \DateTimeInterface|string $date 조회 기준 날짜(YYYYMMDD)입니다. (쿼리, 형식 `YYYYMMDD`(DateTimeInterface는 KST로
     *     변환))
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $templateCode 템플릿 코드입니다. (쿼리)
     *
     * @return Model\KakaoSanction
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getTemplate(
        #[\SensitiveParameter]
        \DateTimeInterface|string $date,
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $templateCode,
    ): Model\KakaoSanction {
        $query = [
            'date' => Params::date('date', $date, 'YYYYMMDD'),
            'senderKey' => $senderKey,
            'templateCode' => $templateCode,
        ];
        Check::all(
            Check::pattern('date', $query['date'], '^[0-9]{8}$'),
        );

        $response = $this->transport->call(
            'getKakaoTemplateSanction',
            query: $query,
            response: Model\KakaoSanctionResponse::class,
        );

        return $response->data->data->kakao->abusing ?? new Model\KakaoSanction();
    }
}
