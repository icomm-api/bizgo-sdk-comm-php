# Changelog

이 프로젝트는 [Semantic Versioning](https://semver.org/lang/ko/)을 따릅니다. 버전은 git 태그(`v1.2.0`)와 `Bizgo\Version::SDK`로 정합니다.

## [Unreleased]

## 1.2.0 - 2026-09-28

첫 릴리스. 비즈고 커뮤니케이션 API(`https://mars.ibapi.kr`) 기준으로 새로 작성했습니다.
이전 `infobank/infobank-omni-api-php7-sdk`, `infobank/infobank-omni-api-php-sdk`(OMNI API)와 호환되지 않습니다.

### 추가
- `BizgoClient`, API Key 인증(환경변수 `BIZGO_API_KEY`), 운영·sandbox 환경, PHP 8.2 이상
- 통합 발송 `send->omni()`와 간편 메서드 `send->sms()`/`lms()`/`mms()`, 대체발송, 치환, 멱등성 키
- 채널 모델: SMS, LMS/MMS, 국제, RCS, 알림톡, 브랜드메시지, 네이버 톡톡 (OpenAPI 스펙에서 생성한 `final readonly` 클래스)
- 보내기 전 검증: 필수·오타 필드, 길이, 바이트 길이(EUC-KR/CP949), 이모지, 수신자 200명, enum, 형식
- 조건부 필수 검증(스펙 `x-sdk-required-if`, 생성한 규칙 표 `Internal\RequiredIfRules` + `Internal\RequiredIf`): 모델·배열 본문 모두 보내기 전에 `ValidationException`(경로·조건만, 입력값 없음). 알림톡 전문 발송은 `msgType`(`AT`/`AI`)·`text` 필수(빠지면 서버 A523, 2026-09-28 sandbox 확인), `sendType: template`은 모든 `destinations[].replaceWords` 필수(`$.` 경로는 `SendOmniRequest`·`ReservationCreateRequest` 단위로 검사), 브랜드메시지 `sendType`별 필드, 브랜드메시지 버튼 `WL`/`AL` URL, RCS `header: '1'`의 `footer`, 상담톡 Plain/Rich 타입별 첨부
- 알림톡 템플릿 응답 `AlimtalkTemplate::$msgType`(알려진 값 `AT`/`AI`, 문자열): 전문 발송의 `AlimtalkMessage.msgType`에 그대로 사용
- 이미지 업로드: MMS(300KB 검사), RCS, 브랜드메시지 6종
- 리포트: polling(`consume`), 수신 확인, 개별 조회
- 조회: 상태(단건·요청별), 발송 이력·MO 이력(generator로 페이지 자동 순회), 통계
- 웹훅: 서명 검증(hex/base64, constant-time), 리포트·MO 페이로드 파싱
- 오류 계층(게이트웨이/상품 단계 구분, 에러코드 설명), 재시도(429·5xx·네트워크, 발송은 멱등성 키가 있을 때만, `Retry-After`)
- 내장 cURL 전송(리다이렉트 미추적, TLS 검증 고정, 압축 해제 후 응답 16MiB 제한, `proxy`/`caInfo`/`caPath` 옵션, 클라이언트당 cURL 핸들 재사용), PSR-3 로깅
- HTTP 클라이언트 정책: Guzzle은 요청별로 리다이렉트·자체 인증·쿠키를 끄고 위험한 cURL 옵션·비기본 미들웨어를 거부(스트리밍 16MiB 제한), Symfony `Psr18Client`는 리다이렉트·인증 해제와 `RetryableHttpClient` 거부, 그 밖의 PSR-18 클라이언트는 `trustHttpClient: true`일 때만
- 스택 트레이스·덤프 보호: 본문·요청 배열·파일 경로·헤더 값 인자에 `#[\SensitiveParameter]`, 예외의 `__debugInfo()`, API Key·웹훅 secret·프록시 자격 증명을 `var_export()`에서도 숨김, 치환 값 마스킹
- 날짜 문자열 검증·정규화(잘못된 날짜 거부, 시간대 없는 MO 시각은 +09:00, `Z`·오프셋은 KST로 변환), int64 `lastSeq`, 웹훅 큰 정수는 문자열 그대로
- 릴리스: verify 성공 뒤에만 Packagist 업데이트(보호된 release 환경), 태그 보호 안내
- **전체 API**: 스펙의 operation 146개 전체를 `x-sdk-*` 메타데이터로 생성한 메서드로 제공 (`reservations`, `insights`, `kakao`, `alimtalk`, `brandMessage`, `rcs`, `counsel` 리소스와 `files`의 추가 업로드). 경로·본문·쿼리 named arguments, 본문 모델 검증, `x-sdk-result` 반환(빈 결과는 빈 모델·배열), `x-sdk-retry` 재시도, cursor·page·offset 페이지 순회 generator(`iter<Method>`), multipart 업로드
- 요청·응답 공용 스키마의 응답 모델(`Bizgo\Model\Response\`), 생성 operation 레지스트리(`Bizgo\Internal\Operations`), `--check`가 생성 파일 전체(529개)를 검사
- 웹훅: 상담톡 이벤트 7종 파서(`counselMessage()` 등), 서명 없음(서명 헤더는 무시, secret 없이 쓰는 정적 `parseCounsel*()`), `counselAck()`, 본문 오류는 `WebhookPayloadException`
- 대량 발송 `send->bulk()`: 청크 분할, 청크별 오류 수집, 멱등성 키 `<prefix>-<chunkSize>-<start>-<hash8>`(언어 공통 테스트 벡터), `BulkSendException`(이미 접수된 결과 보존)
- 멱등성 키 유효시간 기본값 `Send::DEFAULT_IDEMPOTENCY_TTL`(86400초): `idempotencyKey`가 있고 `idempotencyTtl`이 없으면 86400을 채워 보냄(서버는 TTL 없는 키를 A309로 거절, 2026-09-28 sandbox 확인). 직접 지정한 값(0 포함)은 유지, 키가 없으면 추가하지 않음. 발송 메서드, `send->request()`(모델·배열), bulk 청크, 본문에 두 필드가 모두 있는 생성 operation에 적용(레지스트리 `idempotency` 표시, 사용자 모델은 바꾸지 않음)
- 수신자별 A301(같은 `idempotencyKey`로 다시 보낸 요청은 HTTP 200 성공, `destinations[].code` = A301, 2026-09-28 sandbox 확인)을 `failed()`가 아닌 `SendResult::duplicates()`로 반환(`succeeded()`·`msgKeys()`에도 넣지 않음). `BulkSendResult::duplicates()`는 청크별 중복을 모으고 모두 A301인 청크는 오류가 아님(`ok()` 유지). 요청 단위 A301은 그대로 `DuplicateRequestException`. `FakeTransport`는 `sendResult('A301')`로 흉내
- 클라이언트 속도 제한(`RateLimit`): send 버킷(초당 200 메시지, 수신자 수 기준, `x-sdk-rate: send`)과 other 버킷(초당 5 요청), 시도마다 대기, 프로세스 단위
- 테스트 도구 `Bizgo\Testing\FakeTransport`: 요청 기록(헤더 값 제외), operation별 기본 성공 응답(`resvKey` 포함), 오류 주입, `signWebhook()`
- 관측: `Bizgo\Observability\Hook`(operationId, 경로 템플릿, 상태, layer/code, 시도 횟수, 소요 시간), `OpenTelemetryHook`(`open-telemetry/api`가 있을 때만)
- SDK 식별 헤더 `User-Agent`(OS·아키텍처 대략값, 선택 `AppInfo`)와 `X-Bizgo-Client`
- 강화 규칙(SDK-DESIGN.md §12): 응답 파싱 오류를 `InvalidResponseException`(상태·trackingId·본문)으로, 재시도 뒤 A301은 `alreadyAccepted`, 리다이렉트 미재시도, `Retry-After` 검증, 예외 직렬화, 전화번호·본문 마스킹, API Key 형식 검사(자르지 않음), 파일 오류는 파일 이름만, JSON 64단계 제한, 웹훅 timestamp 1~16자리, 사용자 콜백 예외 격리
