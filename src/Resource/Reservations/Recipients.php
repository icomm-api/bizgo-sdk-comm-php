<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Reservations;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->reservations->recipients`: create(), list(), delete().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Recipients
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 예약 수신자 추가
     *
     * 기존 예약 건에 수신자를 추가합니다. 한 번에 최대 1,000건까지 추가할 수 있습니다. 수신자별 `code`를 함께 확인합니다.
     *
     * 확인 필요(x-unverified): 수신자를 추가할 수 있는 예약 상태(PENDING만인지)와 예약 1건의 전체 수신자 상한이 문서에 없습니다.
     *
     * `POST /api/comm/v1/reservation/resvKey/{resvKey}/destinations` (operationId `addReservationRecipients`).
     * 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: send 버킷(수신자 수 기준).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     * @param Model\ReservationRecipientCreateRequest|array<string, mixed> $body request body: a model, or an array
     *     with API field names (unknown fields are rejected)
     *
     * @return Model\SendOmniResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        string $resvKey,
        #[\SensitiveParameter]
        Model\ReservationRecipientCreateRequest|array $body,
    ): Model\SendOmniResult {
        $body = is_array($body) ? Model\ReservationRecipientCreateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'addReservationRecipients',
            path: ['resvKey' => $resvKey],
            json: $body,
            response: Model\ReservationRecipientCreateResponse::class,
        );

        return $response->data->data ?? new Model\SendOmniResult();
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
        string $resvKey,
        #[\SensitiveParameter]
        ?int $lastSeq = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\ReservationRecipientListResponse {
        $query = [
            'lastSeq' => $lastSeq,
            'limit' => $limit,
        ];
        Check::all(
            Check::range('limit', $query['limit'], 1, null),
        );

        return $this->transport->call(
            'listReservationRecipients',
            path: ['resvKey' => $resvKey],
            query: $query,
            response: Model\ReservationRecipientListResponse::class,
        );
    }

    /**
     * 예약 수신자 목록 조회
     *
     * 예약 건에 등록된 수신자 목록을 조회합니다.
     *
     * `GET /api/comm/v1/reservation/resvKey/{resvKey}/destinations` (operationId `listReservationRecipients`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     * @param int|null $lastSeq 다음 페이지를 조회할 때 쓰는 마지막 순번입니다. (쿼리)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return Model\ReservationRecipientListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        string $resvKey,
        #[\SensitiveParameter]
        ?int $lastSeq = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\ReservationRecipientListResult {
        $response = $this->listResponse(resvKey: $resvKey, lastSeq: $lastSeq, limit: $limit);

        return $response->data->data ?? new Model\ReservationRecipientListResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `lastSeq` 커서를 따라 다음 페이지를 가져옵니다. `hasNext`가 false이거나 커서가 없거나 움직이지 않으면 멈춥니다.
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return \Generator<int, Model\ReservationRecipient>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        string $resvKey,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): \Generator {
        $pageCursor = null;
        while (true) {
            $pageResponse = $this->listResponse(resvKey: $resvKey, limit: $limit, lastSeq: $pageCursor);
            foreach ($pageResponse->data->data->destinations ?? [] as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $nextCursor = $pageResponse->data?->data?->lastSeq;
            $hasNext = $pageResponse->data?->data?->hasNext;
            if ($hasNext !== true || $nextCursor === null || $nextCursor === $pageCursor) {
                return;
            }
            $pageCursor = $nextCursor;
        }
    }

    /**
     * 예약 수신자 삭제
     *
     * 예약 건에 등록된 수신자를 메시지 키(msgKey) 기준으로 삭제합니다.
     *
     * 확인 필요(x-unverified): 이미 삭제한 수신자를 다시 삭제할 때의 응답 코드가 문서에 없습니다(재시도 시 오류가 날 수 있음).
     *
     * `DELETE /api/comm/v1/reservation/resvKey/{resvKey}/destinations/msgKey/{msgKey}` (operationId
     * `deleteReservationRecipient`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     * @param string $msgKey 조회할 메시지 키입니다. (경로)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function delete(
        #[\SensitiveParameter]
        string $resvKey,
        #[\SensitiveParameter]
        string $msgKey,
    ): void {
        $this->transport->call(
            'deleteReservationRecipient',
            path: ['resvKey' => $resvKey, 'msgKey' => $msgKey],
            response: Model\ApiResponse::class,
        );
    }
}
