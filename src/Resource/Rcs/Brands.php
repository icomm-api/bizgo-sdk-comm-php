<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Rcs;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Check;
use Bizgo\Internal\Form;
use Bizgo\Internal\Params;
use Bizgo\Model;
use Bizgo\Upload;

/**
 * `$client->rcs->brands`: get(), update().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Brands
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * RCS 브랜드 상세 조회
     *
     * brandId 기준으로 RCS 브랜드 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/rcs/brand` (operationId `getRcsBrand`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other
     * 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 조회할 브랜드 ID입니다. (쿼리)
     *
     * @return Model\RcsBrandResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $brandId,
    ): Model\RcsBrandResult {
        $query = [
            'brandId' => $brandId,
        ];

        $response = $this->transport->call(
            'getRcsBrand',
            query: $query,
            response: Model\RcsBrandResponse::class,
        );

        return $response->data->data ?? new Model\RcsBrandResult();
    }

    /**
     * RCS 브랜드 수정
     *
     * 브랜드 기본 정보와 브랜드 이미지를 수정합니다. `multipart/form-data`로 보내며,
     * `regBrand`는 JSON 문자열 파트, 이미지·증빙 파일은 바이너리 파트입니다.
     * 파일 스트림을 다시 보내야 하고 검수 요청이 다시 걸릴 수 있으므로 SDK는 429만 재시도합니다.
     *
     * `PUT /api/comm/v1/center/rcs/brand` (operationId `updateRcsBrand`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 수정할 브랜드 ID입니다. (multipart)
     * @param Model\RcsBrand|array<string, mixed> $regBrand 브랜드 수정 정보입니다. (multipart)
     * @param string|\SplFileInfo|Upload|resource|null $brandProfile 브랜드 프로필 이미지 파일입니다. (multipart)
     * @param string|\SplFileInfo|Upload|resource|null $brandBackground 브랜드 배경 이미지 파일입니다. (multipart)
     * @param string|\SplFileInfo|Upload|resource|null $seasonDocFile 시즌성 증빙 파일입니다. (multipart)
     *
     * @return Model\RcsBrandResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function update(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        Model\RcsBrand|array $regBrand,
        #[\SensitiveParameter]
        mixed $brandProfile = null,
        #[\SensitiveParameter]
        mixed $brandBackground = null,
        #[\SensitiveParameter]
        mixed $seasonDocFile = null,
    ): Model\RcsBrandResult {
        Check::all(
            Params::requiredFields('regBrand', $regBrand, ['brandId', 'name']),
        );
        $form = (new Form())
            ->text('brandId', $brandId)
            ->json('regBrand', (is_array($regBrand) ? Model\RcsBrand::fromArray($regBrand, 'regBrand') : $regBrand))
            ->file('brandProfile', $brandProfile, false, null)
            ->file('brandBackground', $brandBackground, false, null)
            ->file('seasonDocFile', $seasonDocFile, false, null);

        $response = $this->transport->call(
            'updateRcsBrand',
            multipart: $form->build(),
            response: Model\RcsBrandResponse::class,
        );

        return $response->data->data ?? new Model\RcsBrandResult();
    }
}
