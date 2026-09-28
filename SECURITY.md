# 보안 정책

## 취약점 신고

보안 취약점은 **공개 Issue로 올리지 마세요.** GitHub의 [Private vulnerability reporting](../../security/advisories/new)으로 비공개 신고해 주세요.
확인 후 영업일 기준 5일 안에 답변합니다.

지원 버전: 최신 minor 버전(현재 1.2.x)에 보안 수정을 제공합니다. PHP는 보안 지원 중인 버전(8.2 이상)만 지원합니다.

## 자격 증명이 노출됐을 때

1. 즉시 비즈고 콘솔 `발송관리 > 연동관리`에서 해당 API Key를 폐기하고 새로 발급합니다. 저장소 기록에서 지워도 이미 복제됐을 수 있으므로 **폐기가 먼저**입니다.
2. 웹훅 secret은 비즈고에 재발급을 요청합니다.
3. 허용 IP(ACL) 목록을 점검합니다.

## SDK의 보안 동작

- API Key는 `Authorization` 헤더로만 보내고, 로그·예외 메시지·`__toString`·`var_dump`/`print_r`에 넣지 않습니다. `#[\SensitiveParameter]`로 스택 트레이스에서도 가립니다. 클라이언트와 설정 객체는 직렬화할 수 없습니다.
- 요청 본문, 쿼리 문자열, 검증 오류의 입력값(전화번호 포함 가능)을 로그·예외에 넣지 않습니다. HTTP 라이브러리 예외(URL 포함)는 원인으로 연결하지 않습니다.
- `ApiException::$body`에는 응답 원문이 있습니다. 전화번호가 있을 수 있으니 그대로 로그에 남기지 마세요.
- TLS 검증(peer·host)은 항상 켜져 있고 끄는 옵션이 없습니다. 리다이렉트는 따르지 않습니다. https가 아닌 base URL은 거부합니다(테스트용 localhost 제외). 응답 본문은 16MiB까지만 읽습니다.
- 경로 파라미터는 URL 인코딩하고 `.`/`..`를 거부합니다. 업로드는 로컬 파일 경로만 읽습니다(`http://`, `phar://` 등 스트림 래퍼 거부). multipart 파일명의 제어 문자는 제거합니다.
- 발송은 `idempotencyKey`가 없으면 타임아웃·5xx 후 자동 재시도하지 않습니다(중복 발송 방지).
- 웹훅 서명은 `hash_equals`(constant-time)로 비교하고 timestamp 허용 오차(기본 300초)를 벗어난 오래된 요청을 거부합니다. 본문은 최대 1MB입니다.
  운영 환경에서는 HTTPS, 발신 IP 허용 목록, `msgKey` 기준 중복 제거를 함께 적용하고, 중요한 판단은 조회 API로 결과를 확인하세요.
