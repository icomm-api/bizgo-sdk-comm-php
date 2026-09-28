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
 * `$client->kakao->categories`: list(), get().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Categories
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 발신프로필 카테고리 전체조회
     *
     * 발신프로필 등록 시 사용할 수 있는 카카오 비즈메시지 카테고리 목록을 조회합니다.
     *
     * `GET /api/comm/v1/center/kakao/sender/category/list` (operationId `listKakaoSenderCategories`). 429·5xx·네트워크
     * 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @return list<Model\KakaoCategory>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(): array
    {
        $response = $this->transport->call(
            'listKakaoSenderCategories',
            response: Model\KakaoCategoryListResponse::class,
        );

        return $response->data->data->kakao->categories ?? [];
    }

    /**
     * 발신프로필 카테고리 상세조회
     *
     * 카테고리 코드를 기준으로 카카오 비즈메시지 카테고리 상세 정보를 조회합니다.
     *
     * `GET /api/comm/v1/center/kakao/sender/category` (operationId `getKakaoSenderCategory`). 429·5xx·네트워크 오류 시 자동
     * 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-common
     *
     * @param string $code 조회할 카테고리 코드입니다. (쿼리)
     *
     * @return Model\KakaoCategory
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function get(
        #[\SensitiveParameter]
        string $code,
    ): Model\KakaoCategory {
        $query = [
            'code' => $code,
        ];

        $response = $this->transport->call(
            'getKakaoSenderCategory',
            query: $query,
            response: Model\KakaoCategoryResponse::class,
        );

        return $response->data->data->kakao->category ?? new Model\KakaoCategory();
    }
}
