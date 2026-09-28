<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Kakao;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Params;
use Bizgo\Model;

/**
 * `$client->kakao->senders`: requestToken(), create(), find(), list(), get(), recover().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Senders
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 카카오 채널 인증 토큰 요청
     *
     * 발신프로필 등록에 필요한 카카오톡 채널 인증 토큰을 요청합니다.
     *
     * `POST /api/comm/v1/account/kakao/sender/token` (operationId `requestKakaoSenderToken`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param Model\KakaoSenderTokenRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     *
     * @return Model\KakaoSenderAccountServiceResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function requestToken(
        #[\SensitiveParameter]
        Model\KakaoSenderTokenRequest|array $body,
    ): Model\KakaoSenderAccountServiceResult {
        $body = is_array($body) ? Model\KakaoSenderTokenRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'requestKakaoSenderToken',
            json: $body,
            response: Model\KakaoSenderAccountResponse::class,
        );

        return $response->data ?? new Model\KakaoSenderAccountServiceResult();
    }

    /**
     * 인증 토큰으로 발신프로필 등록
     *
     * 카카오톡 채널을 발신프로필로 신규 등록합니다.
     * 카카오비즈니스 파트너센터에서 비즈니스 인증을 받았고, 프로필이 activated 상태이며 운영자에 의해 차단되지 않은 채널이어야 합니다.
     * 카카오 채널 인증 토큰 요청(`POST /api/comm/v1/account/kakao/sender/token`)으로 받은 토큰과 그때 쓴 전화번호를 헤더로 보냅니다.
     *
     * `POST /api/comm/v1/account/kakao/sender` (operationId `createKakaoSender`). 429만 자동 재시도(두 번 실행되면 안 되는 요청).
     * 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param Model\KakaoSenderCreateRequest|array<string, mixed> $body request body: a model, or an array with API
     *     field names (unknown fields are rejected)
     * @param string $token 카카오 채널 인증 토큰 요청 후 카카오톡으로 받은 채널 인증 토큰 값입니다. (헤더)
     * @param string $phoneNumber 관리자 전화번호입니다. (헤더)
     *
     * @return Model\KakaoSenderProfile
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function create(
        #[\SensitiveParameter]
        Model\KakaoSenderCreateRequest|array $body,
        #[\SensitiveParameter]
        string $token,
        #[\SensitiveParameter]
        string $phoneNumber,
    ): Model\KakaoSenderProfile {
        $body = is_array($body) ? Model\KakaoSenderCreateRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'createKakaoSender',
            json: $body,
            headers: ['token' => Params::header('token', $token), 'phoneNumber' => Params::header('phoneNumber', $phoneNumber)],
            response: Model\KakaoSenderAccountResponse::class,
        );

        return $response->data->kakao->senderProfile ?? new Model\KakaoSenderProfile();
    }

    /**
     * 발신프로필 키 또는 uuid로 조회
     *
     * 발신프로필 키(`senderKey`) 또는 카카오톡 채널 uuid에 해당하는 발신프로필을 조회합니다. `senderKey`와 `uuid` 중 하나는 반드시 입력해야 합니다.
     *
     * `GET /api/comm/v1/account/kakao/sender` (operationId `findKakaoSender`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param string|null $uuid 조회할 카카오톡 채널의 uuid(`@`로 시작하는 채널 아이디)입니다. (쿼리)
     * @param string|null $senderKey 조회할 발신프로필 키입니다. (쿼리)
     *
     * @return Model\KakaoSenderProfile
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function find(
        #[\SensitiveParameter]
        ?string $uuid = null,
        #[\SensitiveParameter]
        ?string $senderKey = null,
    ): Model\KakaoSenderProfile {
        $query = [
            'uuid' => $uuid,
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'findKakaoSender',
            query: $query,
            response: Model\KakaoSenderAccountResponse::class,
        );

        return $response->data->kakao->senderProfile ?? new Model\KakaoSenderProfile();
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
        ?string $startDate = null,
        #[\SensitiveParameter]
        ?string $endDate = null,
        #[\SensitiveParameter]
        ?string $senderKey = null,
        #[\SensitiveParameter]
        ?int $page = null,
        #[\SensitiveParameter]
        ?int $rows = null,
    ): Model\KakaoSenderListResponse {
        $query = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'senderKey' => $senderKey,
            'page' => $page,
            'rows' => $rows,
        ];

        return $this->transport->call(
            'listKakaoSenderProfiles',
            query: $query,
            response: Model\KakaoSenderListResponse::class,
        );
    }

    /**
     * 발신프로필 목록 조회
     *
     * API 키에 등록된 발신프로필 목록을 조회합니다. 검색 기간과 발신프로필 키로 범위를 좁힐 수 있고, 결과는 페이지 단위로 반환합니다.
     *
     * 확인 필요(x-unverified): 이 엔드포인트는 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없습니다. 날짜 형식과 page·rows 기본값·최대값이
     * 문서에 없습니다.
     *
     * `GET /api/comm/v1/account/kakao/sender/profiles` (operationId `listKakaoSenderProfiles`). 429·5xx·네트워크 오류 시
     * 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param string|null $startDate 검색 시작일입니다. (쿼리)
     * @param string|null $endDate 검색 종료일입니다. (쿼리)
     * @param string|null $senderKey 발신프로필 키 또는 발신프로필 그룹키입니다. (쿼리)
     * @param int|null $page 페이지 번호입니다. (쿼리)
     * @param int|null $rows 페이지당 조회 건수입니다. (쿼리)
     *
     * @return Model\KakaoSenderListServiceResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        ?string $startDate = null,
        #[\SensitiveParameter]
        ?string $endDate = null,
        #[\SensitiveParameter]
        ?string $senderKey = null,
        #[\SensitiveParameter]
        ?int $page = null,
        #[\SensitiveParameter]
        ?int $rows = null,
    ): Model\KakaoSenderListServiceResult {
        $response = $this->listResponse(
            startDate: $startDate,
            endDate: $endDate,
            senderKey: $senderKey,
            page: $page,
            rows: $rows,
        );

        return $response->data ?? new Model\KakaoSenderListServiceResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `page`를 1씩 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지, `total` 도달, `hasNext` false에서 멈춥니다.
     *
     * @param string|null $startDate 검색 시작일입니다. (쿼리)
     * @param string|null $endDate 검색 종료일입니다. (쿼리)
     * @param string|null $senderKey 발신프로필 키 또는 발신프로필 그룹키입니다. (쿼리)
     * @param int|null $rows 페이지당 조회 건수입니다. (쿼리)
     *
     * @return \Generator<int, Model\KakaoSenderChannel>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        ?string $startDate = null,
        #[\SensitiveParameter]
        ?string $endDate = null,
        #[\SensitiveParameter]
        ?string $senderKey = null,
        #[\SensitiveParameter]
        ?int $rows = null,
    ): \Generator {
        $pagePosition = 1;
        $itemsSeen = 0;
        while (true) {
            $pageResponse = $this->listResponse(
                startDate: $startDate,
                endDate: $endDate,
                senderKey: $senderKey,
                rows: $rows,
                page: $pagePosition,
            );
            $pageItems = $pageResponse->data->channels ?? [];
            foreach ($pageItems as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $itemCount = count($pageItems);
            $itemsSeen += $itemCount;
            if ($itemCount === 0 || ($rows !== null && $itemCount < $rows)) {
                return;
            }
            if ($pageResponse->data?->hasNext === false) {
                return;
            }
            $pageTotal = $pageResponse->data?->totalCount;
            if ($pageTotal !== null && $itemsSeen >= $pageTotal) {
                return;
            }
            $pagePosition++;
        }
    }

    /**
     * 발신프로필 키로 조회
     *
     * 발신프로필 키를 기준으로 카카오 발신프로필 정보를 조회합니다. 알림톡, 브랜드메시지, 상담톡에서 사용하는 senderKey의 상태(차단·휴면·스팸 등)를 확인할 때 사용합니다.
     *
     * `GET /api/comm/v1/center/kakao/sender` (operationId `getKakaoSender`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     *
     * @return Model\KakaoSenderProfile
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\KakaoSenderProfile {
        $query = [
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'getKakaoSender',
            query: $query,
            response: Model\KakaoSenderResponse::class,
        );

        return $response->data->data->kakao->senderProfile ?? new Model\KakaoSenderProfile();
    }

    /**
     * 발신프로필 휴면 해제
     *
     * 휴면 상태의 발신프로필을 해제합니다. 카카오 비즈메시지 발송 전 발신프로필 상태 복구가 필요한 경우 사용합니다.
     *
     * `POST /api/comm/v1/center/kakao/sender/recover` (operationId `recoverKakaoSender`). 429만 자동 재시도(두 번 실행되면 안
     * 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param Model\KakaoSenderRecoverRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function recover(
        #[\SensitiveParameter]
        Model\KakaoSenderRecoverRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\KakaoSenderRecoverRequest::fromArray($body) : $body;

        $this->transport->call(
            'recoverKakaoSender',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
