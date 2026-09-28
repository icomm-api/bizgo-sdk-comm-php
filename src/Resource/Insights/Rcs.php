<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Insights;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->insights->rcs`: getMessage(), getMessageButton(), getPersistentMenu(), getBrandProfile().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Rcs
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * RCS 메시지 발송·노출 통계
     *
     * 브랜드·그룹 기준으로 RCS 메시지의 발송 건수와 노출(읽음) 건수를 일자별로 조회합니다.
     * 통계는 발송 요청의 `groupId` 단위로 집계되며, `groupId` 없이 보낸 건은 포함되지 않습니다.
     * 응답 데이터는 `data.data`가 아니라 `data.rcs`에 있습니다.
     *
     * `GET /api/comm/v1/center/rcs/brandId/{brandId}/stat/message` (operationId `getRcsMessageInsight`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param string $brandId RCS 브랜드 ID입니다. (경로)
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param string $groupId 그룹 ID입니다. (쿼리)
     * @param string|null $chatbotId 챗봇 ID입니다. (쿼리)
     *
     * @return Model\InsightRcsMessageStatResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getMessage(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
        #[\SensitiveParameter]
        string $groupId,
        #[\SensitiveParameter]
        ?string $chatbotId = null,
    ): Model\InsightRcsMessageStatResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
            'groupId' => $groupId,
            'chatbotId' => $chatbotId,
        ];

        $response = $this->transport->call(
            'getRcsMessageInsight',
            path: ['brandId' => $brandId],
            query: $query,
            response: Model\InsightRcsMessageStatResponse::class,
        );

        return $response->data->rcs ?? new Model\InsightRcsMessageStatResult();
    }

    /**
     * RCS 메시지 버튼 클릭 통계
     *
     * RCS 메시지에 포함된 버튼의 클릭 수를 카드·버튼 단위로 조회합니다.
     * 응답 데이터는 `data.data`가 아니라 `data.rcs`에 있습니다.
     *
     * `GET /api/comm/v1/center/rcs/brandId/{brandId}/stat/messageButton` (operationId
     * `getRcsMessageButtonInsight`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param string $brandId RCS 브랜드 ID입니다. (경로)
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param string $groupId 그룹 ID입니다. (쿼리)
     * @param string|null $chatbotId 챗봇 ID입니다. (쿼리)
     *
     * @return Model\InsightRcsMessageButtonStatResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getMessageButton(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
        #[\SensitiveParameter]
        string $groupId,
        #[\SensitiveParameter]
        ?string $chatbotId = null,
    ): Model\InsightRcsMessageButtonStatResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
            'groupId' => $groupId,
            'chatbotId' => $chatbotId,
        ];

        $response = $this->transport->call(
            'getRcsMessageButtonInsight',
            path: ['brandId' => $brandId],
            query: $query,
            response: Model\InsightRcsMessageButtonStatResponse::class,
        );

        return $response->data->rcs ?? new Model\InsightRcsMessageButtonStatResult();
    }

    /**
     * RCS 대화방 메뉴 클릭 통계
     *
     * 대화방 하단 고정 메뉴의 클릭 수를 메뉴 단위로 조회합니다. RCS 인사이트 중 이 API만 `chatbotId`가 필수입니다.
     * 응답 데이터는 `data.data`가 아니라 `data.rcs`에 있습니다.
     *
     * `GET /api/comm/v1/center/rcs/brandId/{brandId}/stat/persistentMenu` (operationId
     * `getRcsPersistentMenuInsight`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param string $brandId RCS 브랜드 ID입니다. (경로)
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param string $chatbotId 챗봇 ID입니다. (쿼리)
     *
     * @return Model\InsightRcsPersistentMenuStatResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getPersistentMenu(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
        #[\SensitiveParameter]
        string $chatbotId,
    ): Model\InsightRcsPersistentMenuStatResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
            'chatbotId' => $chatbotId,
        ];

        $response = $this->transport->call(
            'getRcsPersistentMenuInsight',
            path: ['brandId' => $brandId],
            query: $query,
            response: Model\InsightRcsPersistentMenuStatResponse::class,
        );

        return $response->data->rcs ?? new Model\InsightRcsPersistentMenuStatResult();
    }

    /**
     * RCS 브랜드 프로필 노출 통계
     *
     * 브랜드 프로필이 노출된 건수를 일자별로 조회합니다. 그룹·챗봇 조건 없이 기간만으로 조회합니다.
     * 응답 데이터는 `data.data`가 아니라 `data.rcs`에 있습니다.
     *
     * `GET /api/comm/v1/center/rcs/brandId/{brandId}/stat/brandProfile` (operationId `getRcsBrandProfileInsight`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param string $brandId RCS 브랜드 ID입니다. (경로)
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     *
     * @return Model\InsightRcsBrandProfileStatResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getBrandProfile(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
    ): Model\InsightRcsBrandProfileStatResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
        ];

        $response = $this->transport->call(
            'getRcsBrandProfileInsight',
            path: ['brandId' => $brandId],
            query: $query,
            response: Model\InsightRcsBrandProfileStatResponse::class,
        );

        return $response->data->rcs ?? new Model\InsightRcsBrandProfileStatResult();
    }
}
