<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource\Counsel;

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
 * `$client->counsel->files`: uploadImage(), upload().
 *
 * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;
 * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).
 */
final class Files
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * 상담톡 이미지 업로드
     *
     * 상담톡 이미지(jpg, png, gif, 최대 5MB)를 올리고 `imgUrl`을 받습니다. Rich 메시지용은 `imageType=rich`로 올립니다.
     *
     * `POST /api/comm/v1/file/cstalk/image` (operationId `uploadCounselImage`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도
     * 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 상담톡 이미지 파일입니다. (multipart)
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     * @param string|null $imageType 이미지 타입입니다. (multipart)
     *
     * @return Model\CounselImageUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadImage(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
        #[\SensitiveParameter]
        ?string $imageType = null,
    ): Model\CounselImageUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('senderKey', $senderKey)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName)
            ->text('imageType', $imageType);

        $response = $this->transport->call(
            'uploadCounselImage',
            multipart: $form->build(),
            response: Model\CounselImageUploadResponse::class,
        );

        return $response->data->data ?? new Model\CounselImageUploadResult();
    }

    /**
     * 상담톡 파일 업로드
     *
     * 상담톡 FILE·AUDIO·VIDEO 타입 첨부 파일을 올리고 `fileUrl`을 받습니다. 운영은 최대 300MB, 샌드박스는 최대 10MB입니다.
     *
     * `POST /api/comm/v1/file/cstalk` (operationId `uploadCounselFile`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한:
     * other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-counsel
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 파일 바이너리입니다. (multipart)
     * @param string $senderKey 카카오 비즈메시지 발신프로필 키입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     * @param string|null $fileType 파일 타입입니다. (multipart, 허용 값: `file`, `audio`, `video`)
     *
     * @return Model\CounselFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function upload(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        string $senderKey,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
        #[\SensitiveParameter]
        ?string $fileType = null,
    ): Model\CounselFileUploadResult {
        Check::all(
            Check::enum('fileType', $fileType, ['file', 'audio', 'video']),
        );
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('senderKey', $senderKey)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName)
            ->text('fileType', $fileType);

        $response = $this->transport->call(
            'uploadCounselFile',
            multipart: $form->build(),
            response: Model\CounselFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\CounselFileUploadResult();
    }
}
