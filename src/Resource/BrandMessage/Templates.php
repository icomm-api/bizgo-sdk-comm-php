<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\BrandMessage;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->brandMessage->templates`: get(), create(), update(), list(), listLastModified(), delete().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Templates
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 브랜드메시지 템플릿 조회
     *
     * 발신프로필 키와 템플릿 코드로 브랜드메시지 템플릿 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/template` (operationId `getBrandMessageTemplate`). 429·5xx·네트워크 오류 시
     * 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $templateCode 템플릿 코드입니다. (쿼리)
     * @param string|null $sendType 브랜드메시지 발송 타입입니다. (쿼리, 허용 값: `basic`, `free`)
     *
     * @return Model\BrandMessageTemplateResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $templateCode,
        #[\SensitiveParameter]
        ?string $sendType = null,
    ): Model\BrandMessageTemplateResult {
        $query = [
            'senderKey' => $senderKey,
            'templateCode' => $templateCode,
            'sendType' => $sendType,
        ];
        Check::all(
            Check::enum('sendType', $query['sendType'], ['basic', 'free']),
        );

        $response = $this->transport->call(
            'getBrandMessageTemplate',
            query: $query,
            response: Model\BrandMessageTemplateResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageTemplateResult();
    }

    /**
     * 브랜드메시지 템플릿 등록
     *
     * 브랜드메시지 템플릿을 등록합니다. 이미지가 필요한 유형은 이미지 업로드 API로 발급받은 `imgUrl`을 함께 씁니다.
     *
     * `POST /api/comm/v1/center/brandmessage/template` (operationId `createBrandMessageTemplate`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageTemplateRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageTemplateResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\BrandMessageTemplateRequest|array $body,
    ): Model\BrandMessageTemplateResult {
        $body = is_array($body) ? Model\BrandMessageTemplateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createBrandMessageTemplate',
            json: $body,
            response: Model\BrandMessageTemplateResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageTemplateResult();
    }

    /**
     * 브랜드메시지 템플릿 수정
     *
     * 등록된 브랜드메시지 템플릿을 수정합니다. 검수 상태와 카카오 정책에 따라 수정 가능한 범위가 달라질 수 있습니다.
     *
     * `PUT /api/comm/v1/center/brandmessage/template` (operationId `updateBrandMessageTemplate`). 429·5xx·네트워크 오류
     * 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageTemplateRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageTemplateResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function update(
        #[\SensitiveParameter]
        Model\BrandMessageTemplateRequest|array $body,
    ): Model\BrandMessageTemplateResult {
        $body = is_array($body) ? Model\BrandMessageTemplateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'updateBrandMessageTemplate',
            json: $body,
            response: Model\BrandMessageTemplateResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageTemplateResult();
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
        string $senderKey,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        ?string $status = null,
        #[\SensitiveParameter]
        ?string $templateType = null,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\BrandMessageTemplateListResponse {
        $query = [
            'senderKey' => $senderKey,
            'senderKeyType' => $senderKeyType,
            'status' => $status,
            'templateType' => $templateType,
            'offset' => $offset,
            'limit' => $limit,
        ];
        Check::all(
            Check::range('offset', $query['offset'], 0, null),
            Check::range('limit', $query['limit'], 1, null),
        );

        return $this->transport->call(
            'listBrandMessageTemplates',
            query: $query,
            response: Model\BrandMessageTemplateListResponse::class,
        );
    }

    /**
     * 브랜드메시지 템플릿 목록 조회
     *
     * 발신프로필(또는 그룹키)에 등록된 브랜드메시지 템플릿 목록을 페이징 조회합니다. 카카오를 호출하지 않고 비즈고에 적재된 템플릿을 읽으므로 목록 화면이나 주기적인 동기화에 쓸 수 있습니다.
     *
     * 확인 필요(x-unverified): 이 엔드포인트는 한국어 페이지(Part A)에만 있습니다. `senderKeyType`·`status`·`templateType` 값 목록과
     * `offset`·`limit` 기본값·최대값, 전체 건수 필드가 문서에 없습니다.
     *
     * `GET /api/comm/v1/center/brandmessage/template/list` (operationId `listBrandMessageTemplates`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키 또는 그룹키입니다. (쿼리)
     * @param string|null $senderKeyType 발신키 유형입니다. (쿼리)
     * @param string|null $status 템플릿 상태입니다. (쿼리)
     * @param string|null $templateType 템플릿 유형입니다. (쿼리)
     * @param int|null $offset 조회 시작 위치입니다. (쿼리, 범위 0~)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return Model\BrandMessageTemplateListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        ?string $status = null,
        #[\SensitiveParameter]
        ?string $templateType = null,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\BrandMessageTemplateListResult {
        $response = $this->listResponse(
            senderKey: $senderKey,
            senderKeyType: $senderKeyType,
            status: $status,
            templateType: $templateType,
            offset: $offset,
            limit: $limit,
        );

        return $response->data->data ?? new Model\BrandMessageTemplateListResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `offset`를 받은 항목 수만큼 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지에서 멈춥니다.
     *
     * @param string $senderKey 발신프로필 키 또는 그룹키입니다. (쿼리)
     * @param string|null $senderKeyType 발신키 유형입니다. (쿼리)
     * @param string|null $status 템플릿 상태입니다. (쿼리)
     * @param string|null $templateType 템플릿 유형입니다. (쿼리)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return \Generator<int, Model\BrandMessageTemplateSummary>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        ?string $status = null,
        #[\SensitiveParameter]
        ?string $templateType = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): \Generator {
        $pagePosition = 0;
        while (true) {
            $pageResponse = $this->listResponse(
                senderKey: $senderKey,
                senderKeyType: $senderKeyType,
                status: $status,
                templateType: $templateType,
                limit: $limit,
                offset: $pagePosition,
            );
            $pageItems = $pageResponse->data->data->brandmessage->templates ?? [];
            foreach ($pageItems as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $itemCount = count($pageItems);
            if ($itemCount === 0 || ($limit !== null && $itemCount < $limit)) {
                return;
            }
            $pagePosition += $itemCount;
        }
    }

    /**
     * The whole response of {@see self::listLastModified()}, for the iterator.
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    private function listLastModifiedResponse(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        \DateTimeInterface|string|null $since = null,
        #[\SensitiveParameter]
        ?int $page = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): Model\BrandMessageTemplateListResponse {
        $query = [
            'senderKey' => $senderKey,
            'senderKeyType' => $senderKeyType,
            'since' => Params::date('since', $since, 'yyyy-MM-dd\'T\'HH:mm:ss'),
            'page' => $page,
            'count' => $count,
        ];
        Check::all(
            Check::maxLength('senderKey', $query['senderKey'], 40),
            Check::range('page', $query['page'], 1, null),
            Check::range('count', $query['count'], 1, null),
        );

        return $this->transport->call(
            'listBrandMessageTemplatesLastModified',
            query: $query,
            response: Model\BrandMessageTemplateListResponse::class,
        );
    }

    /**
     * 최근 변경 브랜드메시지 템플릿 조회
     *
     * 지정한 시각 이후 변경된 브랜드메시지 템플릿 목록을 조회합니다. 템플릿 최신화나 내부 동기화 배치에 씁니다.
     *
     * `GET /api/comm/v1/center/brandmessage/template/lastModified` (operationId
     * `listBrandMessageTemplatesLastModified`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     * @param string|null $senderKeyType 발신키 유형입니다. (쿼리, 서버 기본값 `'S'`)
     * @param \DateTimeInterface|string|null $since 조회 시작 시각(`yyyy-MM-dd'T'HH:mm:ss`)입니다. (쿼리, 형식
     *     `yyyy-MM-dd'T'HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param int|null $page 페이지 번호입니다. (쿼리, 서버 기본값 `1`, 범위 1~)
     * @param int|null $count 페이지당 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~)
     *
     * @return Model\BrandMessageTemplateListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function listLastModified(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        \DateTimeInterface|string|null $since = null,
        #[\SensitiveParameter]
        ?int $page = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): Model\BrandMessageTemplateListResult {
        $response = $this->listLastModifiedResponse(
            senderKey: $senderKey,
            senderKeyType: $senderKeyType,
            since: $since,
            page: $page,
            count: $count,
        );

        return $response->data->data ?? new Model\BrandMessageTemplateListResult();
    }

    /**
     * Iterates over every item of {@see self::listLastModified()}, fetching the following pages as needed.
     *
     * `page`를 1씩 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지에서 멈춥니다.
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     * @param string|null $senderKeyType 발신키 유형입니다. (쿼리, 서버 기본값 `'S'`)
     * @param \DateTimeInterface|string|null $since 조회 시작 시각(`yyyy-MM-dd'T'HH:mm:ss`)입니다. (쿼리, 형식
     *     `yyyy-MM-dd'T'HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param int|null $count 페이지당 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~)
     *
     * @return \Generator<int, Model\BrandMessageTemplateSummary>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterListLastModified(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        \DateTimeInterface|string|null $since = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): \Generator {
        $pagePosition = 1;
        while (true) {
            $pageResponse = $this->listLastModifiedResponse(
                senderKey: $senderKey,
                senderKeyType: $senderKeyType,
                since: $since,
                count: $count,
                page: $pagePosition,
            );
            $pageItems = $pageResponse->data->data->brandmessage->templates ?? [];
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

    /**
     * 브랜드메시지 템플릿 삭제
     *
     * 발신프로필 키와 템플릿 코드로 브랜드메시지 템플릿을 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/brandmessage/template/senderKey/{senderKey}/templateCode/{templateCode}`
     * (operationId `deleteBrandMessageTemplate`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (경로)
     * @param string $templateCode 삭제할 템플릿 코드입니다. (경로)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function delete(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $templateCode,
    ): void {
        $this->transport->call(
            'deleteBrandMessageTemplate',
            path: ['senderKey' => $senderKey, 'templateCode' => $templateCode],
            response: Model\ApiResponse::class,
        );
    }
}
