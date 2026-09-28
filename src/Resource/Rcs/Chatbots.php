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
 * `$client->rcs->chatbots`: list(), get(), update(), cancel(), delete(), getUsableQueries().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Chatbots
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
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
        string $brandId,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\RcsChatbotResponse {
        $query = [
            'brandId' => $brandId,
            'offset' => $offset,
            'limit' => $limit,
        ];

        return $this->transport->call(
            'listRcsChatbots',
            query: $query,
            response: Model\RcsChatbotResponse::class,
        );
    }

    /**
     * RCS 대화방 목록 조회
     *
     * 브랜드 ID를 기준으로 등록된 RCS 대화방 목록을 조회합니다. 대화방(챗봇)은 브랜드에 속해 실제로 메시지를 주고받는 단위입니다.
     *
     * 확인 필요(x-unverified): 응답에 전체 건수나 다음 페이지 여부가 없어, 받은 건수가 limit보다 적을 때 끝으로 판단해야 합니다.
     *
     * `GET /api/comm/v1/center/rcs/chatbot/list` (operationId `listRcsChatbots`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 조회할 브랜드 ID입니다. (쿼리)
     * @param int|null $offset 조회 시작 위치입니다. (쿼리, 서버 기본값 `0`)
     * @param int|null $limit 조회 최대 건수입니다. (쿼리, 서버 기본값 `1000`)
     *
     * @return Model\RcsChatbotResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function list(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        ?int $offset = null,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): Model\RcsChatbotResult {
        $response = $this->listResponse(brandId: $brandId, offset: $offset, limit: $limit);

        return $response->data->data ?? new Model\RcsChatbotResult();
    }

    /**
     * Iterates over every item of {@see self::list()}, fetching the following pages as needed.
     *
     * `offset`를 받은 항목 수만큼 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지에서 멈춥니다.
     *
     * @param string $brandId 조회할 브랜드 ID입니다. (쿼리)
     * @param int|null $limit 조회 최대 건수입니다. (쿼리, 서버 기본값 `1000`)
     *
     * @return \Generator<int, Model\Response\RcsChatbot>
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterList(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        ?int $limit = null,
    ): \Generator {
        $pagePosition = 0;
        while (true) {
            $pageResponse = $this->listResponse(brandId: $brandId, limit: $limit, offset: $pagePosition);
            $pageItems = $pageResponse->data->data->rcs->chatbot ?? [];
            foreach ($pageItems as $pageItem) {
                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item
            }
            $itemCount = count($pageItems);
            if ($itemCount === 0 || $itemCount < ($limit ?? 1000)) {
                return;
            }
            $pagePosition += $itemCount;
        }
    }

    /**
     * RCS 대화방 상세 조회
     *
     * 브랜드 ID와 대화방 ID를 기준으로 RCS 대화방 상세 정보를 조회합니다. 결과는 `data.data.rcs.chatbot` 배열로 돌아옵니다.
     *
     * `GET /api/comm/v1/center/rcs/chatbot` (operationId `getRcsChatbot`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other
     * 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 조회할 브랜드 ID입니다. (쿼리)
     * @param string $chatbotId 조회할 대화방 ID입니다. (쿼리)
     *
     * @return Model\RcsChatbotResult
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
        string $chatbotId,
    ): Model\RcsChatbotResult {
        $query = [
            'brandId' => $brandId,
            'chatbotId' => $chatbotId,
        ];

        $response = $this->transport->call(
            'getRcsChatbot',
            query: $query,
            response: Model\RcsChatbotResponse::class,
        );

        return $response->data->data ?? new Model\RcsChatbotResult();
    }

    /**
     * RCS 대화방 수정
     *
     * 등록된 RCS 대화방 정보를 수정합니다. `multipart/form-data`로 보내며, 대화방 정보(`chatbot`)는 JSON 문자열,
     * 부가번호 증명 서류(`subNumCertificate`)는 파일로 함께 보냅니다.
     * 파일 스트림을 다시 보내야 하고 검수 요청이 다시 걸릴 수 있으므로 SDK는 429만 재시도합니다.
     *
     * `PUT /api/comm/v1/center/rcs/chatbot` (operationId `updateRcsChatbot`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도
     * 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 수정할 대화방이 속한 브랜드 ID입니다. (multipart)
     * @param Model\RcsChatbot|array<string, mixed> $chatbot 수정할 대화방 정보입니다. (multipart)
     * @param string|\SplFileInfo|Upload|resource $subNumCertificate 부가번호 사용을 증명하는 서류 파일입니다. (multipart)
     *
     * @return Model\RcsChatbotResult
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
        Model\RcsChatbot|array $chatbot,
        #[\SensitiveParameter]
        mixed $subNumCertificate,
    ): Model\RcsChatbotResult {
        $form = (new Form())
            ->text('brandId', $brandId)
            ->json('chatbot', (is_array($chatbot) ? Model\RcsChatbot::fromArray($chatbot, 'chatbot') : $chatbot))
            ->file('subNumCertificate', $subNumCertificate, true, null);

        $response = $this->transport->call(
            'updateRcsChatbot',
            multipart: $form->build(),
            response: Model\RcsChatbotResponse::class,
        );

        return $response->data->data ?? new Model\RcsChatbotResult();
    }

    /**
     * RCS 대화방 승인 취소
     *
     * 승인 진행 중인 RCS 대화방의 승인 요청을 취소합니다.
     *
     * 확인 필요(x-unverified): 응답이 대화방 조회와 같은 chatbot 배열로 문서화되어 있으나, 승인 취소 결과로 무엇이 돌아오는지 확인되지 않았습니다.
     *
     * `PUT /api/comm/v1/center/rcs/brandId/{brandId}/chatbotId/{chatbotId}/cancel` (operationId
     * `cancelRcsChatbotApproval`). 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 브랜드 ID입니다. (경로)
     * @param string $chatbotId 대화방 ID입니다. (경로)
     *
     * @return Model\RcsChatbotResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function cancel(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        string $chatbotId,
    ): Model\RcsChatbotResult {
        $response = $this->transport->call(
            'cancelRcsChatbotApproval',
            path: ['brandId' => $brandId, 'chatbotId' => $chatbotId],
            response: Model\RcsChatbotResponse::class,
        );

        return $response->data->data ?? new Model\RcsChatbotResult();
    }

    /**
     * RCS 대화방 삭제
     *
     * 등록된 RCS 대화방을 삭제합니다.
     *
     * 확인 필요(x-unverified): 응답이 대화방 조회와 같은 chatbot 배열로 문서화되어 있으나, 삭제 결과로 무엇이 돌아오는지 확인되지 않았습니다.
     *
     * `DELETE /api/comm/v1/center/rcs/brandId/{brandId}/chatbotId/{chatbotId}` (operationId `deleteRcsChatbot`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $brandId 브랜드 ID입니다. (경로)
     * @param string $chatbotId 대화방 ID입니다. (경로)
     *
     * @return Model\RcsChatbotResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function delete(
        #[\SensitiveParameter]
        string $brandId,
        #[\SensitiveParameter]
        string $chatbotId,
    ): Model\RcsChatbotResult {
        $response = $this->transport->call(
            'deleteRcsChatbot',
            path: ['brandId' => $brandId, 'chatbotId' => $chatbotId],
            response: Model\RcsChatbotResponse::class,
        );

        return $response->data->data ?? new Model\RcsChatbotResult();
    }

    /**
     * RCS 대화방 사용 가능 쿼리 조회
     *
     * 대화방 ID를 기준으로 해당 대화방에서 사용할 수 있는 쿼리 정보를 조회합니다.
     *
     * 확인 필요(x-unverified): 응답이 대화방 정보(chatbot 배열)로 문서화되어 있어 쿼리 정보 필드가 없습니다. 문서 복사 오류일 가능성이 높습니다.
     *
     * `GET /api/comm/v1/center/rcs/usableQuery/chatbotId/{chatbotId}` (operationId `getRcsChatbotUsableQueries`).
     * 429·5xx·네트워크 오류 시 자동 재시도. 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/rcs
     *
     * @param string $chatbotId 대화방 ID입니다. (경로)
     *
     * @return Model\RcsChatbotResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function getUsableQueries(
        #[\SensitiveParameter]
        string $chatbotId,
    ): Model\RcsChatbotResult {
        $response = $this->transport->call(
            'getRcsChatbotUsableQueries',
            path: ['chatbotId' => $chatbotId],
            response: Model\RcsChatbotResponse::class,
        );

        return $response->data->data ?? new Model\RcsChatbotResult();
    }
}
