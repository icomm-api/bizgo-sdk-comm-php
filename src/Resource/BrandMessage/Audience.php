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
 * `$client->brandMessage->audience`: checkPossible(), checkPossibleByPhoneNumbers(), getFriendCount(),
 * estimate().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Audience
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 발송 예상 모수 확인 (친구 그룹 기반)
     *
     * 메시지 타입과 친구 그룹 조건으로 동보 발송 예상 발송 수를 조회합니다. 친구 그룹을 쓰면 그룹 상태가 완료이고 등록 유저 수가 10 이상이어야 발송 예약이 가능하며, 친구 관계는 실시간으로
     * 동기화됩니다.
     *
     * `GET /api/comm/v1/center/brandmessage/groupMessage/possible` (operationId
     * `checkBrandMessageAudiencePossible`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param string $msgType 브랜드메시지 타입입니다(카카오 원본 chatBubbleType으로 변환됩니다). (쿼리)
     * @param string|null $friendGroupKey 대상 친구 그룹 키입니다. (쿼리)
     *
     * @return Model\BrandMessageAudiencePossibleResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function checkPossible(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        string $msgType,
        #[\SensitiveParameter]
        ?string $friendGroupKey = null,
    ): Model\BrandMessageAudiencePossibleResult {
        $query = [
            'senderKey' => $senderKey,
            'msgType' => $msgType,
            'friendGroupKey' => $friendGroupKey,
        ];

        $response = $this->transport->call(
            'checkBrandMessageAudiencePossible',
            query: $query,
            response: Model\BrandMessageAudiencePossibleResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageAudiencePossibleResult();
    }

    /**
     * 발송 예상 모수 확인 (전화번호 명단 기반)
     *
     * 전화번호 명단으로 동보 발송 가능 예상 수를 조회합니다. 국가코드는 자동 정규화되고 유효한 번호로 모수를 계산합니다. 최소 10건이며, 하나라도 형식이 잘못되면 요청 전체가 거부됩니다. 상태를
     * 바꾸지 않는 조회이므로 재시도해도 안전합니다.
     *
     * `POST /api/comm/v1/center/brandmessage/groupMessage/friend/possible` (operationId
     * `checkBrandMessageAudiencePossibleByPhoneNumbers`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageAudiencePossibleByPhoneNumbersRequest|array<string, mixed> $body request body: a
     *     model, or an array with API field names (unknown fields are rejected)
     *
     * @return Model\BrandMessageAudiencePossibleResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function checkPossibleByPhoneNumbers(
        #[\SensitiveParameter]
        Model\BrandMessageAudiencePossibleByPhoneNumbersRequest|array $body,
    ): Model\BrandMessageAudiencePossibleResult {
        $body = is_array($body) ? Model\BrandMessageAudiencePossibleByPhoneNumbersRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'checkBrandMessageAudiencePossibleByPhoneNumbers',
            json: $body,
            response: Model\BrandMessageAudiencePossibleResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageAudiencePossibleResult();
    }

    /**
     * 채널 전체 친구 수 조회
     *
     * 발신프로필 기준 친구 수를 조회합니다. 동보 발송 가능 대상 수를 미리 확인할 때 씁니다.
     *
     * `GET /api/comm/v1/center/brandmessage/groupMessage/friendCount` (operationId `getBrandMessageFriendCount`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     *
     * @return Model\BrandMessageAudienceFriendCountResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getFriendCount(
        #[\SensitiveParameter]
        string $senderKey,
    ): Model\BrandMessageAudienceFriendCountResult {
        $query = [
            'senderKey' => $senderKey,
        ];

        $response = $this->transport->call(
            'getBrandMessageFriendCount',
            query: $query,
            response: Model\BrandMessageAudienceFriendCountResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageAudienceFriendCountResult();
    }

    /**
     * 동보 발송 예상 소요시간 조회
     *
     * 동보 발송 시작 시각과 대상 수로 예상 종료 시각과 소요 시간을 조회합니다.
     *
     * `GET /api/comm/v1/center/brandmessage/groupMessage/estimate` (operationId
     * `estimateBrandMessageGroupSendDuration`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리)
     * @param \DateTimeInterface|string $startTime 동보 발송 시작 일시(`yyyy-MM-dd'T'HH:mm:ss`, KST)입니다. (쿼리, 형식
     *     `yyyy-MM-dd'T'HH:mm:ss`(DateTimeInterface는 KST로 변환))
     * @param int $count 발송 모수입니다. (쿼리)
     * @param string|null $target 대상 유형입니다. (쿼리, 허용 값: `NONE`, `FRIEND_GROUP`, 서버 기본값 `'NONE'`)
     *
     * @return Model\BrandMessageAudienceEstimateResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function estimate(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        \DateTimeInterface|string $startTime,
        #[\SensitiveParameter]
        int $count,
        #[\SensitiveParameter]
        ?string $target = null,
    ): Model\BrandMessageAudienceEstimateResult {
        $query = [
            'senderKey' => $senderKey,
            'startTime' => Params::date('startTime', $startTime, 'yyyy-MM-dd\'T\'HH:mm:ss'),
            'count' => $count,
            'target' => $target,
        ];
        Check::all(
            Check::enum('target', $query['target'], ['NONE', 'FRIEND_GROUP']),
        );

        $response = $this->transport->call(
            'estimateBrandMessageGroupSendDuration',
            query: $query,
            response: Model\BrandMessageAudienceEstimateResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageAudienceEstimateResult();
    }
}
