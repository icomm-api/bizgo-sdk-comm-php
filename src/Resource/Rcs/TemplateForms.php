<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Rcs;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Pages;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->rcs->templateForms`: list(), get().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class TemplateForms
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * The whole response of {@see self::list()}, for the iterator.
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    private function listResponse(
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\RcsMessagebaseFormListResponse {
        $query = [
            'offset' => $offset,
            'limit' => $limit,
        ];
        Check::all(
            Check::range('limit', $query['limit'], 100, 1000),
        );

        return $this->transport->call(
            'listRcsTemplateForms',
            query: $query,
            response: Model\RcsMessagebaseFormListResponse::class,
        );
    }

    /**
     * RCS 템플릿 양식 목록 조회
     *
     * RCS 템플릿 등록에 사용할 수 있는 템플릿 양식 목록을 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/messagebaseform/list` (operationId `listRcsTemplateForms`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param int|null $offset 조회 시작 위치입니다. (쿼리, 서버 기본값 `0`)
     * @param int|null $limit 조회 최대 건수입니다. (쿼리, 서버 기본값 `100`, 범위 100~1000)
     *
     * @return Model\RcsMessagebaseFormListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\RcsMessagebaseFormListResult {
        $response = $this->listResponse(offset: $offset, limit: $limit);

        return $response->data->data ?? new Model\RcsMessagebaseFormListResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `offset`를 받은 항목 수만큼 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지, `total` 도달에서 멈춥니다.
     *
     * @param int|null $limit 조회 최대 건수입니다. (쿼리, 서버 기본값 `100`, 범위 100~1000)
     *
     * @return \Generator<int, Model\RcsMessagebaseFormSummary>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        ?int $limit = null,
    ): \Generator {
        $pagePosition = 0;
        $itemsSeen = 0;
        while (true) {
            $pageResponse = $this->listResponse(limit: $limit, offset: $pagePosition);
            $pageItems = $pageResponse->data->data->rcs->messageForms ?? [];
            foreach ($pageItems as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $itemCount = count($pageItems);
            $itemsSeen += $itemCount;
            if ($itemCount === 0 || $itemCount < ($limit ?? 100)) {
                return;
            }
            $pageTotal = Pages::int($pageResponse->data?->data?->rcs?->pagination?->total);
            if ($pageTotal !== null && $itemsSeen >= $pageTotal) {
                return;
            }
            $pagePosition += $itemCount;
        }
    }

    /**
     * RCS 템플릿 양식 상세 조회
     *
     * 템플릿 양식 ID를 기준으로 템플릿 양식 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/messagebaseform` (operationId `getRcsTemplateForm`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $messagebaseformId 메시지 양식 ID입니다. (쿼리)
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
        string $messagebaseformId,
    ): Model\RcsMessagebaseFormDetailResult {
        $query = [
            'messagebaseformId' => $messagebaseformId,
        ];

        $response = $this->transport->call(
            'getRcsTemplateForm',
            query: $query,
            response: Model\RcsMessagebaseFormDetailResponse::class,
        );

        return $response->data->data ?? new Model\RcsMessagebaseFormDetailResult();
    }
}
