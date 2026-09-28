<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Internal\Mask;
use Bizgo\Internal\ResponseData;
use Bizgo\Internal\Serialize;

/**
 * 발급된 카카오 업로드 채널 정보입니다. `uploadUrl`과 `token`은 발급 후 5분간만 유효합니다. 파일은 `POST {uploadUrl}`에
 * `x-kamp-upload-token: {token}` 헤더와 multipart `file` 필드로 직접 전송합니다(비즈고 API를 거치지 않음).
 *
 * 응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.
 */
final readonly class BrandMessageVideoUploadChannel implements ResponseModel
{
    /** API field names, in spec order. */
    public const FIELDS = ['vid', 'uploadUrl', 'token'];

    /** Fields the spec marks as required. They are not enforced when parsing a response. */
    public const REQUIRED = [];

    /**
     * @param string|null $vid 동영상 식별자입니다.
     * @param string|null $uploadUrl 동영상 파일을 전송할 카카오 업로드 URL입니다.
     * @param string|null $token 업로드 요청 시 `x-kamp-upload-token` 헤더로 보낼 인증 토큰(JWT)입니다.
     * @param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name
     */
    public function __construct(
        /**
         * 동영상 식별자입니다. 이후 조회 API에서 사용합니다.
         */
        public ?string $vid = null,
        /**
         * 동영상 파일을 전송할 카카오 업로드 URL입니다.
         */
        public ?string $uploadUrl = null,
        /**
         * 업로드 요청 시 `x-kamp-upload-token` 헤더로 보낼 인증 토큰(JWT)입니다.
         */
        public ?string $token = null,
        /** Fields the spec does not describe (new server fields), by API name. */
        public array $extra = [],
    ) {
    }

    /**
     * @param array<array-key, mixed> $data decoded JSON object
     *
     * @throws InvalidResponseException when a field has an incompatible type
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $in = new ResponseData($data, $path, self::FIELDS);

        return new static(
            vid: $in->string('vid'),
            uploadUrl: $in->string('uploadUrl'),
            token: $in->string('token'),
            extra: $in->extra(),
        );
    }

    /**
     * The fields with API names, followed by the unknown fields from `$extra`. Unset fields are omitted.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Serialize::compact([
            'vid' => $this->vid,
            'uploadUrl' => $this->uploadUrl,
            'token' => $this->token,
        ]) + $this->extra;
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
        return Mask::describe('BrandMessageVideoUploadChannel', get_object_vars($this));
    }
}
