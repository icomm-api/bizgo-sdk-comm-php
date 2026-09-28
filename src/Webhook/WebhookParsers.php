<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Webhook;

use Bizgo\Exception\WebhookPayloadException;
use Bizgo\Model\CounselCertResultWebhookPayload;
use Bizgo\Model\CounselExpiredSessionWebhookPayload;
use Bizgo\Model\CounselMessageWebhookPayload;
use Bizgo\Model\CounselPersonalInfoWebhookPayload;
use Bizgo\Model\CounselReferenceWebhookPayload;
use Bizgo\Model\CounselResultWebhookPayload;
use Bizgo\Model\CounselSeenInfoWebhookPayload;
use Psr\Http\Message\MessageInterface;

/**
 * Webhook parsers generated from the `webhooks` of the spec (`x-sdk-webhook`). Used by {@see WebhookReceiver}, whose
 * hand-written `report()`/`mo()` take precedence.
 *
 * @internal
 */
trait WebhookParsers
{
    /**
     * 상담톡 사용자 메시지 수신(Webhook)
     *
     * 사용자가 상담톡으로 보낸 메시지가 전달됩니다. `content`는 2026년 1월 이후 제공되지 않으므로 `contents[]`를 사용하며,
     * 4,000자를 넘는 메시지는 `attachment.url`(txt 파일)로 전체 내용이 추가 전달됩니다.
     * `msgKey`는 이벤트마다 새로 발급되며 발송 건과 대응하지 않습니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselMessage()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselMessage(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselMessageWebhookPayload {
        return self::parseCounselMessage($rawBody);
    }

    /**
     * Parses a `counselMessage` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselMessage()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselMessage(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselMessageWebhookPayload {
        return self::parse($body, CounselMessageWebhookPayload::class, CounselMessageWebhookPayload::REQUIRED);
    }

    /**
     * 상담톡 사용자 메타 정보 수신(Webhook)
     *
     * 사용자가 상담 연결을 요청한 시점의 메타 정보가 전달됩니다. 현재 메타 정보가 없으면 `lastReference`에 마지막 메타 정보가 옵니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselReference()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselReference(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselReferenceWebhookPayload {
        return self::parseCounselReference($rawBody);
    }

    /**
     * Parses a `counselReference` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselReference()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselReference(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselReferenceWebhookPayload {
        return self::parse($body, CounselReferenceWebhookPayload::class, CounselReferenceWebhookPayload::REQUIRED);
    }

    /**
     * 상담톡 세션 종료 수신(Webhook)
     *
     * 상담 세션이 종료되면 전달됩니다. 세션은 상담원의 상담 종료·사용자 차단, 사용자의 `!종료` 입력·채팅방 나가기·채널 차단,
     * 마지막 메시지 수신 후 30일 경과 시 종료됩니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselExpiredSession()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselExpiredSession(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselExpiredSessionWebhookPayload {
        return self::parseCounselExpiredSession($rawBody);
    }

    /**
     * Parses a `counselExpiredSession` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselExpiredSession()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselExpiredSession(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselExpiredSessionWebhookPayload {
        return self::parse($body, CounselExpiredSessionWebhookPayload::class, CounselExpiredSessionWebhookPayload::REQUIRED);
    }

    /**
     * 상담톡 읽음 정보 수신(Webhook)
     *
     * 사용자 읽음 정보가 전달됩니다. 상담 세션이 연결된 상태에서만, 발송 후 최대 24시간까지 수신되며 지연될 수 있습니다.
     * 이 웹훅을 받으려면 카카오 비즈니스 라운지에 별도 문의가 필요합니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselSeenInfo()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselSeenInfo(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselSeenInfoWebhookPayload {
        return self::parseCounselSeenInfo($rawBody);
    }

    /**
     * Parses a `counselSeenInfo` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselSeenInfo()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselSeenInfo(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselSeenInfoWebhookPayload {
        return self::parse($body, CounselSeenInfoWebhookPayload::class, CounselSeenInfoWebhookPayload::REQUIRED);
    }

    /**
     * 상담톡 개인정보 수신(Webhook)
     *
     * 사용자가 개인정보 수집에 동의한 뒤 카카오 계정 전화번호·닉네임이 전달됩니다. 수집 목적 범위에서만 쓰고 로그에 남기지 않습니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselPersonalInfo()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselPersonalInfo(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselPersonalInfoWebhookPayload {
        return self::parseCounselPersonalInfo($rawBody);
    }

    /**
     * Parses a `counselPersonalInfo` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselPersonalInfo()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselPersonalInfo(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselPersonalInfoWebhookPayload {
        return self::parse($body, CounselPersonalInfoWebhookPayload::class, CounselPersonalInfoWebhookPayload::REQUIRED);
    }

    /**
     * 상담톡 본인인증 결과 수신(Webhook)
     *
     * `KAKAO_CERT` 말풍선으로 요청한 카카오톡 본인인증(전자서명)이 완료되면 결과가 전달됩니다. 상담 세션 상태와 관계없이 전달됩니다.
     * `certResult`는 AES256/CTR/NoPadding + Base64로 암호화된 인증정보(이름·전화번호·생년월일·성별·내외국인·CI)입니다.
     * 카카오 정책상 인증정보는 저장하지 말고 사용자를 식별한 즉시 파기해야 하며, 로그·APM·에러 리포트에 남지 않도록 마스킹합니다.
     * 복호화 키는 카카오가 이메일로 별도 전달하므로 코드·저장소에 넣지 말고 비밀 저장소에서 읽습니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselCertResult()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselCertResult(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselCertResultWebhookPayload {
        return self::parseCounselCertResult($rawBody);
    }

    /**
     * Parses a `counselCertResult` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselCertResult()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselCertResult(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselCertResultWebhookPayload {
        return self::parse($body, CounselCertResultWebhookPayload::class, CounselCertResultWebhookPayload::REQUIRED);
    }

    /**
     * 상담톡 발송 결과 수신(Webhook)
     *
     * Plain/Rich 발송(`write`)과 상담 종료(`end`, `endwithbot`) 요청의 처리 결과가 전달됩니다.
     * `msgKey`는 발송 API 응답의 msgKey와 같으므로 `msgKey`·`ref`로 발송 건과 대조하고, `reportCode`가 `A000`이면 성공입니다.
     *
     * 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, 서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계),
     * 필드 형식만 검사합니다. webhook secret 없이 쓰려면 `WebhookReceiver::parseCounselResult()`를 쓰세요. 응답은
     * `WebhookReceiver::counselAck()`입니다.
     *
     * @param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)
     *
     * @throws WebhookPayloadException the body is not usable
     */
    public function counselResult(
        MessageInterface|array $headers,
        #[\SensitiveParameter]
        string $rawBody,
    ): CounselResultWebhookPayload {
        return self::parseCounselResult($rawBody);
    }

    /**
     * Parses a `counselResult` webhook body. Counsel webhooks are not signed, so this needs no webhook secret
     * and applies the same checks as {@see self::counselResult()}.
     *
     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array
     *
     * @throws WebhookPayloadException the body is not a JSON object with the required fields
     */
    public static function parseCounselResult(
        #[\SensitiveParameter]
        string|array $body,
    ): CounselResultWebhookPayload {
        return self::parse($body, CounselResultWebhookPayload::class, CounselResultWebhookPayload::REQUIRED);
    }
}
