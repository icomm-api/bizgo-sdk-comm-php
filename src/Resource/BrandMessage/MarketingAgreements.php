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
use Bizgo\Model;
use Bizgo\Upload;

/**
 * `$client->brandMessage->marketingAgreements`: uploadEvidence().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class MarketingAgreements
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 광고성 정보 수신동의 증적자료 파일 업로드
     *
     * 광고성 정보 수신동의를 입증하는 증적자료 파일을 업로드합니다. 파일은 발신프로필 기준으로 관리되며, 발송권한 신청 조건 중 하나입니다.
     *
     * `POST /api/comm/v1/center/brandmessage/marketingAgree` (operationId
     * `uploadBrandMessageMarketingAgreeEvidence`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string $senderKey 발신프로필 키입니다. (multipart, 최대 40자)
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 광고성 정보 수신동의 증적자료 파일입니다. (multipart)
     *
     * @return Model\BrandMessageMarketingAgreeResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadEvidence(
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        mixed $file,
    ): Model\BrandMessageMarketingAgreeResult {
        Check::all(
            Check::maxLength('senderKey', $senderKey, 40),
        );
        $form = (new Form())
            ->text('senderKey', $senderKey)
            ->file('file', $file, true, null);

        $response = $this->transport->call(
            'uploadBrandMessageMarketingAgreeEvidence',
            multipart: $form->build(),
            response: Model\BrandMessageMarketingAgreeResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageMarketingAgreeResult();
    }
}
