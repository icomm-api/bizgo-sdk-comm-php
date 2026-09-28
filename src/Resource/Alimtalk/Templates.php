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
use Bizgo\Internal\Form;
use Bizgo\Internal\Params;
use Bizgo\Model;
use Bizgo\Upload;

/**
 * `$client->alimtalk->templates`: get(), create(), update(), list(), listModified(), delete(),
 * requestInspection(), requestInspectionWithFiles(), cancelInspection().
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
     * 알림톡 템플릿 조회
     *
     * 발신프로필 키와 템플릿 코드를 기준으로 알림톡 템플릿 상세 정보(검수 상태, 심사 의견 포함)를 조회합니다.
     *
     * `GET /api/comm/v1/center/alimtalk/template` (operationId `getAlimtalkTemplate`). 429·5xx·네트워크 오류 시 자동 재시도.
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $templateCode 템플릿 코드입니다. (쿼리)
     *
     * @return Model\AlimtalkTemplate
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
    ): Model\AlimtalkTemplate {
        $query = [
            'senderKey' => $senderKey,
            'templateCode' => $templateCode,
        ];

        $response = $this->transport->call(
            'getAlimtalkTemplate',
            query: $query,
            response: Model\AlimtalkTemplateResponse::class,
        );

        return $response->data->data->alimtalk ?? new Model\AlimtalkTemplate();
    }

    /**
     * 알림톡 템플릿 등록
     *
     * 알림톡 템플릿을 등록합니다. 등록한 템플릿은 검수 요청(`POST /api/comm/v1/center/alimtalk/template/request`) 후 카카오 승인을 받아야 발송할 수
     * 있습니다.
     * 이미지형·아이템리스트형 템플릿은 템플릿 이미지 업로드 API로 받은 `imgUrl`을 함께 넣습니다.
     *
     * `POST /api/comm/v1/center/alimtalk/template` (operationId `createAlimtalkTemplate`). 429만 자동 재시도(두 번 실행되면 안
     * 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param Model\AlimtalkTemplateSaveRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\AlimtalkTemplate
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\AlimtalkTemplateSaveRequest|array $body,
    ): Model\AlimtalkTemplate {
        $body = is_array($body) ? Model\AlimtalkTemplateSaveRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createAlimtalkTemplate',
            json: $body,
            response: Model\AlimtalkTemplateResponse::class,
        );

        return $response->data->data->alimtalk ?? new Model\AlimtalkTemplate();
    }

    /**
     * 알림톡 템플릿 수정
     *
     * 등록된 알림톡 템플릿 정보를 수정합니다. 검수 상태와 카카오 정책에 따라 수정 가능한 범위가 달라질 수 있습니다. 대상 템플릿은 본문의 `senderKey`와 `templateCode`로
     * 지정합니다.
     *
     * `PUT /api/comm/v1/center/alimtalk/template` (operationId `updateAlimtalkTemplate`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param Model\AlimtalkTemplateSaveRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\AlimtalkTemplate
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function update(
        #[\SensitiveParameter]
        Model\AlimtalkTemplateSaveRequest|array $body,
    ): Model\AlimtalkTemplate {
        $body = is_array($body) ? Model\AlimtalkTemplateSaveRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'updateAlimtalkTemplate',
            json: $body,
            response: Model\AlimtalkTemplateResponse::class,
        );

        return $response->data->data->alimtalk ?? new Model\AlimtalkTemplate();
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
        ?string $inspectionStatus = null,
        #[\SensitiveParameter]
        ?string $templateType = null,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\AlimtalkTemplateListResponse {
        $query = [
            'senderKey' => $senderKey,
            'senderKeyType' => $senderKeyType,
            'status' => $status,
            'inspectionStatus' => $inspectionStatus,
            'templateType' => $templateType,
            'offset' => $offset,
            'limit' => $limit,
        ];
        Check::all(
            Check::enum('senderKeyType', $query['senderKeyType'], ['G', 'S']),
            Check::enum('inspectionStatus', $query['inspectionStatus'], ['REG', 'REQ', 'APR', 'REJ']),
            Check::range('offset', $query['offset'], 0, null),
            Check::range('limit', $query['limit'], 1, null),
        );

        return $this->transport->call(
            'listAlimtalkTemplates',
            query: $query,
            response: Model\AlimtalkTemplateListResponse::class,
        );
    }

    /**
     * 알림톡 템플릿 목록 조회
     *
     * 발신프로필에 등록된 알림톡 템플릿 목록을 페이징 조회합니다. 카카오를 호출하지 않고 비즈고에 적재된 템플릿을 읽으므로 목록 화면이나 주기적인 동기화에 사용할 수 있습니다.
     *
     * 확인 필요(x-unverified): 이 엔드포인트는 한국어 페이지(Part A)에만 있습니다. offset이 건수 기준인지, limit 기본값·최대값, 마지막 페이지 판별
     * 방법(hasNext·total 없음)이 문서에 없습니다.
     *
     * `GET /api/comm/v1/center/alimtalk/template/list` (operationId `listAlimtalkTemplates`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string $senderKey 발신프로필 키 또는 그룹키입니다. (쿼리)
     * @param string|null $senderKeyType 발신키 유형입니다(`S` 발신프로필, `G` 그룹). (쿼리, 허용 값: `G`, `S`)
     * @param string|null $status 템플릿 상태입니다. (쿼리)
     * @param string|null $inspectionStatus 검수 상태입니다(`REG` 등록, `REQ` 검수 요청, `APR` 승인, `REJ` 반려). (쿼리, 허용 값: `REG`,
     *     `REQ`, `APR`, `REJ`)
     * @param string|null $templateType 템플릿 유형입니다. (쿼리)
     * @param int|null $offset 조회 시작 위치입니다. (쿼리, 범위 0~)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return list<Model\AlimtalkTemplate>
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
        ?string $inspectionStatus = null,
        #[\SensitiveParameter]
        ?string $templateType = null,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): array {
        $response = $this->listResponse(
            senderKey: $senderKey,
            senderKeyType: $senderKeyType,
            status: $status,
            inspectionStatus: $inspectionStatus,
            templateType: $templateType,
            offset: $offset,
            limit: $limit,
        );

        return $response->data->data->alimtalk->templates ?? [];
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `offset`를 받은 항목 수만큼 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지에서 멈춥니다.
     *
     * @param string $senderKey 발신프로필 키 또는 그룹키입니다. (쿼리)
     * @param string|null $senderKeyType 발신키 유형입니다(`S` 발신프로필, `G` 그룹). (쿼리, 허용 값: `G`, `S`)
     * @param string|null $status 템플릿 상태입니다. (쿼리)
     * @param string|null $inspectionStatus 검수 상태입니다(`REG` 등록, `REQ` 검수 요청, `APR` 승인, `REJ` 반려). (쿼리, 허용 값: `REG`,
     *     `REQ`, `APR`, `REJ`)
     * @param string|null $templateType 템플릿 유형입니다. (쿼리)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return \Generator<int, Model\AlimtalkTemplate>
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
        ?string $inspectionStatus = null,
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
                inspectionStatus: $inspectionStatus,
                templateType: $templateType,
                limit: $limit,
                offset: $pagePosition,
            );
            $pageItems = $pageResponse->data->data->alimtalk->templates ?? [];
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
     * The whole response of {@see self::listModified()}, for the iterator.
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    private function listModifiedResponse(
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
    ): Model\AlimtalkTemplateListResponse {
        $query = [
            'senderKey' => $senderKey,
            'senderKeyType' => $senderKeyType,
            'since' => Params::date('since', $since, 'yyyy-MM-dd\'T\'HH:mm:ss'),
            'page' => $page,
            'count' => $count,
        ];
        Check::all(
            Check::maxLength('senderKey', $query['senderKey'], 40),
            Check::enum('senderKeyType', $query['senderKeyType'], ['G', 'S']),
            Check::range('page', $query['page'], 1, null),
            Check::range('count', $query['count'], 1, null),
        );

        return $this->transport->call(
            'listModifiedAlimtalkTemplates',
            query: $query,
            response: Model\AlimtalkTemplateListResponse::class,
        );
    }

    /**
     * 최근 변경 알림톡 템플릿 조회
     *
     * 지정한 시각 이후 변경된 알림톡 템플릿 목록을 조회합니다. 템플릿 최신화나 내부 동기화 배치 기준으로 사용할 수 있습니다.
     *
     * 확인 필요(x-unverified): 마지막 페이지 판별 방법(hasNext·total)이 응답에 없습니다. count 최대값이 문서에 없습니다.
     *
     * `GET /api/comm/v1/center/alimtalk/template/lastModified` (operationId `listModifiedAlimtalkTemplates`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     * @param string|null $senderKeyType 발신키 유형입니다(`S` 발신프로필, `G` 그룹). (쿼리, 허용 값: `G`, `S`, 서버 기본값 `'S'`)
     * @param \DateTimeInterface|string|null $since 조회 시작 시각(yyyy-MM-dd'T'HH:mm:ss)입니다. (쿼리, 형식
     *     `yyyy-MM-dd'T'HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param int|null $page 페이지 번호입니다. (쿼리, 서버 기본값 `1`, 범위 1~)
     * @param int|null $count 페이지당 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~)
     *
     * @return list<Model\AlimtalkTemplate>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function listModified(
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
    ): array {
        $response = $this->listModifiedResponse(
            senderKey: $senderKey,
            senderKeyType: $senderKeyType,
            since: $since,
            page: $page,
            count: $count,
        );

        return $response->data->data->alimtalk->templates ?? [];
    }

    /**
     * Iterates over every item of {@see self::listModified()}, fetching the following pages as needed.
     *
     * `page`를 1씩 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지에서 멈춥니다.
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     * @param string|null $senderKeyType 발신키 유형입니다(`S` 발신프로필, `G` 그룹). (쿼리, 허용 값: `G`, `S`, 서버 기본값 `'S'`)
     * @param \DateTimeInterface|string|null $since 조회 시작 시각(yyyy-MM-dd'T'HH:mm:ss)입니다. (쿼리, 형식
     *     `yyyy-MM-dd'T'HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param int|null $count 페이지당 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~)
     *
     * @return \Generator<int, Model\AlimtalkTemplate>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterListModified(
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
            $pageResponse = $this->listModifiedResponse(
                senderKey: $senderKey,
                senderKeyType: $senderKeyType,
                since: $since,
                count: $count,
                page: $pagePosition,
            );
            $pageItems = $pageResponse->data->data->alimtalk->templates ?? [];
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
     * 알림톡 템플릿 삭제
     *
     * 발신프로필 키와 템플릿 코드를 기준으로 알림톡 템플릿을 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/alimtalk/template/senderKey/{senderKey}/templateCode/{templateCode}`
     * (operationId `deleteAlimtalkTemplate`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
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
            'deleteAlimtalkTemplate',
            path: ['senderKey' => $senderKey, 'templateCode' => $templateCode],
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 알림톡 템플릿 검수 요청
     *
     * 등록된 알림톡 템플릿을 카카오 검수로 요청합니다. 템플릿 상태가 대기이고 검수 상태가 등록(`REG`)인 경우 요청할 수 있습니다.
     * - `application/json`: 첨부 없이 요청합니다. 템플릿 정보는 `alimtalk` 객체에 넣습니다.
     * - `multipart/form-data`: 파일을 첨부해 요청합니다. 필드를 최상위에 두며 `comment`가 필수입니다. 파일 형식은 png, jpg, jpeg, gif, pdf, hwp,
     *   doc, docx입니다.
     *
     * `POST /api/comm/v1/center/alimtalk/template/request` (operationId `requestAlimtalkTemplateInspection`). 429만
     * 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param Model\AlimtalkTemplateInspectionRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function requestInspection(
        #[\SensitiveParameter]
        Model\AlimtalkTemplateInspectionRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\AlimtalkTemplateInspectionRequest::fromArray($body) : $body;

        $this->transport->call(
            'requestAlimtalkTemplateInspection',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 알림톡 템플릿 검수 요청
     *
     * 등록된 알림톡 템플릿을 카카오 검수로 요청합니다. 템플릿 상태가 대기이고 검수 상태가 등록(`REG`)인 경우 요청할 수 있습니다.
     * - `application/json`: 첨부 없이 요청합니다. 템플릿 정보는 `alimtalk` 객체에 넣습니다.
     * - `multipart/form-data`: 파일을 첨부해 요청합니다. 필드를 최상위에 두며 `comment`가 필수입니다. 파일 형식은 png, jpg, jpeg, gif, pdf, hwp,
     *   doc, docx입니다.
     *
     * `POST /api/comm/v1/center/alimtalk/template/request` (operationId `requestAlimtalkTemplateInspection`). 429만
     * 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string $senderKey 발신프로필 키입니다. (multipart)
     * @param string $templateCode 템플릿 코드입니다. (multipart)
     * @param string $comment 검수 의견 또는 문의 사항입니다. (multipart)
     * @param string|null $senderKeyType 발신프로필 키 타입입니다. (multipart, 허용 값: `G`, `S`, 서버 기본값 `'S'`)
     * @param list<string|\SplFileInfo|Upload|resource>|null $attachment 검수 요청에 첨부할 파일입니다. (multipart)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function requestInspectionWithFiles(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $templateCode,
        #[\SensitiveParameter]
        string $comment,
        #[\SensitiveParameter]
        ?string $senderKeyType = null,
        #[\SensitiveParameter]
        ?array $attachment = null,
    ): void {
        Check::all(
            Check::enum('senderKeyType', $senderKeyType, ['G', 'S']),
        );
        $form = (new Form())
            ->text('senderKey', $senderKey)
            ->text('templateCode', $templateCode)
            ->text('comment', $comment)
            ->text('senderKeyType', $senderKeyType)
            ->files('attachment', $attachment, 10);

        $this->transport->call(
            'requestAlimtalkTemplateInspection',
            multipart: $form->build(),
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 알림톡 템플릿 검수 요청 취소
     *
     * 검수 요청된 알림톡 템플릿의 검수 요청을 취소합니다.
     *
     * `POST /api/comm/v1/center/alimtalk/template/request/cancel` (operationId
     * `cancelAlimtalkTemplateInspection`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param Model\AlimtalkTemplateInspectionCancelRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function cancelInspection(
        #[\SensitiveParameter]
        Model\AlimtalkTemplateInspectionCancelRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\AlimtalkTemplateInspectionCancelRequest::fromArray($body) : $body;

        $this->transport->call(
            'cancelAlimtalkTemplateInspection',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
