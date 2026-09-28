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
use Bizgo\Internal\Form;
use Bizgo\Internal\Pages;
use Bizgo\Internal\Params;
use Bizgo\Model;
use Bizgo\Upload;

/**
 * `$client->brandMessage->friendGroups`: uploadFile(), create(), get(), list(), delete(), addPhoneNumbers(),
 * deletePhoneNumbers(), listPhoneNumberRequests(), getPhoneNumberRequest().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class FriendGroups
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 친구 그룹 파일 업로드
     *
     * 전화번호 목록 파일(txt, csv)을 업로드해 친구 그룹 등록 또는 전화번호 추가·삭제에 쓸 임시 파일 키를 발급받습니다.
     *
     * `POST /api/comm/v1/center/brandmessage/friendGroup/file` (operationId `uploadBrandMessageFriendGroupFile`).
     * 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (multipart)
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 전화번호 목록 파일입니다. (multipart)
     *
     * @return Model\BrandMessageFriendGroupFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadFile(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        mixed $file,
    ): Model\BrandMessageFriendGroupFileUploadResult {
        $form = (new Form())
            ->text('senderKey', $senderKey)
            ->file('file', $file, true, null);

        $response = $this->transport->call(
            'uploadBrandMessageFriendGroupFile',
            multipart: $form->build(),
            response: Model\BrandMessageFriendGroupFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupFileUploadResult();
    }

    /**
     * 친구 그룹 등록
     *
     * 친구 그룹을 생성합니다. `fileKey` 또는 `phoneNumbers`로 그룹에 넣을 전화번호를 함께 등록할 수 있습니다. 처리는 비동기이며 응답의 `status`는
     * `IN_PROGRESS`일 수 있습니다.
     *
     * `POST /api/comm/v1/center/brandmessage/friendGroup` (operationId `createBrandMessageFriendGroup`). 429만 자동
     * 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageFriendGroupRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageFriendGroupProcessingResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\BrandMessageFriendGroupRequest|array $body,
    ): Model\BrandMessageFriendGroupProcessingResult {
        $body = is_array($body) ? Model\BrandMessageFriendGroupRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createBrandMessageFriendGroup',
            json: $body,
            response: Model\BrandMessageFriendGroupProcessingResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupProcessingResult();
    }

    /**
     * 친구 그룹 조회
     *
     * 발신프로필 키와 친구 그룹 키로 친구 그룹 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/friendGroup` (operationId `getBrandMessageFriendGroup`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $friendGroupKey 친구 그룹 키입니다. (쿼리)
     *
     * @return Model\BrandMessageFriendGroupResult
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
        string $friendGroupKey,
    ): Model\BrandMessageFriendGroupResult {
        $query = [
            'senderKey' => $senderKey,
            'friendGroupKey' => $friendGroupKey,
        ];

        $response = $this->transport->call(
            'getBrandMessageFriendGroup',
            query: $query,
            response: Model\BrandMessageFriendGroupResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupResult();
    }

    /**
     * 친구 그룹 목록 조회
     *
     * 발신프로필 기준으로 등록된 친구 그룹 목록을 조회합니다. 문서에 페이징 파라미터가 없습니다.
     *
     * `GET /api/comm/v1/center/brandmessage/friendGroup/list` (operationId `listBrandMessageFriendGroups`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     *
     * @return Model\BrandMessageFriendGroupListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\BrandMessageFriendGroupListResult {
        $query = [
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'listBrandMessageFriendGroups',
            query: $query,
            response: Model\BrandMessageFriendGroupListResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupListResult();
    }

    /**
     * 친구 그룹 삭제
     *
     * 발신프로필 키와 친구 그룹 키로 친구 그룹을 삭제합니다.
     *
     * `DELETE /api/comm/v1/center/brandmessage/friendGroup/senderKey/{senderKey}/friendGroupKey/{friendGroupKey}`
     * (operationId `deleteBrandMessageFriendGroup`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (경로)
     * @param string $friendGroupKey 삭제할 친구 그룹 키입니다. (경로)
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
        string $friendGroupKey,
    ): void {
        $this->transport->call(
            'deleteBrandMessageFriendGroup',
            path: ['senderKey' => $senderKey, 'friendGroupKey' => $friendGroupKey],
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 친구 그룹 내 전화번호 추가
     *
     * 기존 친구 그룹에 전화번호를 추가합니다. `fileKey` 또는 `phoneNumbers`로 입력합니다. 처리는 비동기이며 진행 상황은 전화번호 요청 조회로 확인합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/friendGroup/phoneNumber/update` (operationId
     * `addBrandMessageFriendGroupPhoneNumbers`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageFriendGroupRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageFriendGroupProcessingResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function addPhoneNumbers(
        #[\SensitiveParameter]
        Model\BrandMessageFriendGroupRequest|array $body,
    ): Model\BrandMessageFriendGroupProcessingResult {
        $body = is_array($body) ? Model\BrandMessageFriendGroupRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'addBrandMessageFriendGroupPhoneNumbers',
            json: $body,
            response: Model\BrandMessageFriendGroupProcessingResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupProcessingResult();
    }

    /**
     * 친구 그룹 내 전화번호 삭제
     *
     * 기존 친구 그룹에서 전화번호를 삭제합니다. `fileKey` 또는 `phoneNumbers`로 입력합니다. 처리는 비동기이며 진행 상황은 전화번호 요청 조회로 확인합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/friendGroup/phoneNumber/delete` (operationId
     * `deleteBrandMessageFriendGroupPhoneNumbers`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageFriendGroupRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageFriendGroupProcessingResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function deletePhoneNumbers(
        #[\SensitiveParameter]
        Model\BrandMessageFriendGroupRequest|array $body,
    ): Model\BrandMessageFriendGroupProcessingResult {
        $body = is_array($body) ? Model\BrandMessageFriendGroupRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'deleteBrandMessageFriendGroupPhoneNumbers',
            json: $body,
            response: Model\BrandMessageFriendGroupProcessingResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupProcessingResult();
    }

    /**
     * The whole response of {@see self::listPhoneNumberRequests()}, for the iterator.
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    private function listPhoneNumberRequestsResponse(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $friendGroupKey,
        #[\SensitiveParameter]
        ?string $lastRequestId = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): Model\BrandMessageFriendGroupPhoneNumberRequestListResponse {
        $query = [
            'senderKey' => $senderKey,
            'friendGroupKey' => $friendGroupKey,
            'lastRequestId' => $lastRequestId,
            'count' => $count,
        ];
        Check::all(
            Check::range('count', $query['count'], 1, null),
        );

        return $this->transport->call(
            'listBrandMessageFriendGroupPhoneNumberRequests',
            query: $query,
            response: Model\BrandMessageFriendGroupPhoneNumberRequestListResponse::class,
        );
    }

    /**
     * 친구 그룹 전화번호 요청 목록 조회
     *
     * 친구 그룹 전화번호 추가·삭제 요청의 처리 상태를 목록으로 조회합니다. 다음 페이지는 이전 응답의 마지막 항목 `requestId`를 `lastRequestId`로 넘겨 조회합니다.
     *
     * 확인 필요(x-unverified): 이 엔드포인트는 한국어 페이지(Part A)에만 있습니다. 응답에 다음 커서 필드가 없어 마지막 항목의 `requestId`를 `lastRequestId`로
     * 쓰는 것으로 해석했습니다. `count` 기본값·최대값이 없습니다.
     *
     * `GET /api/comm/v1/center/brandmessage/friendGroup/phoneNumber/requests` (operationId
     * `listBrandMessageFriendGroupPhoneNumberRequests`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $friendGroupKey 친구 그룹 키입니다. (쿼리)
     * @param string|null $lastRequestId 이전 페이지의 마지막 요청 아이디입니다. (쿼리)
     * @param int|null $count 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return Model\BrandMessageFriendGroupPhoneNumberRequestListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function listPhoneNumberRequests(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $friendGroupKey,
        #[\SensitiveParameter]
        ?string $lastRequestId = null,
        #[\SensitiveParameter]
        ?int $count = null,
    ): Model\BrandMessageFriendGroupPhoneNumberRequestListResult {
        $response = $this->listPhoneNumberRequestsResponse(
            senderKey: $senderKey,
            friendGroupKey: $friendGroupKey,
            lastRequestId: $lastRequestId,
            count: $count,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupPhoneNumberRequestListResult();
    }

    /**
     * Iterates over every item of {@see self::listPhoneNumberRequests()}, fetching the following pages as needed.
     *
     * `lastRequestId` 커서를 따라 다음 페이지를 가져옵니다. `hasNext`가 false이거나 커서가 없거나 움직이지 않으면 멈춥니다.
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $friendGroupKey 친구 그룹 키입니다. (쿼리)
     * @param int|null $count 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return \Generator<int, Model\BrandMessageFriendGroupPhoneNumberRequest>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterListPhoneNumberRequests(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $friendGroupKey,
        #[\SensitiveParameter]
        ?int $count = null,
    ): \Generator {
        $pageCursor = null;
        while (true) {
            $pageResponse = $this->listPhoneNumberRequestsResponse(
                senderKey: $senderKey,
                friendGroupKey: $friendGroupKey,
                count: $count,
                lastRequestId: $pageCursor,
            );
            foreach ($pageResponse->data->data->friendGroups ?? [] as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $nextCursor = Pages::last($pageResponse->data?->data?->friendGroups)?->requestId;
            $hasNext = $pageResponse->data?->data?->hasNext;
            if ($hasNext !== true || $nextCursor === null || $nextCursor === $pageCursor) {
                return;
            }
            $pageCursor = $nextCursor;
        }
    }

    /**
     * 친구 그룹 전화번호 요청 단건 조회
     *
     * 전화번호 추가·삭제 응답으로 받은 `requestId`로 해당 요청의 처리 상태를 조회합니다.
     *
     * 확인 필요(x-unverified): 이 엔드포인트는 한국어 페이지(Part A)에만 있습니다.
     *
     * `GET /api/comm/v1/center/brandmessage/friendGroup/phoneNumber/request` (operationId
     * `getBrandMessageFriendGroupPhoneNumberRequest`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $friendGroupKey 친구 그룹 키입니다. (쿼리)
     * @param string $requestId 조회할 요청 아이디입니다. (쿼리)
     *
     * @return Model\BrandMessageFriendGroupPhoneNumberRequestResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getPhoneNumberRequest(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $friendGroupKey,
        #[\SensitiveParameter]
        string $requestId,
    ): Model\BrandMessageFriendGroupPhoneNumberRequestResult {
        $query = [
            'senderKey' => $senderKey,
            'friendGroupKey' => $friendGroupKey,
            'requestId' => $requestId,
        ];

        $response = $this->transport->call(
            'getBrandMessageFriendGroupPhoneNumberRequest',
            query: $query,
            response: Model\BrandMessageFriendGroupPhoneNumberRequestResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFriendGroupPhoneNumberRequestResult();
    }
}
