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
 * `$client->brandMessage->videos`: registerUpload(), registerExisting(), get(), list().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Videos
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 브랜드메시지 동영상 업로드 등록
     *
     * 브랜드메시지에 쓸 동영상의 업로드를 등록하고 카카오 업로드 채널(`vid`, `uploadUrl`, `token`)을 발급받습니다. 파일은 이 API로 보내지 않고 `uploadUrl`로 직접
     * 전송합니다(5분 안에). 전송 후 동영상 조회로 `status`가 `PUBLIC`인지 확인합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/video/upload/register` (operationId
     * `registerBrandMessageVideoUpload`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageVideoUploadRegisterRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageVideoUploadRegisterResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function registerUpload(
        #[\SensitiveParameter]
        Model\BrandMessageVideoUploadRegisterRequest|array $body,
    ): Model\BrandMessageVideoUploadRegisterResult {
        $body = is_array($body) ? Model\BrandMessageVideoUploadRegisterRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'registerBrandMessageVideoUpload',
            json: $body,
            response: Model\BrandMessageVideoUploadRegisterResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageVideoUploadRegisterResult();
    }

    /**
     * 브랜드메시지 기존 동영상 등록
     *
     * 카카오톡 채널에 이미 올라가 있는 동영상을 재업로드 없이 브랜드메시지 발송용으로 등록합니다. `vid` 또는 `videoUrl` 중 하나만 지정합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/video/register` (operationId `registerBrandMessageExistingVideo`).
     * 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageVideoRegisterRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageVideoInfoResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function registerExisting(
        #[\SensitiveParameter]
        Model\BrandMessageVideoRegisterRequest|array $body,
    ): Model\BrandMessageVideoInfoResult {
        $body = is_array($body) ? Model\BrandMessageVideoRegisterRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'registerBrandMessageExistingVideo',
            json: $body,
            response: Model\BrandMessageVideoInfoResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageVideoInfoResult();
    }

    /**
     * 브랜드메시지 동영상 조회
     *
     * 동영상 식별자로 업로드된 동영상 한 건의 처리 상태와 메타 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/video` (operationId `getBrandMessageVideo`). 429·5xx·네트워크 오류 시 자동 재시도.
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $vid 조회할 동영상 식별자입니다. (쿼리)
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     *
     * @return Model\BrandMessageVideoInfoResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $vid,
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\BrandMessageVideoInfoResult {
        $query = [
            'vid' => $vid,
            'senderKey' => $senderKey,
        ];
        Check::all(
            Check::maxLength('senderKey', $query['senderKey'], 40),
        );

        $response = $this->transport->call(
            'getBrandMessageVideo',
            query: $query,
            response: Model\BrandMessageVideoInfoResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageVideoInfoResult();
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
        \DateTimeInterface|string|null $date = null,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\BrandMessageVideoListResponse {
        $query = [
            'senderKey' => $senderKey,
            'date' => Params::date('date', $date, 'yyyyMMdd'),
            'offset' => $offset,
            'limit' => $limit,
        ];
        Check::all(
            Check::maxLength('senderKey', $query['senderKey'], 40),
            Check::range('offset', $query['offset'], 0, null),
            Check::range('limit', $query['limit'], 1, 1000),
        );

        return $this->transport->call(
            'listBrandMessageVideos',
            query: $query,
            response: Model\BrandMessageVideoListResponse::class,
        );
    }

    /**
     * 브랜드메시지 동영상 목록 조회
     *
     * 발신프로필 기준으로 업로드한 동영상 이력을 조회합니다. 등록일자로 거르고 `offset`·`limit`으로 페이징합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/video/list` (operationId `listBrandMessageVideos`). 429·5xx·네트워크 오류 시
     * 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     * @param \DateTimeInterface|string|null $date 조회할 등록일자(`yyyyMMdd`)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는
     *     KST로 변환))
     * @param int|null $offset 조회 시작 위치입니다. (쿼리, 서버 기본값 `0`, 범위 0~)
     * @param int|null $limit 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~1000)
     *
     * @return Model\BrandMessageVideoListResult
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
        \DateTimeInterface|string|null $date = null,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\BrandMessageVideoListResult {
        $response = $this->listResponse(senderKey: $senderKey, date: $date, offset: $offset, limit: $limit);

        return $response->data->data ?? new Model\BrandMessageVideoListResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `offset`를 받은 항목 수만큼 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지, `total` 도달에서 멈춥니다.
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     * @param \DateTimeInterface|string|null $date 조회할 등록일자(`yyyyMMdd`)입니다. (쿼리, 형식 `yyyyMMdd`(DateTimeInterface는
     *     KST로 변환))
     * @param int|null $limit 조회 건수입니다. (쿼리, 서버 기본값 `100`, 범위 1~1000)
     *
     * @return \Generator<int, Model\BrandMessageVideoListEntry>
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
        \DateTimeInterface|string|null $date = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): \Generator {
        $pagePosition = 0;
        $itemsSeen = 0;
        while (true) {
            $pageResponse = $this->listResponse(
                senderKey: $senderKey,
                date: $date,
                limit: $limit,
                offset: $pagePosition,
            );
            $pageItems = $pageResponse->data->data->videos ?? [];
            foreach ($pageItems as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $itemCount = count($pageItems);
            $itemsSeen += $itemCount;
            if ($itemCount === 0 || $itemCount < ($limit ?? 100)) {
                return;
            }
            $pageTotal = $pageResponse->data?->data?->totalCount;
            if ($pageTotal !== null && $itemsSeen >= $pageTotal) {
                return;
            }
            $pagePosition += $itemCount;
        }
    }
}
