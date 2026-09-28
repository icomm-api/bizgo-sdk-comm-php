<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Rcs;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Form;
use Bizgo\Internal\Params;
use Bizgo\Model;
use Bizgo\Upload;

/**
 * `$client->rcs->templateImages`: get(), upload(), listFormLogos().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class TemplateImages
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * RCS 템플릿 이미지 상세 조회
     *
     * 브랜드 ID와 템플릿 파일 ID를 기준으로 템플릿 이미지 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/file` (operationId `getRcsTemplateImage`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 브랜드 ID입니다. (쿼리)
     * @param string $fileId 템플릿 파일 ID입니다. (쿼리)
     *
     * @return Model\RcsTemplateImageResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        string $fileId,
    ): Model\RcsTemplateImageResult {
        $query = [
            'brandId' => $brandId,
            'fileId' => $fileId,
        ];

        $response = $this->transport->call(
            'getRcsTemplateImage',
            query: $query,
            response: Model\RcsTemplateImageResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateImageResult();
    }

    /**
     * RCS 템플릿 이미지 등록
     *
     * RCS 템플릿 등록에 사용할 이미지를 업로드하고 템플릿 파일 ID를 발급받습니다.
     * 발송용 이미지 업로드(`/api/comm/v1/file/rcs`)와는 다른 API입니다.
     *
     * `POST /api/comm/v1/center/rcs/messagebase/file` (operationId `uploadRcsTemplateImage`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 브랜드 ID입니다. (multipart)
     * @param string|\SplFileInfo|Upload|resource $file 템플릿 등록에 사용할 이미지 파일입니다. (multipart)
     *
     * @return Model\RcsTemplateImageResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function upload(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        mixed $file,
    ): Model\RcsTemplateImageResult {
        $form = (new Form())
            ->text('brandId', $brandId)
            ->file('file', $file, true, null);

        $response = $this->transport->call(
            'uploadRcsTemplateImage',
            multipart: $form->build(),
            response: Model\RcsTemplateImageResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateImageResult();
    }

    /**
     * RCS 템플릿 양식 로고 이미지 조회
     *
     * 템플릿 양식 ID를 기준으로 템플릿 양식에서 사용할 수 있는 로고 이미지 목록을 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/messagebase/messagebaseform/logo` (operationId `listRcsTemplateFormLogos`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $messagebaseformId 메시지 양식 ID입니다. (쿼리)
     *
     * @return Model\RcsTemplateFormLogoListResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function listFormLogos(
        #[\SensitiveParameter]
        string $messagebaseformId,
    ): Model\RcsTemplateFormLogoListResult {
        $query = [
            'messagebaseformId' => $messagebaseformId,
        ];

        $response = $this->transport->call(
            'listRcsTemplateFormLogos',
            query: $query,
            response: Model\RcsTemplateFormLogoListResponse::class,
        );

        return $response->data->data ?? new Model\RcsTemplateFormLogoListResult();
    }
}
