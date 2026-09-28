<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\Check;
use Bizgo\Internal\Mask;
use Bizgo\Internal\RequestData;
use Bizgo\Internal\Serialize;

/**
 * RCS 브랜드 정보입니다.
 *
 * 확인 필요(x-unverified): 일시 필드(registerDate, approvalDate, updateDate, chatbotDate, logoDate,
 * messagebaseDate)의 형식이 문서화되어 있지 않습니다.
 *
 * 요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. `fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한
 * 필드만 API 이름으로 돌려줍니다.
 */
final readonly class RcsBrand implements RequestModel
{
    /** API field names, in spec order. */
    public const FIELDS = [
        'brandId',
        'name',
        'brandKey',
        'registerDate',
        'approvalDate',
        'updateDate',
        'status',
        'mediaUrl',
        'chatbotDate',
        'logoDate',
        'messagebaseDate',
        'description',
        'tel',
        'menus',
        'categoryId',
        'categoryName',
        'subCategoryId',
        'subCategoryName',
        'categoryOpt',
        'zipCode',
        'roadAddress',
        'detailAddress',
        'email',
        'webSiteUrl',
        'approvalReason',
        'brandFeedUrl',
        'initTab',
        'initFeedItems',
        'templateColor',
        'bizInfoYn',
        'bizInfoTitle',
        'bizInfoContent',
        'saftyStatusYn',
    ];

    /**
     * @param string|null $brandId 브랜드 ID입니다.
     * @param string|null $name 브랜드 이름입니다.
     * @param string|null $brandKey 브랜드 키입니다(최대 200자).
     * @param string|null $registerDate 등록일시입니다(최대 30자).
     * @param string|null $approvalDate 승인일시입니다.
     * @param string|null $updateDate 수정일시입니다.
     * @param string|null $status 브랜드의 상태입니다(최대 1300자).
     * @param list<RcsBrandMediaUrl>|null $mediaUrl 이미지 파일 ID와 URL 및 사용 유형 정보입니다.
     * @param string|null $chatbotDate 브랜드 내 등록된 대화방 중 가장 최근에 변경된 대화방의 일시입니다.
     * @param string|null $logoDate 로고 이미지 중 가장 최근에 변경된 일시입니다.
     * @param string|null $messagebaseDate 브랜드 내 등록된 템플릿 중 가장 최근에 변경된 템플릿의 일시입니다.
     * @param string|null $description 브랜드 설명에 등록된 내용입니다.
     * @param string|null $tel 브랜드 홈에 노출될 전화번호입니다.
     * @param list<RcsBrandMenu>|null $menus 메뉴입니다.
     * @param array<string, mixed>|null $categoryId 브랜드 카테고리 ID입니다.
     * @param list<array<string, mixed>>|null $categoryName 브랜드 카테고리명입니다.
     * @param array<string, mixed>|null $subCategoryId 브랜드 하위 카테고리 ID입니다.
     * @param array<string, mixed>|null $subCategoryName 브랜드 하위 카테고리명입니다.
     * @param array<string, mixed>|null $categoryOpt 검색용 키워드입니다.
     * @param list<array<string, mixed>>|null $zipCode 브랜드 홈에 표시될 우편번호입니다.
     * @param string|null $roadAddress 브랜드 홈에 표시되는 도로명주소입니다.
     * @param string|null $detailAddress 브랜드 홈에 표시되는 상세주소입니다.
     * @param string|null $email 브랜드 홈에 표시되는 이메일 주소입니다.
     * @param string|null $webSiteUrl 브랜드 홈에 표시되는 홈페이지 주소입니다.
     * @param string|null $approvalReason 검수 시 반려 사유입니다.
     * @param string|null $brandFeedUrl 브랜드 소식 URL입니다.
     * @param string|null $initTab 단말에 표시되는 브랜드 홈의 기본 탭입니다.
     * @param string|null $initFeedItems `initTab`이 `FEED`인 경우 소식 탭에 표시할 메뉴입니다.
     * @param string|null $templateColor 브랜드 내 등록되는 템플릿의 버튼 컬러 값입니다.
     * @param string|null $bizInfoYn 브랜드 소식 탭에 운영정보를 사용할지 여부입니다(`Y`/`N`).
     * @param string|null $bizInfoTitle `bizInfoYn`이 `Y`인 경우 운영정보 제목입니다.
     * @param string|null $bizInfoContent `bizInfoYn`이 `Y`인 경우 운영정보 내용입니다.
     * @param string|null $saftyStatusYn 안심마크 지정 기업 여부입니다.
     *
     * @throws ValidationException
     */
    public function __construct(
        /**
         * 브랜드 ID입니다.
         */
        #[\SensitiveParameter]
        public ?string $brandId = null,
        /**
         * 브랜드 이름입니다.
         */
        #[\SensitiveParameter]
        public ?string $name = null,
        /**
         * 브랜드 키입니다(최대 200자).
         *
         * 최대 200자
         */
        #[\SensitiveParameter]
        public ?string $brandKey = null,
        /**
         * 등록일시입니다(최대 30자).
         *
         * 최대 30자
         */
        #[\SensitiveParameter]
        public ?string $registerDate = null,
        /**
         * 승인일시입니다.
         */
        #[\SensitiveParameter]
        public ?string $approvalDate = null,
        /**
         * 수정일시입니다.
         */
        #[\SensitiveParameter]
        public ?string $updateDate = null,
        /**
         * 브랜드의 상태입니다(최대 1300자). 문서 예시 값은 `ready`입니다.
         *
         * 최대 1300자
         *
         * 확인 필요(x-unverified): 상태 값 목록이 문서화되어 있지 않고, 길이 제한 1300자는 상태 코드로 보기에 이례적입니다.
         */
        #[\SensitiveParameter]
        public ?string $status = null,
        /**
         * 이미지 파일 ID와 URL 및 사용 유형 정보입니다.
         */
        #[\SensitiveParameter]
        public ?array $mediaUrl = null,
        /**
         * 브랜드 내 등록된 대화방 중 가장 최근에 변경된 대화방의 일시입니다.
         */
        #[\SensitiveParameter]
        public ?string $chatbotDate = null,
        /**
         * 로고 이미지 중 가장 최근에 변경된 일시입니다.
         */
        #[\SensitiveParameter]
        public ?string $logoDate = null,
        /**
         * 브랜드 내 등록된 템플릿 중 가장 최근에 변경된 템플릿의 일시입니다.
         */
        #[\SensitiveParameter]
        public ?string $messagebaseDate = null,
        /**
         * 브랜드 설명에 등록된 내용입니다.
         */
        #[\SensitiveParameter]
        public ?string $description = null,
        /**
         * 브랜드 홈에 노출될 전화번호입니다.
         */
        #[\SensitiveParameter]
        public ?string $tel = null,
        /**
         * 메뉴입니다.
         */
        #[\SensitiveParameter]
        public ?array $menus = null,
        /**
         * 브랜드 카테고리 ID입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object이지만 하위 필드가 없고, ID 값이므로 문자열일 가능성이 있습니다.
         */
        #[\SensitiveParameter]
        public ?array $categoryId = null,
        /**
         * 브랜드 카테고리명입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object Array이지만 하위 필드가 없고, 이름 값이므로 문자열일 가능성이 있습니다.
         */
        #[\SensitiveParameter]
        public ?array $categoryName = null,
        /**
         * 브랜드 하위 카테고리 ID입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object이지만 하위 필드가 없고, ID 값이므로 문자열일 가능성이 있습니다.
         */
        #[\SensitiveParameter]
        public ?array $subCategoryId = null,
        /**
         * 브랜드 하위 카테고리명입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object이지만 하위 필드가 없고, 이름 값이므로 문자열일 가능성이 있습니다.
         */
        #[\SensitiveParameter]
        public ?array $subCategoryName = null,
        /**
         * 검색용 키워드입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object이지만 하위 필드가 없습니다.
         */
        #[\SensitiveParameter]
        public ?array $categoryOpt = null,
        /**
         * 브랜드 홈에 표시될 우편번호입니다.
         *
         * 확인 필요(x-unverified): 문서 타입은 Object Array이지만 하위 필드가 없고, 우편번호는 문자열일 가능성이 있습니다.
         */
        #[\SensitiveParameter]
        public ?array $zipCode = null,
        /**
         * 브랜드 홈에 표시되는 도로명주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $roadAddress = null,
        /**
         * 브랜드 홈에 표시되는 상세주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $detailAddress = null,
        /**
         * 브랜드 홈에 표시되는 이메일 주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $email = null,
        /**
         * 브랜드 홈에 표시되는 홈페이지 주소입니다.
         */
        #[\SensitiveParameter]
        public ?string $webSiteUrl = null,
        /**
         * 검수 시 반려 사유입니다.
         */
        #[\SensitiveParameter]
        public ?string $approvalReason = null,
        /**
         * 브랜드 소식 URL입니다.
         */
        #[\SensitiveParameter]
        public ?string $brandFeedUrl = null,
        /**
         * 단말에 표시되는 브랜드 홈의 기본 탭입니다. 알려진 값은 `FEED`입니다.
         *
         * 알려진 값: `FEED`
         *
         * 확인 필요(x-unverified): FEED 외의 탭 값이 문서화되어 있지 않습니다.
         */
        #[\SensitiveParameter]
        public ?string $initTab = null,
        /**
         * `initTab`이 `FEED`인 경우 소식 탭에 표시할 메뉴입니다.
         */
        #[\SensitiveParameter]
        public ?string $initFeedItems = null,
        /**
         * 브랜드 내 등록되는 템플릿의 버튼 컬러 값입니다.
         */
        #[\SensitiveParameter]
        public ?string $templateColor = null,
        /**
         * 브랜드 소식 탭에 운영정보를 사용할지 여부입니다(`Y`/`N`).
         *
         * 확인 필요(x-unverified): Y 외의 값(N 등)이 문서에 명시되어 있지 않습니다.
         */
        #[\SensitiveParameter]
        public ?string $bizInfoYn = null,
        /**
         * `bizInfoYn`이 `Y`인 경우 운영정보 제목입니다.
         */
        #[\SensitiveParameter]
        public ?string $bizInfoTitle = null,
        /**
         * `bizInfoYn`이 `Y`인 경우 운영정보 내용입니다.
         */
        #[\SensitiveParameter]
        public ?string $bizInfoContent = null,
        /**
         * 안심마크 지정 기업 여부입니다. 필드명은 문서 표기(`safty`)를 따릅니다.
         */
        #[\SensitiveParameter]
        public ?string $saftyStatusYn = null,
    ) {
        Check::all(
            Check::maxLength('brandKey', $brandKey, 200),
            Check::maxLength('registerDate', $registerDate, 30),
            Check::maxLength('status', $status, 1300),
            Check::listOf('mediaUrl', $mediaUrl, [RcsBrandMediaUrl::class]),
            Check::listOf('menus', $menus, [RcsBrandMenu::class]),
            Check::objectList('categoryName', $categoryName),
            Check::objectList('zipCode', $zipCode),
        );
    }

    /**
     * Builds the model from an array with API field names (for example decoded JSON).
     * Unknown keys, missing required fields and wrong types are reported together, by field path.
     *
     * @param array<array-key, mixed> $data
     *
     * @throws ValidationException
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new RequestData($data, $path, self::FIELDS);
        $brandId = $in->string('brandId');
        $name = $in->string('name');
        $brandKey = $in->string('brandKey');
        $registerDate = $in->string('registerDate');
        $approvalDate = $in->string('approvalDate');
        $updateDate = $in->string('updateDate');
        $status = $in->string('status');
        $mediaUrl = $in->modelList('mediaUrl', RcsBrandMediaUrl::class);
        $chatbotDate = $in->string('chatbotDate');
        $logoDate = $in->string('logoDate');
        $messagebaseDate = $in->string('messagebaseDate');
        $description = $in->string('description');
        $tel = $in->string('tel');
        $menus = $in->modelList('menus', RcsBrandMenu::class);
        $categoryId = $in->object('categoryId');
        $categoryName = $in->objectList('categoryName');
        $subCategoryId = $in->object('subCategoryId');
        $subCategoryName = $in->object('subCategoryName');
        $categoryOpt = $in->object('categoryOpt');
        $zipCode = $in->objectList('zipCode');
        $roadAddress = $in->string('roadAddress');
        $detailAddress = $in->string('detailAddress');
        $email = $in->string('email');
        $webSiteUrl = $in->string('webSiteUrl');
        $approvalReason = $in->string('approvalReason');
        $brandFeedUrl = $in->string('brandFeedUrl');
        $initTab = $in->string('initTab');
        $initFeedItems = $in->string('initFeedItems');
        $templateColor = $in->string('templateColor');
        $bizInfoYn = $in->string('bizInfoYn');
        $bizInfoTitle = $in->string('bizInfoTitle');
        $bizInfoContent = $in->string('bizInfoContent');
        $saftyStatusYn = $in->string('saftyStatusYn');

        return $in->create(static fn (): static => new static(
            brandId: $brandId,
            name: $name,
            brandKey: $brandKey,
            registerDate: $registerDate,
            approvalDate: $approvalDate,
            updateDate: $updateDate,
            status: $status,
            mediaUrl: $mediaUrl,
            chatbotDate: $chatbotDate,
            logoDate: $logoDate,
            messagebaseDate: $messagebaseDate,
            description: $description,
            tel: $tel,
            menus: $menus,
            categoryId: $categoryId,
            categoryName: $categoryName,
            subCategoryId: $subCategoryId,
            subCategoryName: $subCategoryName,
            categoryOpt: $categoryOpt,
            zipCode: $zipCode,
            roadAddress: $roadAddress,
            detailAddress: $detailAddress,
            email: $email,
            webSiteUrl: $webSiteUrl,
            approvalReason: $approvalReason,
            brandFeedUrl: $brandFeedUrl,
            initTab: $initTab,
            initFeedItems: $initFeedItems,
            templateColor: $templateColor,
            bizInfoYn: $bizInfoYn,
            bizInfoTitle: $bizInfoTitle,
            bizInfoContent: $bizInfoContent,
            saftyStatusYn: $saftyStatusYn,
        ));
    }

    /**
     * The request body with API field names. Only fields you set are included.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Serialize::compact([
            'brandId' => $this->brandId,
            'name' => $this->name,
            'brandKey' => $this->brandKey,
            'registerDate' => $this->registerDate,
            'approvalDate' => $this->approvalDate,
            'updateDate' => $this->updateDate,
            'status' => $this->status,
            'mediaUrl' => Serialize::models($this->mediaUrl),
            'chatbotDate' => $this->chatbotDate,
            'logoDate' => $this->logoDate,
            'messagebaseDate' => $this->messagebaseDate,
            'description' => $this->description,
            'tel' => $this->tel,
            'menus' => Serialize::models($this->menus),
            'categoryId' => $this->categoryId,
            'categoryName' => $this->categoryName,
            'subCategoryId' => $this->subCategoryId,
            'subCategoryName' => $this->subCategoryName,
            'categoryOpt' => $this->categoryOpt,
            'zipCode' => $this->zipCode,
            'roadAddress' => $this->roadAddress,
            'detailAddress' => $this->detailAddress,
            'email' => $this->email,
            'webSiteUrl' => $this->webSiteUrl,
            'approvalReason' => $this->approvalReason,
            'brandFeedUrl' => $this->brandFeedUrl,
            'initTab' => $this->initTab,
            'initFeedItems' => $this->initFeedItems,
            'templateColor' => $this->templateColor,
            'bizInfoYn' => $this->bizInfoYn,
            'bizInfoTitle' => $this->bizInfoTitle,
            'bizInfoContent' => $this->bizInfoContent,
            'saftyStatusYn' => $this->saftyStatusYn,
        ]);
    }

    /**
     * The value for json_encode(): like toArray(), with maps as JSON objects even when empty.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return Serialize::compact([
            'brandId' => $this->brandId,
            'name' => $this->name,
            'brandKey' => $this->brandKey,
            'registerDate' => $this->registerDate,
            'approvalDate' => $this->approvalDate,
            'updateDate' => $this->updateDate,
            'status' => $this->status,
            'mediaUrl' => $this->mediaUrl,
            'chatbotDate' => $this->chatbotDate,
            'logoDate' => $this->logoDate,
            'messagebaseDate' => $this->messagebaseDate,
            'description' => $this->description,
            'tel' => $this->tel,
            'menus' => $this->menus,
            'categoryId' => Serialize::jsonObject($this->categoryId),
            'categoryName' => Serialize::jsonObjects($this->categoryName),
            'subCategoryId' => Serialize::jsonObject($this->subCategoryId),
            'subCategoryName' => Serialize::jsonObject($this->subCategoryName),
            'categoryOpt' => Serialize::jsonObject($this->categoryOpt),
            'zipCode' => Serialize::jsonObjects($this->zipCode),
            'roadAddress' => $this->roadAddress,
            'detailAddress' => $this->detailAddress,
            'email' => $this->email,
            'webSiteUrl' => $this->webSiteUrl,
            'approvalReason' => $this->approvalReason,
            'brandFeedUrl' => $this->brandFeedUrl,
            'initTab' => $this->initTab,
            'initFeedItems' => $this->initFeedItems,
            'templateColor' => $this->templateColor,
            'bizInfoYn' => $this->bizInfoYn,
            'bizInfoTitle' => $this->bizInfoTitle,
            'bizInfoContent' => $this->bizInfoContent,
            'saftyStatusYn' => $this->saftyStatusYn,
        ]);
    }

    /**
     * The fields for var_dump()/print_r(): phone numbers masked (`010****0000`), message content and personal
     * data shown as their length only. The properties keep the real values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return Mask::fields(get_object_vars($this));
    }

    /** A log-safe description, masked like {@see self::__debugInfo()}. */
    public function __toString(): string
    {
        return Mask::describe('RcsBrand', get_object_vars($this));
    }
}
