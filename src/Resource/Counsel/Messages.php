<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Counsel;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Model;

/**
 * `$client->counsel->messages`: sendPlain(), sendRich(), delete().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Messages
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 상담톡 Plain 메시지 발송
     *
     * 상담톡 Plain 메시지(TEXT, IMAGE, VIDEO, AUDIO, FILE)를 발송합니다.
     *
     * `POST /api/comm/v1/cstalk/plain` (operationId `sendCounselPlain`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한:
     * send 버킷(수신자 수 기준).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselPlainMessageRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\CounselSendServiceResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function sendPlain(
        #[\SensitiveParameter]
        Model\CounselPlainMessageRequest|array $body,
    ): Model\CounselSendServiceResult {
        $body = is_array($body) ? Model\CounselPlainMessageRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'sendCounselPlain',
            json: $body,
            response: Model\CounselSendResponse::class,
        );

        return $response->data ?? new Model\CounselSendServiceResult();
    }

    /**
     * 상담톡 Rich 메시지 발송
     *
     * 상담톡 Rich 메시지(TEXT, IMAGE, WIDE, ITEM_LIST, WIDE_ITEM_LIST, CAROUSEL_FEED, PERSONAL)를 발송합니다.
     * `KAKAO_CERT`(본인인증) 발송은 채널이 본인인증 화이트리스트에 사전 등록되어 있어야 하며, 결과는 `counselCertResult` 웹훅으로 옵니다.
     *
     * `POST /api/comm/v1/cstalk/rich` (operationId `sendCounselRich`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: send
     * 버킷(수신자 수 기준).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselRichMessageRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @return Model\CounselSendServiceResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function sendRich(
        #[\SensitiveParameter]
        Model\CounselRichMessageRequest|array $body,
    ): Model\CounselSendServiceResult {
        $body = is_array($body) ? Model\CounselRichMessageRequest::fromArray($body) : $body;

        $response = $this->transport->call(
            'sendCounselRich',
            json: $body,
            response: Model\CounselSendResponse::class,
        );

        return $response->data ?? new Model\CounselSendServiceResult();
    }

    /**
     * 상담 메시지 삭제
     *
     * 이미 발송된 상담톡 메시지를 삭제합니다. 삭제한 메시지는 복구할 수 없습니다.
     * 응답 코드: `A000` 삭제 성공, `A502` 발신프로필 미등록·`senderKey`/`msgKey` 오류·소유자 불일치, `A507` `userKey` 오류, `A822` 삭제 불가 상태,
     * `A213` 그 외 처리 실패.
     *
     * 확인 필요(x-unverified): 한국어 페이지(Part A)에만 있고 Copy Markdown(Part B)에는 없는 엔드포인트입니다.
     *
     * `POST /api/comm/v1/center/cstalk/chat/delete` (operationId `deleteCounselMessage`). 429만 자동 재시도(두 번 실행되면 안
     * 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param Model\CounselMessageDeleteRequest|array<string, mixed> $body request body: a model, or an array with
     *     API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function delete(
        #[\SensitiveParameter]
        Model\CounselMessageDeleteRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\CounselMessageDeleteRequest::fromArray($body) : $body;

        $this->transport->call(
            'deleteCounselMessage',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
