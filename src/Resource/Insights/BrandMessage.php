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
 * `$client->insights->brandMessage`: get(), getHourly(), getByTemplate().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class BrandMessage
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 브랜드메시지 주요 인사이트 조회
     *
     * 기간·발신프로필 기준으로 브랜드메시지 발송 성공/실패·읽음·클릭 등 주요 통계를 조회합니다. 카카오가 직접 제공하는 통계입니다.
     *
     * `GET /api/comm/v1/center/statistics/brandmessage` (operationId `getBrandMessageInsight`). 429·5xx·네트워크 오류 시
     * 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param string|list<string> $senderKey 발신프로필 키입니다. (쿼리, 여러 값은 쉼표로 이어 보냄)
     * @param string|list<string>|null $templateCode 템플릿 코드입니다. (쿼리, 여러 값은 쉼표로 이어 보냄)
     *
     * @return Model\InsightKakaoStatisticsResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
        #[\SensitiveParameter]
        string|array $senderKey,
        #[\SensitiveParameter]
        string|array|null $templateCode = null,
    ): Model\InsightKakaoStatisticsResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
            'senderKey' => Params::csv('senderKey', $senderKey, 1),
            'templateCode' => Params::csv('templateCode', $templateCode, null),
        ];

        $response = $this->transport->call(
            'getBrandMessageInsight',
            query: $query,
            response: Model\InsightKakaoStatisticsResponse::class,
        );

        return $response->data->data ?? new Model\InsightKakaoStatisticsResult();
    }

    /**
     * 브랜드메시지 시간별 반응 지표
     *
     * 기간·발신프로필 기준으로 브랜드메시지 시간대별(0~23시) 반응 통계를 조회합니다.
     *
     * `GET /api/comm/v1/center/statistics/brandmessage/reaction/hourly` (operationId
     * `getBrandMessageHourlyInsight`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param string|list<string> $senderKey 발신프로필 키입니다. (쿼리, 여러 값은 쉼표로 이어 보냄)
     * @param string|list<string>|null $templateCode 템플릿 코드입니다. (쿼리, 여러 값은 쉼표로 이어 보냄)
     *
     * @return Model\InsightKakaoHourlyStatisticsResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getHourly(
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
        #[\SensitiveParameter]
        string|array $senderKey,
        #[\SensitiveParameter]
        string|array|null $templateCode = null,
    ): Model\InsightKakaoHourlyStatisticsResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
            'senderKey' => Params::csv('senderKey', $senderKey, 1),
            'templateCode' => Params::csv('templateCode', $templateCode, null),
        ];

        $response = $this->transport->call(
            'getBrandMessageHourlyInsight',
            query: $query,
            response: Model\InsightKakaoHourlyStatisticsResponse::class,
        );

        return $response->data->data ?? new Model\InsightKakaoHourlyStatisticsResult();
    }

    /**
     * 브랜드메시지 템플릿 상세 조회
     *
     * 기간·발신프로필 기준으로 브랜드메시지 템플릿별 통계를 조회합니다.
     *
     * `GET /api/comm/v1/center/statistics/brandmessage/template` (operationId `getBrandMessageTemplateInsight`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/insight
     *
     * @param \DateTimeInterface|string $startDate 조회 시작일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param \DateTimeInterface|string $endDate 조회 종료일(YYYYMMDD)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는 KST로
     *     변환))
     * @param string|list<string> $senderKey 발신프로필 키입니다. (쿼리, 여러 값은 쉼표로 이어 보냄)
     * @param string|list<string>|null $templateCode 템플릿 코드입니다. (쿼리, 여러 값은 쉼표로 이어 보냄)
     *
     * @return Model\InsightKakaoTemplateStatisticsResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getByTemplate(
        #[\SensitiveParameter]
        \DateTimeInterface|string $startDate,
        #[\SensitiveParameter]
        \DateTimeInterface|string $endDate,
        #[\SensitiveParameter]
        string|array $senderKey,
        #[\SensitiveParameter]
        string|array|null $templateCode = null,
    ): Model\InsightKakaoTemplateStatisticsResult {
        $query = [
            'startDate' => Params::date('startDate', $startDate, 'yyyyMMdd'),
            'endDate' => Params::date('endDate', $endDate, 'yyyyMMdd'),
            'senderKey' => Params::csv('senderKey', $senderKey, 1),
            'templateCode' => Params::csv('templateCode', $templateCode, null),
        ];

        $response = $this->transport->call(
            'getBrandMessageTemplateInsight',
            query: $query,
            response: Model\InsightKakaoTemplateStatisticsResponse::class,
        );

        return $response->data->data ?? new Model\InsightKakaoTemplateStatisticsResult();
    }
}
