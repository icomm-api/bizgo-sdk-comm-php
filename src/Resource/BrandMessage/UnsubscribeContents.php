<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\BrandMessage;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Model;

/**
 * `$client->brandMessage->unsubscribeContents`: register().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class UnsubscribeContents
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 발신프로필 무료수신거부 정보 입력
     *
     * 발신프로필의 무료수신거부 전화번호와 인증번호를 등록합니다. 광고성 메시지 하단의 무료수신거부 안내에 씁니다.
     *
     * `POST /api/comm/v1/center/brandmessage/unSubscribeContent` (operationId
     * `registerBrandMessageUnsubscribeContent`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessageUnsubscribeContentRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function register(
        #[\SensitiveParameter]
        Model\BrandMessageUnsubscribeContentRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\BrandMessageUnsubscribeContentRequest::fromArray($body) : $body;

        $this->transport->call(
            'registerBrandMessageUnsubscribeContent',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
