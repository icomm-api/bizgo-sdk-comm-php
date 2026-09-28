<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Alimtalk;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->alimtalk->publicTemplates`: list().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class PublicTemplates
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
        \DateTimeInterface|string|null $since = null,
        #[\SensitiveParameter]
        ?int $page = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): Model\AlimtalkTemplatePublicListResponse {
        $query = [
            'since' => Params::date('since', $since, 'yyyyMMddHHmmss'),
            'page' => $page,
            'count' => $count,
        ];
        Check::all(
            Check::range('page', $query['page'], 1, null),
            Check::range('count', $query['count'], 1, 1000),
        );

        return $this->transport->call(
            'listAlimtalkPublicTemplates',
            query: $query,
            response: Model\AlimtalkTemplatePublicListResponse::class,
        );
    }

    /**
     * 알림톡 공용템플릿 조회
     *
     * 카카오가 제공하는 공용템플릿 목록을 조회합니다.
     *
     * 확인 필요(x-unverified): 마지막 페이지 판별 방법(hasNext·total)이 응답에 없습니다.
     *
     * `GET /api/comm/v1/center/alimtalk/public/template` (operationId `listAlimtalkPublicTemplates`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @deprecated 스펙에서 사용 중단(deprecated)으로 표시된 API입니다.
     *
     * @param \DateTimeInterface|string|null $since 조회 기준 시각(yyyyMMddHHmmss)입니다. (쿼리, 형식
     *     `yyyyMMddHHmmss`(DateTimeInterface는 KST로 변환), 서버 기본값 `'20250708000000'`)
     * @param int|null $page 조회 페이지 번호입니다. (쿼리, 서버 기본값 `1`, 범위 1~)
     * @param int|null $count 페이지당 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~1000)
     *
     * @return list<Model\AlimtalkTemplatePublic>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        \DateTimeInterface|string|null $since = null,
        #[\SensitiveParameter]
        ?int $page = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): array {
        $response = $this->listResponse(since: $since, page: $page, count: $count);

        return $response->data->data->templates ?? [];
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `page`를 1씩 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지에서 멈춥니다.
     *
     * @param \DateTimeInterface|string|null $since 조회 기준 시각(yyyyMMddHHmmss)입니다. (쿼리, 형식
     *     `yyyyMMddHHmmss`(DateTimeInterface는 KST로 변환), 서버 기본값 `'20250708000000'`)
     * @param int|null $count 페이지당 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~1000)
     *
     * @return \Generator<int, Model\AlimtalkTemplatePublic>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        \DateTimeInterface|string|null $since = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): \Generator {
        $pagePosition = 1;
        while (true) {
            $pageResponse = $this->listResponse(since: $since, count: $count, page: $pagePosition);
            $pageItems = $pageResponse->data->data->templates ?? [];
            foreach ($pageItems as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $itemCount = count($pageItems);
            if ($itemCount === 0 || $itemCount < ($count ?? 100)) {
                return;
            }
            $pagePosition++;
        }
    }
}
