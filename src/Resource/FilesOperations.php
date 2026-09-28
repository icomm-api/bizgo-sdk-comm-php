<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Form;
use Bizgo\Model;
use Bizgo\Upload;

/**
 * Methods of `$client->files` generated from the spec. Used by the hand-written {@see Files} class,
 * whose convenience methods take precedence over generated ones with the same name.
 *
 * @internal
 */
trait FilesOperations
{
    /**
     * 브랜드메시지 이미지 업로드
     *
     * 기본형 브랜드메시지에서 사용하는 이미지를 업로드합니다. 발송 본문에 파일을 직접 첨부하는 API가 아니라 브랜드메시지 템플릿 등록과 구성에 사용할 이미지 URL(`imgUrl`)을 발급받는
     * API입니다.
     *
     * `POST /api/comm/v1/file/brandmessage/default` (operationId `uploadBrandMessageDefaultImage`). 429만 자동 재시도(두
     * 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 브랜드메시지 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageDefault(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageDefaultImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 브랜드메시지 와이드 이미지 업로드
     *
     * 와이드 이미지형 브랜드메시지에서 사용하는 이미지를 업로드합니다. 발급된 `imgUrl`은 브랜드메시지 템플릿 또는 발송 구성에 사용합니다.
     *
     * `POST /api/comm/v1/file/brandmessage/wide` (operationId `uploadBrandMessageWideImage`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 브랜드메시지 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageWide(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageWideImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 브랜드메시지 와이드 리스트 첫번째 이미지 업로드
     *
     * 와이드 리스트형 브랜드메시지의 첫 번째 리스트 이미지에 사용할 이미지를 업로드합니다.
     *
     * `POST /api/comm/v1/file/brandmessage/wideItemList/first` (operationId
     * `uploadBrandMessageWideItemListFirstImage`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 브랜드메시지 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageWideItemListFirst(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageWideItemListFirstImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 브랜드메시지 와이드 리스트 이미지 업로드
     *
     * 와이드 리스트형 브랜드메시지의 2~4번째 리스트 이미지에 사용할 이미지를 업로드합니다.
     *
     * `POST /api/comm/v1/file/brandmessage/wideItemList` (operationId `uploadBrandMessageWideItemListImage`). 429만
     * 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 브랜드메시지 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageWideItemList(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageWideItemListImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 브랜드메시지 캐러셀 피드 이미지 업로드
     *
     * 캐러셀 피드형 브랜드메시지에서 사용하는 이미지를 업로드합니다.
     *
     * `POST /api/comm/v1/file/brandmessage/carouselFeed` (operationId `uploadBrandMessageCarouselFeedImage`). 429만
     * 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 브랜드메시지 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageCarouselFeed(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageCarouselFeedImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 브랜드메시지 캐러셀 커머스 이미지 업로드
     *
     * 캐러셀 커머스형 브랜드메시지에서 사용하는 이미지를 업로드합니다. 전체 캐러셀 이미지 비율은 동일하게 맞춰야 합니다.
     *
     * `POST /api/comm/v1/file/brandmessage/carouselCommerce` (operationId
     * `uploadBrandMessageCarouselCommerceImage`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 브랜드메시지 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageCarouselCommerce(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageCarouselCommerceImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 알림톡 템플릿 이미지 업로드
     *
     * 알림톡 이미지형, 와이드 이미지형, 아이템리스트형 템플릿 등록에 사용할 이미지를 업로드합니다. 메시지 발송용 업로드가 아니라 템플릿 등록을 위한 사전 이미지 등록 API입니다. 응답의
     * `imgUrl`을 템플릿 등록의 `imgUrl`에 넣습니다.
     *
     * `POST /api/comm/v1/file/alimtalk/template` (operationId `uploadAlimtalkTemplateImage`). 429만 자동 재시도(두 번 실행되면
     * 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\AlimtalkTemplateImageUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadAlimtalkTemplateImage(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\AlimtalkTemplateImageUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadAlimtalkTemplateImage',
            multipart: $form->build(),
            response: Model\AlimtalkTemplateImageUploadResponse::class,
        );

        return $response->data->data ?? new Model\AlimtalkTemplateImageUploadResult();
    }

    /**
     * 알림톡 템플릿 하이라이트 이미지 업로드
     *
     * 아이템리스트형 알림톡 템플릿의 아이템 하이라이트 영역에 사용할 이미지를 업로드합니다. 템플릿 이미지 업로드와 같은 템플릿 등록용 사전 이미지 등록 API입니다.
     *
     * 확인 필요(x-unverified): 업로드한 하이라이트 이미지 URL을 템플릿 등록 요청의 어느 필드에 넣는지 문서에 없습니다(`attachment.itemHighlight`에 이미지 필드가
     * 없음).
     *
     * `POST /api/comm/v1/file/alimtalk/itemHighlight` (operationId `uploadAlimtalkItemHighlightImage`). 429만 자동
     * 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-alimtalk
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 아이템 하이라이트 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\AlimtalkTemplateImageUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadAlimtalkItemHighlightImage(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\AlimtalkTemplateImageUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadAlimtalkItemHighlightImage',
            multipart: $form->build(),
            response: Model\AlimtalkTemplateImageUploadResponse::class,
        );

        return $response->data->data ?? new Model\AlimtalkTemplateImageUploadResult();
    }

    /**
     * 브랜드메시지 카탈로그 이미지 업로드
     *
     * 카탈로그형(FG) 브랜드메시지에서 쓰는 1:1 비율 이미지를 업로드하고 템플릿에 쓸 `imgUrl`을 발급받습니다. 홀수형(아이템 3·5·7개)의 첫 번째 아이템은 카탈로그 홀수형 첫번째 이미지
     * 업로드를 씁니다.
     *
     * `POST /api/comm/v1/file/brandmessage/catalog` (operationId `uploadBrandMessageCatalogImage`). 429만 자동 재시도(두
     * 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 카탈로그(FG) 아이템 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageCatalog(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageCatalogImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }

    /**
     * 브랜드메시지 카탈로그 홀수형 첫번째 이미지 업로드
     *
     * 카탈로그형(FG) 홀수형의 첫 번째 아이템에 쓰는 2:1 비율 이미지를 업로드합니다. 나머지 아이템과 짝수형(4·6개)은 카탈로그 이미지 업로드를 씁니다.
     *
     * `POST /api/comm/v1/file/brandmessage/catalog/oddFirst` (operationId
     * `uploadBrandMessageCatalogOddFirstImage`). 429만 자동 재시도(두 번 실행되면 안 되는 요청). 속도 제한: other 버킷(요청 1건).
     *
     * @see https://developers.bizgo.io/api-sdk/api-reference/comm/kakao-brand
     *
     * @param string|\SplFileInfo|Upload|resource $file 업로드할 카탈로그 홀수형(아이템 3·5·7개) 첫 번째 아이템 이미지 파일입니다. (multipart)
     * @param string|null $fileKey 업로드 파일을 식별하는 키입니다. (multipart)
     * @param string|null $imageName 업로드 파일의 이름입니다. (multipart)
     *
     * @return Model\BrandMessageFileUploadResult
     *
     * @throws ValidationException the request is invalid (checked before anything is sent)
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function uploadBrandMessageCatalogOddFirst(
        #[\SensitiveParameter]
        mixed $file,
        #[\SensitiveParameter]
        ?string $fileKey = null,
        #[\SensitiveParameter]
        ?string $imageName = null,
    ): Model\BrandMessageFileUploadResult {
        $form = (new Form())
            ->file('file', $file, true, null)
            ->text('fileKey', $fileKey)
            ->text('imageName', $imageName);

        $response = $this->transport->call(
            'uploadBrandMessageCatalogOddFirstImage',
            multipart: $form->build(),
            response: Model\BrandMessageFileUploadResponse::class,
        );

        return $response->data->data ?? new Model\BrandMessageFileUploadResult();
    }
}