- SDK 자체에는 TLS 검증을 끄는 옵션이 없습니다. 사용자 HTTP 클라이언트를 넘기면 **그 클라이언트의 TLS 설정이 그대로 적용**되므로 검증을 켜 두세요.
- **HTTP 클라이언트 정책** (SDK-DESIGN.md §12.6): 내장 cURL 전송을 권장합니다. 프록시·사내 CA는 `proxy`/`caInfo`/`caPath` 옵션으로 설정합니다. Guzzle은 요청마다 `allow_redirects: false`, `auth: null`, `cookies: false`, `http_errors: false`로 보내고, 리다이렉트·인증·쿠키 cURL 옵션(`CURLOPT_FOLLOWLOCATION`, `CURLOPT_HTTPAUTH`, `CURLOPT_USERPWD`, `CURLOPT_COOKIE*` 등)이나 기본(`http_errors`/`allow_redirects`/`cookies`/`prepare_body`) 외 미들웨어가 있는 handler는 설정 오류로 거부합니다(응답은 압축 해제 후 16MiB에서 끊는 sink로 받음). Symfony `Psr18Client`는 `max_redirects: 0`과 자체 인증 해제로 감싸고, `RetryableHttpClient`가 들어 있으면 거부합니다. 그 밖의 PSR-18 클라이언트는 SDK가 점검할 수 없으므로 **기본적으로 거부**합니다. `trustHttpClient: true`로 받을 수는 있지만, 그 클라이언트는 리다이렉트·재시도·자체 인증을 하면 안 되며 중복 발송과 데이터(키·전화번호) 유출 위험은 사용자 책임입니다. 사용자 클라이언트의 TLS 설정은 그대로 적용됩니다(SDK 자체에는 TLS 검증을 끄는 옵션이 없음).
  리다이렉트·자체 인증·재시도를 하는 클라이언트는 401이나 3xx 뒤 요청(발송 포함, 전화번호 포함)을 다른 인증 정보나 다른 주소로, 또는 여러 번 다시 보낼 수 있습니다(Java·C#·PHP 검토에서 재현). SDK까지 온 3xx 응답은 따르지도 재시도하지도 않습니다.
- 내장 cURL 전송은 `CURLOPT_FOLLOWLOCATION`을 끄고(`MAXREDIRS 0`), `CURLOPT_HTTPAUTH`·`USERPWD`·쿠키 옵션을 쓰지 않습니다. 응답은 **압축 해제 후** 16MiB까지만 읽습니다(gzip 폭탄 방지). JSON은 64단계 중첩까지만 받습니다.
- `Authorization`, `User-Agent`, `X-Bizgo-Client`는 요청마다 SDK가 마지막에 설정하므로 헤더 파라미터로 바꿀 수 없습니다. 헤더 파라미터 값은 출력 가능한 ASCII 한 줄만 받습니다(CR/LF 인젝션 방지). `appInfo`는 `[A-Za-z0-9._-]`·`[A-Za-z0-9._+-]`만 받습니다.
- 상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). SDK는 본문 검사(1MB, JSON 64단계, 형식)만 하며 webhook secret 없이 `WebhookReceiver::parseCounsel*()`로 받을 수 있습니다. 상담톡 엔드포인트도 HTTPS, 비즈고 웹훅 발신 IP 허용 목록, `msgKey` 기준 중복 제거를 권장합니다. 리포트·MO 웹훅은 항상 서명이 필요합니다.
- 클라이언트 속도 제한은 프로세스 안에서만 동작합니다(PHP-FPM 워커끼리 공유하지 않음). 한도 초과(429)는 서버가 판정하며 SDK는 재시도만 합니다.
- 모델·결과·예외의 `var_dump`/`print_r`/문자열 변환은 전화번호를 가리고 본문·개인정보를 숨깁니다. hooks와 `Bizgo\Testing` 기록에는 경로 템플릿·상태만 있고 헤더 값·키는 없습니다.

## 공급망

- 런타임 의존성은 코드가 없는 PHP-FIG 인터페이스 패키지 4개(`psr/http-client`, `psr/http-factory`, `psr/http-message`, `psr/log`)뿐입니다.
- Composer 플러그인은 허용하지 않습니다(`allow-plugins: false`). 개발 도구 버전은 `composer.lock`으로 고정하고 CI에서 `composer audit`로 검사합니다.
- Packagist 배포: Packagist의 자동 GitHub webhook은 끄고, 릴리스 워크플로가 검증(테스트·생성 코드·감사)과 GitHub Release가 성공한 뒤에만 Packagist 업데이트 API를 호출합니다. 토큰(`PACKAGIST_API_TOKEN`)은 필수 승인자가 있는 `release` 환경의 secret으로만 두고 저장소에 커밋하지 않습니다. `v*` 태그는 보호 규칙으로 관리자만 만들고 수정·삭제할 수 없게 합니다(설정 방법은 `.github/workflows/release.yml` 상단). 배포 아카이브에는 `tests/`, `spec/`, `bin/`, `examples/`, `.github/`가 포함되지 않습니다.
- 모든 커밋과 PR은 gitleaks로 검사합니다.
