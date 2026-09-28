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
 * `$client->brandMessage->permissions`: check(), apply().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Permissions
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 브랜드메시지 발송권한 신청 가능 여부 확인
     *
     * 발신프로필이 고객사 회원 대상 발송 권한을 신청할 수 있는 상태인지 확인합니다. `data.code`가 `A000`이면 신청 가능합니다. 고객사 회원 대상 발송(targeting M·N·O)에는
     * 카카오 발송 권한이 필요합니다. 조건: 비즈니스 인증 채널, 등록된 채널 전화번호, 채널 친구 수 5만 이상, 업로드된 광고성 정보 수신동의 증적파일, 3개월 이내 알림톡 발송 이력. 어떤
     * 조건이 미달인지는 응답으로 구분되지 않습니다.
     *
     * `GET /api/comm/v1/center/brandmessage/sendPermission` (operationId `checkBrandMessageSendPermission`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (쿼리, 최대 40자)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function check(
        #[\SensitiveParameter]
        string $senderKey,
    ): void {
        $query = [
            'senderKey' => $senderKey,
        ];
        Check::all(
            Check::maxLength('senderKey', $query['senderKey'], 40),
        );

        $this->transport->call(
            'checkBrandMessageSendPermission',
            query: $query,
            response: Model\ApiResponse::class,
        );
    }

    /**
     * 브랜드메시지 발송권한 신청
     *
     * 고객사 회원 대상 발송(targeting M·N·O) 권한을 신청합니다. 권한은 톡채널 단위로 부여되므로 같은 톡채널을 쓰는 다른 딜러사의 발신프로필에도 함께 적용됩니다. 고객사 회원 대상
     * 발송(targeting M·N·O)에는 카카오 발송 권한이 필요합니다. 조건: 비즈니스 인증 채널, 등록된 채널 전화번호, 채널 친구 수 5만 이상, 업로드된 광고성 정보 수신동의 증적파일,
     * 3개월 이내 알림톡 발송 이력. 어떤 조건이 미달인지는 응답으로 구분되지 않습니다.
     *
     * `POST /api/comm/v1/center/brandmessage/sendPermission` (operationId `applyBrandMessageSendPermission`). 429만
     * 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param Model\BrandMessagePermissionApplyRequest|array<string, mixed> $body request body: a model, or an
     *     array with API field names (unknown fields are rejected)
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function apply(
        #[\SensitiveParameter]
        Model\BrandMessagePermissionApplyRequest|array $body,
    ): void {
        $body = is_array($body) ? Model\BrandMessagePermissionApplyRequest::fromArray($body) : $body;

        $this->transport->call(
            'applyBrandMessageSendPermission',
            json: $body,
            response: Model\ApiResponse::class,
        );
    }
}
