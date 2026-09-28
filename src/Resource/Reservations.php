<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Params;
use Bizgo\Model;
use Bizgo\Resource\Reservations\Recipients;

/**
 * `$client->reservations`: create(), list(), get(), update(), cancel(), pause(), resume().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Reservations
{
    /** `$client->reservations->recipients`. */
    public readonly Recipients $recipients;

    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
        $this->recipients = new Recipients($transport);
    }

    /**
     * 예약 발송 등록
     *
     * 메시지를 지정한 시각에 발송하도록 예약 등록합니다. SMS/LMS/MMS, 국제, 통합 RCS, 알림톡, 브랜드메시지(기본형·자유형)를 예약할 수 있으며,
     * 채널별 메시지 필드는 통합 발송 규격과 같습니다. 채널별 안내는 문자·국제·RCS·알림톡·브랜드메시지 API 레퍼런스의 "예약 발송" 섹션에 있습니다.
     *
     * `POST /api/comm/v1/reservation` (operationId `createReservation`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한:
     * send 버킷(수신자 수 기준).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/mt
     *
     * @param Model\ReservationCreateRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @return Model\ReservationCreateServiceResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\ReservationCreateRequest|array $body,
    ): Model\ReservationCreateServiceResult {
        $body = is_array($body) ? Model\ReservationCreateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createReservation',
            json: $body,
            response: Model\ReservationCreateResponse::class,
        );

        return $response->data ?? new Model\ReservationCreateServiceResult();
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
        \DateTimeInterface|string $resvSendTime,
        #[\SensitiveParameter]
        ?string $paymentCode = null,
        #[\SensitiveParameter]
        ?int $lastSeq = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\ReservationListResponse {
        $query = [
            'resvSendTime' => Params::date('resvSendTime', $resvSendTime, 'yyyy-MM-dd HH:mm:ss'),
            'paymentCode' => $paymentCode,
            'lastSeq' => $lastSeq,
            'limit' => $limit,
        ];
        Check::all(
            Check::range('limit', $query['limit'], 1, null),
        );

        return $this->transport->call(
            'listReservations',
            query: $query,
            response: Model\ReservationListResponse::class,
        );
    }

    /**
     * 예약 목록 조회
     *
     * 예약 발송 건 목록을 조회합니다. `resvSendTime`에 지정한 시각을 포함해 그 이후의 예약 건이 조회됩니다.
     *
     * `GET /api/comm/v1/reservation/list` (operationId `listReservations`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other
     * 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param \DateTimeInterface|string $resvSendTime 조회 기준 예약 발송 시각입니다. (쿼리, 형식 `yyyy-MM-dd
     *     HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param string|null $paymentCode 정산 코드로 조회 대상을 거릅니다. (쿼리)
     * @param int|null $lastSeq 다음 페이지를 조회할 때 쓰는 마지막 순번입니다. (쿼리)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return Model\ReservationListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        \DateTimeInterface|string $resvSendTime,
        #[\SensitiveParameter]
        ?string $paymentCode = null,
        #[\SensitiveParameter]
        ?int $lastSeq = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\ReservationListResult {
        $response = $this->listResponse(
            resvSendTime: $resvSendTime,
            paymentCode: $paymentCode,
            lastSeq: $lastSeq,
            limit: $limit,
        );

        return $response->data->data ?? new Model\ReservationListResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `lastSeq` 커서를 따라 다음 페이지를 가져옵니다. `hasNext`가 false이거나 커서가 없거나 움직이지 않으면 멈춥니다.
     *
     * @param \DateTimeInterface|string $resvSendTime 조회 기준 예약 발송 시각입니다. (쿼리, 형식 `yyyy-MM-dd
     *     HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param string|null $paymentCode 정산 코드로 조회 대상을 거릅니다. (쿼리)
     * @param int|null $limit 조회 건수입니다. (쿼리, 범위 1~)
     *
     * @return \Generator<int, Model\Reservation>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        \DateTimeInterface|string $resvSendTime,
        #[\SensitiveParameter]
        ?string $paymentCode = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): \Generator {
        $pageCursor = null;
        while (true) {
            $pageResponse = $this->listResponse(
                resvSendTime: $resvSendTime,
                paymentCode: $paymentCode,
                limit: $limit,
                lastSeq: $pageCursor,
            );
            foreach ($pageResponse->data->data->reservations ?? [] as $pageItem) {
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
     * 예약 상세 조회
     *
     * 예약 발송 키(resvKey)로 예약 건을 단건 조회합니다.
     *
     * `GET /api/comm/v1/reservation/resvKey/{resvKey}` (operationId `getReservation`). 429·5xx·네트워크 오류 시 자동 재시도.
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     *
     * @return Model\Reservation
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $resvKey,
    ): Model\Reservation {
        $response = $this->transport->call(
            'getReservation',
            path: ['resvKey' => $resvKey],
            response: Model\ReservationResponse::class,
        );

        return $response->data->data ?? new Model\Reservation();
    }

    /**
     * 예약 수정
     *
     * 예약 대기(PENDING) 상태인 예약 건의 발송 시각과 예약명을 수정합니다. 다른 상태이면 거절됩니다(A824).
     * 수정 응답에는 `resvData`가 빠질 수 있습니다.
     *
     * `PUT /api/comm/v1/reservation/resvKey/{resvKey}` (operationId `updateReservation`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     * @param Model\ReservationUpdateRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @return Model\Reservation
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function update(
        #[\SensitiveParameter]
        string $resvKey,
        #[\SensitiveParameter]
        Model\ReservationUpdateRequest|array $body,
    ): Model\Reservation {
        $body = is_array($body) ? Model\ReservationUpdateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'updateReservation',
            path: ['resvKey' => $resvKey],
            json: $body,
            response: Model\ReservationResponse::class,
        );

        return $response->data->data ?? new Model\Reservation();
    }

    /**
     * 예약 취소
     *
     * 예약 대기(PENDING) 또는 예약 중지(STOPPED) 상태인 예약 건을 취소합니다. 성공하면 `status`가 `CANCELLED`가 됩니다.
     *
     * `POST /api/comm/v1/reservation/resvKey/{resvKey}/cancel` (operationId `cancelReservation`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     *
     * @return Model\Reservation
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function cancel(
        #[\SensitiveParameter]
        string $resvKey,
    ): Model\Reservation {
        $response = $this->transport->call(
            'cancelReservation',
            path: ['resvKey' => $resvKey],
            response: Model\ReservationResponse::class,
        );

        return $response->data->data ?? new Model\Reservation();
    }

    /**
     * 예약 중지
     *
     * 발송 처리 중(PROCESSING)인 예약 건을 중지합니다. 성공하면 `status`가 `STOPPED`가 되며, 재개(`resume`)로 다시 발송할 수 있습니다.
     *
     * `POST /api/comm/v1/reservation/resvKey/{resvKey}/stop` (operationId `stopReservation`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     *
     * @return Model\Reservation
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function pause(
        #[\SensitiveParameter]
        string $resvKey,
    ): Model\Reservation {
        $response = $this->transport->call(
            'stopReservation',
            path: ['resvKey' => $resvKey],
            response: Model\ReservationResponse::class,
        );

        return $response->data->data ?? new Model\Reservation();
    }

    /**
     * 예약 재개
     *
     * 예약 중지(STOPPED) 상태인 예약 건을 다시 발송 처리합니다.
     *
     * 확인 필요(x-unverified): 재개 응답 예시의 status가 PROCESSING이 아니라 PENDING입니다. 재개 후 상태가 무엇인지 확인이 필요합니다.
     *
     * `POST /api/comm/v1/reservation/resvKey/{resvKey}/resume` (operationId `resumeReservation`). 429만 자동 재시도(두 번
     * 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/reservation
     *
     * @param string $resvKey 예약 발송 키입니다. (경로)
     *
     * @return Model\Reservation
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function resume(
        #[\SensitiveParameter]
        string $resvKey,
    ): Model\Reservation {
        $response = $this->transport->call(
            'resumeReservation',
            path: ['resvKey' => $resvKey],
            response: Model\ReservationResponse::class,
        );

        return $response->data->data ?? new Model\Reservation();
    }
}
