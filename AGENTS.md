# AGENTS.md — bizgo-sdk-comm-php

AI 코딩 도구와 기여자가 이 저장소를 수정할 때 따르는 규칙입니다. SDK를 *사용하는* 코드를 쓸 때는 [llms.txt](llms.txt)를 보세요.
동작 규약은 모든 언어 SDK 공통인 [bizgo-api-spec/docs/SDK-DESIGN.md](https://github.com/icomm-api/bizgo-api-spec/blob/main/docs/SDK-DESIGN.md)를 따릅니다.

## 명령

```bash
composer install
vendor/bin/phpunit                        # 전부 오프라인(mock PSR-18 + 127.0.0.1 내장 서버, README 코드 PHPStan 검사 포함). 네트워크·API Key 불필요
vendor/bin/phpstan analyse                # level max + strict rules
vendor/bin/phpcs                          # PSR-12 (자동 수정: vendor/bin/phpcbf)
php bin/generate-models.php               # 모델·리소스·operation 레지스트리·웹훅 파서 재생성
php bin/generate-models.php --check       # CI: 생성 파일 전체가 스펙과 같은지 (남은 파일도 검사)
composer validate --strict && composer audit
```

변경 후 위 명령이 모두 통과해야 합니다. 지원 최소 버전은 PHP 8.2입니다(8.2 문법으로 작성, CI는 8.2/8.3/8.4/8.5).

## 구조

```
spec/openapi.yaml, spec/error-codes.json   # bizgo-api-spec에서 복사한 스펙 (직접 수정 금지)
bin/generate-models.php                    # 스펙 → src/Model/**, src/Exception/ServiceCodes.php (+ bin/lib/resources.php)
bin/lib/required-if.php                    # 스펙 x-sdk-required-if → src/Internal/RequiredIfRules.php(생성, 조건부 필수 규칙 표)
bin/lib/resources.php                      # 스펙 x-sdk-* → src/Resource/**(생성), src/Resource/FilesOperations.php, src/GeneratedResources.php,
                                           #   src/Internal/Operations.php, src/Webhook/WebhookParsers.php
src/
  BizgoClient.php        # 진입점. send/files/reports/messages(직접 작성) + GeneratedResources 트레이트(생성 리소스)
  AppInfo.php, RateLimit.php  # User-Agent 앱 정보, 클라이언트 속도 제한 설정
  ClientConfig.php       # API Key·base URL·타임아웃 검증 (키는 private, 직렬화 금지)
  Environment.php        # Production / Sandbox
  Version.php            # Version::SDK = 1.2.0 (릴리스 태그 v1.2.0과 같아야 함)
  Upload.php             # 업로드 파일 (로컬 경로·스트림·바이트, 한 번만 읽음)
  Http/                  # Transport(call(operationId): 헤더·속도 제한·재시도·봉투 해석·hooks·로그), Identification, CurlSender, Psr18Sender, Multipart, ErrorMapper
  Exception/             # BizgoException 인터페이스와 예외 계층, ServiceCodes(생성됨)
  Internal/              # 검증·읽기·직렬화·마스킹(Mask)·파라미터(Params)·Form·Pages·TokenBucket/RateLimiter 헬퍼, Operations(생성) (@internal)
  Model/                 # 생성됨 (RequestModel/ResponseModel 인터페이스만 손으로 작성). Model/Response/: 요청과 공유하는 스키마의 응답 모델
  Resource/              # Send, Files, Reports, Messages는 직접 작성, 나머지(하위 디렉터리 포함)와 *Operations 트레이트는 생성
  Result/                # SendResult(succeeded/duplicates(수신자별 A301)/failed), ReportBatch, MessagePage, MoPage, BulkSendResult, BulkChunkError
  Webhook/               # WebhookReceiver (서명 검증, report/mo 직접 작성), WebhookParsers(생성, 상담톡 등)
  Observability/         # Hook, RequestEvent, OpenTelemetryHook(open-telemetry/api가 있을 때만)
  Testing/               # FakeTransport, Stub, RecordedRequest, signWebhook — 고객 테스트용(운영 코드에서 쓰지 않음)
examples/                # tests/ExamplesTest.php가 mock으로 실행
tests/                   # PHPUnit. Support/에 mock PSR-18 클라이언트, 내장 서버 router
```

## 규칙

1. **생성 파일은 손으로 고치지 않습니다**(`// Generated ...` 헤더가 있는 파일). 모델을 바꾸려면 bizgo-api-spec을 고치고 `spec/`에 복사한 뒤 재생성합니다. 생성기가 모르는 스키마 키워드는 무시하지 말고 생성기를 고칩니다(모르면 실패하도록 되어 있음).
2. 새 엔드포인트는 **직접 작성하지 않습니다.** bizgo-api-spec에 `x-sdk-resource`/`x-sdk-method`/`x-sdk-retry`(+ `x-sdk-pagination`, `x-sdk-result`, `x-sdk-rate`)를 넣고 `spec/`에 복사한 뒤 재생성합니다. `tests/AllOperationsTest.php`가 모든 operation을 method·경로 템플릿·재시도·페이지 방식까지 호출해 확인합니다. P0 편의 메서드(`Send`/`Files`/`Reports`/`Messages`)는 직접 작성하며 같은 이름이면 생성 메서드보다 우선합니다(생성기가 소스를 읽어 건너뜀). 직접 작성한 메서드도 `Transport::call('<operationId>', ...)`로 보냅니다(경로·재시도·속도 제한·hooks가 레지스트리를 따름).
3. 공개 메서드는 named arguments로 쓰기 좋게 인자 이름을 API 이름과 맞추고, PHPDoc(`@param`, `@return`, `@throws`)을 갖춥니다. 반환 타입은 생성 모델이나 `Result/` 타입입니다(배열 반환 금지, 목록은 `list<Model>`).
4. 재시도 정책은 스펙의 `x-sdk-retry`를 따릅니다(`safe` → `Retry::Safe`, `rate_limit_only` → `Retry::RateLimitOnly`). 예외: 발송은 `idempotencyKey`가 있을 때만 `Safe`. 3xx는 재시도하지 않습니다. 속도 제한 버킷은 `x-sdk-rate`(`send`면 수신자 수만큼 토큰)를 따르며 목록을 코드에 하드코딩하지 않습니다.
5. 예제·README의 코드는 실제로 동작해야 합니다. 예제를 바꾸면 `tests/ExamplesTest.php`도 맞춥니다.
6. 문서에 없는 동작을 가정하지 않습니다. 불확실하면 스펙에 `x-unverified`로 표시하고 bizgo-api-spec의 GitHub Issues에 알립니다.
7. `@phpstan-ignore`, baseline, 타입을 넓혀 오류를 숨기는 방식은 쓰지 않습니다.
8. **멱등성 키 유효시간 기본값**(SDK-DESIGN.md §12.20): `idempotencyKey`가 있고 `idempotencyTtl`이 없으면 `Send::DEFAULT_IDEMPOTENCY_TTL`(86400)을 보냅니다(서버는 A309로 거절). 직접 지정한 값(0 포함)은 그대로, 키가 없으면 TTL을 추가하지 않습니다. `Transport::call()`이 한 곳에서 처리합니다: 생성기가 본문 스키마에 두 필드가 모두 있는 operation을 레지스트리에 `'idempotency' => true`로 표시하고, `Transport`가 모델·배열의 JSON 사본에만 값을 넣습니다(사용자 모델은 바꾸지 않음). 발송 메서드마다 따로 채우지 않습니다. 관련 테스트: `tests/IdempotencyTtlTest.php`.
9. **조건부 필수**(`x-sdk-required-if`, SDK-DESIGN.md §4): 규칙은 스펙에서만 읽습니다(필드 이름을 코드에 하드코딩하지 않음). 생성기(`bin/lib/required-if.php`)가 규칙 형식을 검사해 `Internal\RequiredIfRules`(모델별 규칙 `MODELS`, 요청 본문 루트별 `$.` 경로 규칙 `ROOTS`)를 만들고, 해당 요청 모델 생성자가 기본 검사 뒤 `Internal\RequiredIf::check('<스키마>', $this->toArray())`를 부릅니다. 모델 자신의 규칙(`required`, 상대 경로)은 그 모델을 만들 때, `$.` 경로는 루트 본문(`SendOmniRequest`, `ReservationCreateRequest` 등, 경로 첫 속성이 없는 루트는 제외)을 만들 때 검사합니다. 배열 본문도 `fromArray()`로 같은 생성자를 거칩니다. 오류는 경로와 조건(`필수 필드입니다 (sendType != template일 때)`)만 담습니다. 새 연산자·키는 bizgo-api-spec AGENTS.md 11번과 함께 고치고 생성기가 모르는 값이면 실패하게 둡니다. 관련 테스트: `tests/RequiredIfTest.php`.

## 보안 규칙 (오픈소스 저장소)

- **로그·예외에 민감정보 금지**: API Key, 요청 본문, 쿼리 문자열, 전화번호, 웹훅 secret을 로그, 예외 메시지, `__toString`, `__debugInfo`에 넣지 않습니다. HTTP 라이브러리 예외는 URL을 담고 있으므로 `previous`로 연결하지 않고, `curl_error()` 대신 `curl_strerror(errno)`를 씁니다. 관련 테스트: `tests/ErrorsAndSecurityTest.php`, `tests/CurlSenderTest.php`.
- 키·secret·전화번호·본문을 받는 인자에는 `#[\SensitiveParameter]`를 붙입니다(스택 트레이스에서 가려짐). 생성 모델은 모든 생성자 인자에 붙입니다.
- 검증 오류(`ValidationException`)에는 필드 경로와 이유만 넣고 입력값은 넣지 않습니다.
- TLS 검증을 끄는 옵션, 리다이렉트 추적, `http://` base URL(localhost 제외)을 허용하는 코드를 추가하지 않습니다. 사용자 URL로 Authorization 헤더를 보내는 기능을 만들지 않습니다.
- 경로 파라미터는 `Internal\Path::segment()`로 인코딩합니다(`.`/`..`/빈 값 거부). 업로드 파일 경로는 로컬 경로만 받습니다(스트림 래퍼 거부).
- 테스트·예제의 값은 placeholder만 씁니다: 전화번호 `01000000000`, `01000001234`, 키 `test-api-key-not-real`, `SENDER_KEY_EXAMPLE`. 실제 키·번호·발신프로필 키를 커밋하지 않습니다(gitleaks가 pre-commit·CI에서 검사). 테스트는 비즈고 서버에 접속하지 않습니다.
- 런타임 의존성은 PSR 인터페이스 패키지 4개(`psr/http-client`, `psr/http-factory`, `psr/http-message`, `psr/log`)만 둡니다. `open-telemetry/api`는 `suggest`와 `require-dev`(정적 분석·테스트용)에만 둡니다. 추가가 필요하면 이유를 PR에 적습니다. `composer.json`의 `allow-plugins`는 `false`로 둡니다.
- `Authorization`, `User-Agent`, `X-Bizgo-Client`는 `Transport`가 요청마다 마지막에 설정합니다. 헤더 파라미터·사용자 HTTP 클라이언트로 바꿀 수 있게 하지 않습니다. 사용자 HTTP 클라이언트는 SDK가 안전한 설정을 강제하거나 점검할 수 있는 것만 받습니다: Guzzle(`GuzzleSender`: 요청별 안전 옵션, 위험 cURL 옵션·비기본 미들웨어 거부), Symfony(`Psr18Sender::symfony()`: 리다이렉트·인증 해제, RetryableHttpClient 거부). 그 밖의 PSR-18 클라이언트는 `trustHttpClient: true`일 때만 받습니다(SDK-DESIGN.md §12.6 일반 원칙). 이 기본값을 느슨하게 하지 않습니다. 관련 테스트: `tests/HttpClientPolicyTest.php`.
- 키·secret·본문·경로·파일 경로를 받을 수 있는 인자에는 `#[\SensitiveParameter]`를 붙입니다(생성 메서드는 모든 인자, 생성 모델의 `fromArray()`). API Key·웹훅 secret·프록시 자격 증명은 `Internal\Secret`에 담아 `var_export()`에도 나오지 않게 합니다.
- hooks·로그·`Bizgo\Testing` 기록에는 operationId·경로 템플릿·상태만 넣습니다. 본문, 쿼리, 헤더 값, 실제 경로 값을 넣지 않습니다. 사용자 hook/logger 예외는 삼킵니다(결과를 바꾸지 않음).
- 모델·결과의 `__debugInfo()`/`__toString()`은 `Internal\Mask`로 전화번호·본문·개인정보를 가립니다. 새 필드 이름이 전화번호·본문이면 `Mask`의 목록에 추가합니다.
- 리포트·MO 웹훅 서명은 `X-IB-Signature` = `HmacSHA256(secret, X-IB-Timestamp)`입니다(비즈고 확인). 다른 서명 헤더 이름이나 `sha256=` 같은 접두어는 받지 않고, 출력 인코딩은 아직 확인되지 않아 hex(대소문자 무시)·base64를 모두 허용합니다. 웹훅 secret은 비즈고에 요청해 받습니다.
- 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 상담톡 파서는 서명을 요구·검사하지 않고(서명 헤더는 무시) 본문 검사(1MB, JSON 64단계, 형식)만 합니다. 정적 `parseCounsel*()`는 secret 없이 씁니다. 리포트·MO는 항상 서명을 검증합니다. 생성기(`bin/lib/resources.php`)는 스펙에 `X-IB-Timestamp`/`X-IB-Signature` 헤더 파라미터가 있는 웹훅만 서명 검증 파서로 만듭니다.
- SDK-DESIGN.md §12의 강화 규칙 19개는 `tests/HardeningTest.php`(와 BulkTest, HooksTest, CurlSenderTest)에 규칙별 회귀 테스트가 있습니다. 규칙을 약하게 만드는 변경은 하지 않습니다.
