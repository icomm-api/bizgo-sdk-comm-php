# bizgo-sdk-comm (PHP)

[비즈고(Bizgo)](https://bizgo.io) 커뮤니케이션 API의 PHP SDK입니다.
SMS/LMS/MMS, 국제문자, RCS, 카카오 알림톡·브랜드메시지, 네이버 톡톡을 하나의 클라이언트로 발송하고, 리포트·이력·통계를 조회하고, 웹훅을 검증합니다.
예약 발송, 발신프로필·템플릿 관리, 인사이트, 상담톡 등 **나머지 API 전체**(스펙의 operation 146개)도 스펙에서 생성한 메서드로 제공하며, 대량 발송·클라이언트 속도 제한·테스트 도구·관측 hook을 포함합니다.

- **PHP 8.2 이상** · 네임스페이스 `Bizgo\` · 불변(readonly) 모델과 named arguments · PHPStan max 레벨
- 요청 모델은 보내기 전에 검증합니다: 필수 필드, 오타 필드, **바이트 길이**(SMS 90byte 등), EUC-KR 범위 밖 문자(이모지), 수신자 200명, 발송 방식별 조건부 필수(알림톡 전문 발송의 `msgType`·`text` 등)
- 런타임 의존성은 PHP-FIG 인터페이스 패키지(`psr/http-client`, `psr/http-factory`, `psr/http-message`, `psr/log`)뿐입니다. HTTP는 내장 cURL 전송(프록시·사내 CA 옵션 포함)을 쓰고, Guzzle·Symfony HttpClient는 SDK가 안전한 옵션을 강제해 받습니다.
- 스펙: [bizgo-api-spec](https://github.com/icomm-api/bizgo-api-spec) (OpenAPI 3.1) · 원문: [API 레퍼런스](https://developers.bizgo.io/api-sdk/api-reference)

> 이전 `infobank/infobank-omni-api-php7-sdk` 또는 `infobank/infobank-omni-api-php-sdk`(PHP 5)를 쓰고 있다면 [이전 SDK에서 옮기기](#이전-sdk에서-옮기기)를 참고하세요.

> **PHP 8.1 이하는 지원하지 않습니다.** PHP 8.1은 2025-12-31에 보안 지원이 끝났고(7.x·5.x는 그보다 먼저), 더 이상 보안 수정을 받지 못하는 런타임에서 API Key를 다루는 코드를 돌리지 않도록 최소 버전을 8.2로 정했습니다. PHP 버전 지원 일정은 [php.net/supported-versions](https://www.php.net/supported-versions.php)를 참고하세요.

## 설치

```bash
composer require icomm-api/bizgo-sdk-comm:^1.2
```

필요한 확장: `curl`, `json`, `mbstring` (대부분의 PHP 배포판에 기본 포함).

## 시작하기

1. 콘솔 `발송관리 > 연동관리`에서 **API Key**를 발급하고, 호출할 서버의 **공인 IP를 등록**합니다.
2. 키를 환경변수로 설정합니다. 키는 코드나 저장소에 쓰지 않습니다.

```bash
export BIZGO_API_KEY=...
```

3. sandbox(실제 발송 없음)에서 먼저 확인합니다.

```php
use Bizgo\BizgoClient;
use Bizgo\Environment;

$client = new BizgoClient(environment: Environment::Sandbox); // 키는 BIZGO_API_KEY에서 읽음

$result = $client->send->sms(to: '01000000000', from: '01000000000', text: '[비즈고] 인증번호는 123456 입니다.');

$result->msgKeys(); // 접수된 메시지 키
$result->failed();  // 접수 단계에서 거절된 수신자 (없으면 [])
$result->duplicates(); // 같은 idempotencyKey로 이미 접수된 수신자(수신자별 A301, 다시 발송되지 않음)
```

운영에 보낼 때는 `environment`를 생략하거나 `Environment::Production`을 씁니다.

> **접수 ≠ 발송 완료.** `send->*`의 결과는 접수 결과입니다. 최종 결과는 [리포트](#리포트)로 받습니다.

## 발송

모든 채널은 `$client->send->omni()` 하나로 보냅니다. `messages`에 여러 개를 넣으면 **앞 메시지가 실패할 때 다음 메시지로 대체발송**됩니다.

```php
use Bizgo\Model\AlimtalkMessage;
use Bizgo\Model\Destination;
use Bizgo\Model\SmsMessage;

$result = $client->send->omni(
    to: [new Destination(to: '01000000000', replaceWords: ['name' => '홍길동'])], // 최대 200명
    messages: [
        new AlimtalkMessage(senderKey: '...', templateCode: '...', msgType: 'AT', text: '#{name}님, 주문이 접수되었습니다.'),
        new SmsMessage(from: '01000000000', text: '#{name}님, 주문이 접수되었습니다.'), // 알림톡 실패 시
    ],
    idempotencyKey: 'order-20260923-0001', // 권장: 재시도해도 중복 발송되지 않음 (idempotencyTtl 기본 86400초)
    ref: 'order-20260923-0001',            // 리포트에 그대로 돌아오는 참조값
);
```

`idempotencyKey`가 있으면 `idempotencyTtl`(0~86400초)의 기본값은 86400(`Send::DEFAULT_IDEMPOTENCY_TTL`)입니다. 비즈고는 키만 있고 TTL이 없는 요청을 A309로 거절하므로, TTL을 비워 두면 SDK가 86400을 채워 보냅니다. 직접 지정한 값(0 포함)은 그대로 보내고, 키가 없으면 TTL을 추가하지 않습니다. `send->request()`에 넘긴 모델·배열, `send->bulk()`의 청크, 본문에 두 필드가 모두 있는 생성 메서드에 모두 적용되며 넘긴 모델은 바꾸지 않습니다.

| 채널 | 모델 (`Bizgo\Model\`) | 간편 메서드 |
|---|---|---|
| SMS (90byte) | `SmsMessage` | `send->sms()` |
| LMS (2,000byte) | `MmsMessage` (`fileKey` 없음) | `send->lms()` |
| MMS | `MmsMessage` (`fileKey` 최대 3개) | `send->mms()` |
| 국제문자 | `InternationalMessage` | |
| RCS | `RcsMessage` | |
| 카카오 알림톡 | `AlimtalkMessage` | |
| 카카오 브랜드메시지 | `BrandMessage` | |
| 네이버 톡톡 | `NaverTalkMessage` | |

- 모델은 `final readonly` 클래스이고 named arguments로 만듭니다. 필드 이름은 API 이름(`senderKey`, `from`) 그대로입니다.
- 만들 때 검증하므로 잘못된 값은 보내기 전에 `Bizgo\Exception\ValidationException`이 납니다. 오류에는 **필드 경로와 이유만** 담기고 입력값(전화번호 등)은 담기지 않습니다.
- **조건부 필수**(스펙의 `x-sdk-required-if`): 다른 필드 값에 따라 필수가 되는 필드도 보내기 전에 검사합니다. 예: 알림톡 전문 발송(`sendType` 없음)은 `msgType`(`AT` 텍스트형, `AI` 이미지형)과 `text`가 필수이고(빠지면 서버가 A523으로 거절), 템플릿 자동 치환 발송(`sendType: 'template'`)은 `msgType`·`text` 없이 모든 `destinations[].replaceWords`가 필수입니다. 템플릿의 `msgType`은 `$client->alimtalk->templates->get(...)->msgType`으로 받아 그대로 쓸 수 있습니다. 그 밖에 브랜드메시지 `sendType`별 필드, 버튼 `WL`의 `urlPc`·`urlMobile`, RCS `header: '1'`의 `footer`, 상담톡 메시지 타입별 첨부 등이 있습니다. 요청 전체를 봐야 하는 규칙(`destinations[]`)은 `SendOmniRequest`·`ReservationCreateRequest`를 만들 때(`send->omni()`/`request()`/`bulk()`, `reservations->create()`) 검사합니다.
- 배열로 만들 수도 있습니다: `SmsMessage::fromArray([...])`, `$client->send->request([...])`. 이때 **모델에 없는 키(오타)는 거절**합니다.
- `toArray()`는 설정한 필드만 API 이름으로 돌려줍니다(스펙의 기본값을 임의로 보내지 않음).

### 이미지

```php
use Bizgo\Upload;

$uploaded = $client->files->uploadMms('banner.jpg'); // jpg, 최대 300KB (넘으면 보내기 전 오류)
if ($uploaded->fileKey !== null) {
    $client->send->mms(to: '01000000000', from: '01000000000', text: '...', fileKeys: [$uploaded->fileKey]);
}

$client->files->uploadRcs('card.png');                                  // → ->media
$client->files->uploadBrandMessage('wide.jpg', kind: 'wide');           // → ->imgUrl
$client->files->uploadMms(Upload::fromContents($bytes, 'banner.jpg'));  // 이미 가진 바이트
```

파일 경로는 로컬 경로만 받습니다(`http://`, `phar://` 같은 스트림 래퍼 거부). 파일은 한 번 읽어 재시도에 같은 바이트를 씁니다.

## 리포트

콘솔에서 API Key별로 리포트 수신 방식(POLLING 또는 WEBHOOK)을 정합니다.

**Polling** — 처리에 성공한 배치만 수신 확인(ack)합니다. 처리 함수가 예외를 던지면 같은 배치를 다시 받습니다.

```php
$client->reports->consume(function (array $reports) use ($db): void {
    foreach ($reports as $report) {
        $db->upsert($report->msgKey, $report->reportCode); // 같은 리포트가 다시 올 수 있으니 upsert
    }
});
```

**Webhook** — 서명을 검증하고 5초 안에 `{"msgKey": ...}`로 응답합니다. 웹훅 secret은 비즈고에 요청해 별도로 받습니다.

```php
use Bizgo\Exception\WebhookPayloadException;
use Bizgo\Exception\WebhookVerificationException;
use Bizgo\Webhook\WebhookReceiver;

// secret이 없으면 빈 문자열 -> WebhookVerificationException(설정 오류)으로 바로 알 수 있음
$receiver = new WebhookReceiver((string) getenv('BIZGO_WEBHOOK_SECRET'));

try {
    // PSR-7 요청, getallheaders(), $_SERVER 모두 가능
    $report = $receiver->report($_SERVER, (string) file_get_contents('php://input'));
} catch (WebhookPayloadException) {
    http_response_code(400); // 본문 형식 오류
    exit;
} catch (WebhookVerificationException) {
    http_response_code(401); // 서명·timestamp 오류
    exit;
}
$db->upsert($report->msgKey, $report->reportCode);           // 같은 리포트가 다시 올 수 있으니 upsert (무거운 처리는 비동기로)
echo json_encode(WebhookReceiver::ack($report->msgKey));
```

> 웹훅 서명과 timestamp 허용 오차를 검증하세요. 운영 환경에서는 HTTPS, 비즈고 웹훅 발신 IP 허용 목록, `msgKey` 기준 중복 제거를 함께 적용하고, 중요한 판단은 리포트·상태 조회 API로 결과를 확인하세요.
> 서명 비교는 `hash_equals`(constant-time)이고, timestamp 허용 오차는 기본 300초입니다.
> 본문이 JSON이 아니거나 너무 크거나(1MB) 깊거나(64단계) 필드 형식이 다르면 `WebhookPayloadException`(`WebhookVerificationException`의 하위 클래스, 400으로 응답)입니다.

**개별 조회** — `$client->reports->inquiry($msgKey)` (30일 이내)

## 조회

```php
use DateTimeImmutable;
use DateTimeZone;

$client->messages->status($msgKey);                 // 단건 상태 (대체발송 시 채널별 항목)
$client->messages->statusByRequestId($requestId);   // 동보 요청 전체 (msgKey에서 끝 3자리를 뺀 값)
$client->messages->statistics('20260901', '20260923', serviceType: 'SMS');

$kst = new DateTimeZone('Asia/Seoul');
foreach ($client->messages->iterHistory(new DateTimeImmutable('2026-09-23 09:00', $kst), serviceType: ['SMS', 'ALIMTALK']) as $m) {
    // 페이지(lastSeq)를 자동으로 따라가는 generator
}
foreach ($client->messages->iterMoHistory(new DateTimeImmutable('2026-09-23 09:00', $kst)) as $mo) {
    // MO(수신) 이력
}
```

- `DateTimeInterface`는 Asia/Seoul(KST)로 변환해 보냅니다. PHP의 `DateTime`은 항상 시간대를 가지며, 시간대 없이 만들면 `date.timezone` 설정을 따릅니다. 한국 시각을 뜻한다면 위처럼 `Asia/Seoul`을 지정하거나 문자열(`2026-09-23T09:00:00`)을 넘기세요.
- `limit`는 1~1000이며 범위 밖이면 보내기 전 오류입니다.
- 조회 API의 기본 호출 한도는 초당 5회입니다.
- 응답 모델에 스펙에 없는 새 필드가 오면 `$model->extra`에 그대로 담깁니다.

## 전체 API (스펙에서 생성)

P0 편의 메서드(`send`·`files`·`reports`·`messages`) 밖의 모든 API(스펙의 operation 146개 전체)는 [bizgo-api-spec](https://github.com/icomm-api/bizgo-api-spec)의 `x-sdk-*` 메타데이터로 **생성한 메서드**로 제공합니다. 위치는 `$client-><리소스>-><메서드>()`이고 점으로 구분된 리소스는 중첩 객체입니다.

| 리소스 | 예 |
|---|---|
| 예약 발송 | `$client->reservations->create()`/`list()`/`get()`/`update()`/`cancel()`/`pause()`/`resume()`, `->recipients->create()`/`list()`/`delete()` |
| 인사이트 | `$client->insights->alimtalk->get()`, `->brandMessage->getHourly()`, `->rcs->getMessage()` |
| 카카오 발신프로필 | `$client->kakao->senders->requestToken()`/`create()`/`list()`, `->groups`, `->categories`, `->sanctions` |
| 알림톡 템플릿 | `$client->alimtalk->templates->list()`/`get()`/`create()`/`requestInspection()`, `->templateCategories`, `->publicTemplates` |
| 브랜드메시지 | `$client->brandMessage->templates`, `->groupSends`, `->audience`, `->friendGroups`, `->videos`, `->groupTags` 등 |
| RCS | `$client->rcs->brands`, `->chatbots`, `->templates`, `->templateForms`, `->templateImages`, `->commonFormats` |
| 상담톡 | `$client->counsel->messages->sendPlain()`/`sendRich()`, `->sessions`, `->users`, `->files`, `->channels` 등 |
| 파일 | `$client->files->uploadBrandMessageCatalog()`, `uploadAlimtalkTemplateImage()` 등 (P0 업로드 메서드와 같은 객체) |

```php
use Bizgo\Model\ReservationCreateRequest;

// 요청 본문: 모델(보내기 전 검증) 또는 API 이름의 배열(모르는 키는 거절)
$reservation = $client->reservations->create(ReservationCreateRequest::fromArray([
    'destinations' => [['to' => '01000000000']],
    'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => '예약 안내']]],
    'resvSendTime' => '2026-12-01 10:00:00',
]));
echo $reservation->resvKey; // x-sdk-result: data -> resvKey와 수신자별 결과(data)를 함께 돌려줌

// 경로 -> 본문 -> 쿼리 순서의 named arguments. 한 페이지 메서드와 전체 순회 generator(iter<Method>)
$page = $client->alimtalk->templates->list(senderKey: 'SENDER_KEY_EXAMPLE', inspectionStatus: 'APR', limit: 100);
foreach ($client->alimtalk->templates->iterList(senderKey: 'SENDER_KEY_EXAMPLE') as $template) {
    // 문서에 적힌 방식(offset·page·cursor) 그대로 다음 페이지를 가져오고, 빈 페이지·짧은 페이지·total·hasNext에서 멈춤
}

// multipart: 파일은 로컬 경로·스트림·Upload, JSON 파트는 모델 또는 배열
$client->rcs->templateImages->upload(brandId: 'BRAND_ID_EXAMPLE', file: 'card.png');
```

- 반환값은 스펙의 `x-sdk-result`(기본 `data.data`) 부분의 응답 모델입니다. 서버가 그 부분을 보내지 않아도 **빈 모델·빈 배열**을 돌려주므로 null 검사 없이 필드에 접근할 수 있습니다(값이 없으면 필드가 null). 돌려줄 데이터가 없는 API(삭제 등)는 `void`입니다.
- JSON과 multipart를 모두 받는 operation은 JSON 메서드와 파일을 보내는 `<메서드>WithFiles()`가 함께 있습니다(예: `alimtalk->templates->requestInspectionWithFiles(senderKey:, templateCode:, comment:, attachment: [...])`).
- 재시도는 `x-sdk-retry`를 따릅니다: 조회와 같은 결과를 내는 수정·삭제는 429·5xx·네트워크 오류, 생성·발송은 429만.
- 요청과 응답에 함께 쓰이는 스키마(예: `AlimtalkButton`)는 요청 모델이 `Bizgo\Model\`, 관대하게 파싱하는 응답 모델이 `Bizgo\Model\Response\`에 있습니다.
- 편의 메서드와 이름이 같으면 편의 메서드가 우선합니다(`send->omni`, `files->uploadMms` 등). 생성 코드는 CI에서 `php bin/generate-models.php --check`로 스펙과 비교합니다.

## 대량 발송

수신자 수 제한 없이 한 메시지를 보냅니다. `chunkSize`(1~200)씩 나눠 `send->omni()`를 호출하고, 한 청크가 실패해도 나머지는 계속 보냅니다.

```php
use Bizgo\Model\SmsMessage;

$result = $client->send->bulk(
    to: $numbers,                              // 문자열·Destination·배열 목록 (개수 제한 없음)
    messages: [new SmsMessage(from: '01000000000', text: '[비즈고] 점검 안내')],
    chunkSize: 200,
    idempotencyKeyPrefix: 'campaign-2026-09',  // 권장: 같은 목록·같은 chunkSize로 재실행해도 중복 발송 없음
);
foreach ($result->errors as $error) {
    // $error->chunkIndex, $error->start, $error->end(제외), $error->error - 수신번호는 담지 않음(인덱스만)
}
$result->msgKeys();
$result->failed();
$result->duplicates(); // 재실행 시 이미 접수된 수신자
```

- 청크마다 `idempotencyTtl`(지정하지 않으면 86400)을 함께 보냅니다.
- 청크 멱등성 키는 `<prefix>-<chunkSize>-<시작 인덱스>-<hash8>`입니다(`hash8` = 청크 수신번호를 순서대로 줄바꿈으로 이은 UTF-8 문자열의 SHA-256 앞 8자리). 모든 언어 SDK가 같은 키를 만듭니다.
- **재실행은 같은 목록·같은 `chunkSize`로** 하세요. 이미 접수된 청크는 다시 발송되지 않고, 그 수신자는 `duplicates()`에 모입니다(수신자별 A301; `failed()`·`errors`에 들어가지 않으며 모두 A301인 청크도 오류가 아님). 요청 단위 A301은 전처럼 `DuplicateRequestException`으로 `errors`에 들어갑니다. 실패한 청크만 다시 보내려면 `errors`의 청크 번호(인덱스 범위)를 쓰세요.
- 보내기 전에 모든 청크를 검증합니다. 하나라도 잘못되면 아무것도 보내지 않고 `ValidationException`(경로 `to[<인덱스>]`)을 냅니다.
- PHP는 청크를 차례로 보냅니다(`concurrency`는 다른 언어와 같은 인자를 받기 위한 상한). 설정 오류나 PHP `Error`로 멈추면 `BulkSendException::$partial`에 이미 접수된 청크 결과가 담깁니다.

## 속도 제한 (클라이언트, 프로세스 단위)

클라이언트 인스턴스마다 토큰 버킷 2개가 기본으로 켜져 있습니다.

| 버킷 | 기본 한도 | 비용 | 대상 |
|---|---|---|---|
| send | 초당 **200 메시지** | 요청의 `destinations` 수(최소 1) | 스펙의 `x-sdk-rate: send` operation: `sendOmni`(`send->*`, `bulk`), `createReservation`, `addReservationRecipients`, `createBrandMessageGroupSend`, `sendCounselPlain`, `sendCounselRich` |
| other | 초당 5 요청 | 요청마다 1 | 그 밖의 모든 API |

```php
use Bizgo\BizgoClient;
use Bizgo\RateLimit;

new BizgoClient(rateLimit: new RateLimit(send: 100, other: 5)); // 한도 변경
new BizgoClient(rateLimit: null);                             // 끄기
```

- 버킷은 가득 찬 상태(1초 분량)로 시작합니다(서버도 토큰 버킷). 재시도를 포함한 **모든 시도 전에** 필요한 만큼 `usleep`으로 기다립니다. 한도보다 큰 요청(예: 한도 100에 200명)은 버킷이 찰 때까지 기다린 뒤 보내고 부족분은 빚으로 남깁니다.
- **이 제한은 한 PHP 프로세스 안의 한 클라이언트에만 적용됩니다.** PHP-FPM 워커, 큐 워커, 여러 서버는 버킷을 공유하지 않습니다. 비즈고 한도는 계정 단위이므로 여러 프로세스가 같은 키를 쓰면 프로세스별 한도를 낮추거나 따로 조율하세요. 429는 자동 재시도가 계속 처리합니다.

## 상담톡 웹훅

상담톡 이벤트(`counselMessage`, `counselReference`, `counselExpiredSession`, `counselSeenInfo`, `counselPersonalInfo`, `counselCertResult`, `counselResult`)는 `WebhookReceiver`의 정적 메서드 `parseCounselMessage()` 등(`parse` + 이벤트 이름)으로 받습니다. 상담톡 웹훅에는 서명이 없으므로(서명은 리포트·MO 웹훅에만 적용) **webhook secret이 필요 없습니다.** 응답은 `{"code": "A000", "result": "Success"}`입니다(리포트·MO와 다름).

```php
use Bizgo\Webhook\WebhookReceiver;

$event = WebhookReceiver::parseCounselMessage((string) file_get_contents('php://input')); // secret 없이 사용
echo json_encode(WebhookReceiver::counselAck());
```

- 리포트·MO도 받는 엔드포인트라면 이미 만든 `WebhookReceiver`의 인스턴스 메서드(`$receiver->counselMessage($headers, $body)` 등)를 써도 됩니다. 결과는 같습니다: 서명을 요구하거나 검사하지 않고 서명 헤더가 와도 무시합니다.
- 본문은 리포트 웹훅과 같이 검사합니다(최대 1MB, JSON 중첩 64단계, 필드 형식). 맞지 않으면 `WebhookPayloadException`(HTTP 400으로 응답)입니다.
- 운영 시 권장: HTTPS로 제공하고 비즈고 웹훅 발신 IP만 허용하며, `msgKey`가 있으면 그 기준으로 중복을 제거하세요(재전송될 수 있음).
- 리포트·MO 웹훅은 항상 서명이 필요합니다.
- 본문에는 최종 사용자 식별자·상담 내용·개인정보(전화번호·닉네임)가 들어 있습니다. 로그에 남기지 마세요(모델의 `var_dump`·문자열 변환에서는 가려집니다).

## 오류 처리

```php
use Bizgo\Exception;
use Bizgo\Model\SmsMessage;

try {
    $client->send->omni(to: '01000000000', messages: [new SmsMessage(from: '01000000000', text: '안내')]);
} catch (Exception\ValidationException $e) {       // 보내기 전 검증 실패. $e->errors: [['path' => ..., 'reason' => ...]]
} catch (Exception\AuthenticationException $e) {   // 키가 틀렸거나 IP가 등록되지 않음
} catch (Exception\RateLimitException $e) {        // 자동 재시도 후에도 한도 초과. $e->retryAfter
} catch (Exception\DuplicateRequestException $e) { // 요청 단위 A301: 같은 idempotencyKey가 이미 접수됨 (다시 발송되지 않음)
} catch (Exception\ApiException $e) {              // 그 밖의 거절. $e->errorCode, $e->layer, $e->description, $e->trackingId
} catch (Exception\ConnectionException $e) {       // 네트워크 오류(TimeoutException 포함). 발송은 접수됐을 수도 있음 → 상태 조회로 확인
}
```

- 모든 SDK 예외는 `Bizgo\Exception\BizgoException` 인터페이스를 구현합니다.
- 응답은 두 단계로 판정합니다: `common.authCode`(게이트웨이: 인증·형식) → `data.code`(상품 처리). `$e->layer`가 `gateway`/`service`입니다. 같은 코드라도 단계마다 뜻이 다릅니다(예: service `A401`은 paymentCode 오류).
- `$e->getMessage()` 형식: `HTTP 400 | service code=A306 | <result> | <설명> | infobankTrId=...`. 비즈고 코드는 `$e->errorCode`입니다(`\Exception::$code`와 겹치지 않도록). `getCode()`는 HTTP 상태를 돌려줍니다.
- 발송 요청이 성공해도 **일부 수신자는 거절될 수 있습니다.** 항상 `$result->failed()`를 확인하세요.
- 같은 `idempotencyKey`로 유효시간 안에 다시 보내면 요청은 성공(HTTP 200)하고 수신자별 코드가 `A301`입니다. SDK는 이 수신자를 `failed()`가 아닌 `$result->duplicates()`로 돌려줍니다(이전에 이미 접수됨, `succeeded()`·`msgKeys()`에도 없음). 그래서 같은 키로 재시도해도 안전합니다(2026-09-28 sandbox 확인).

## 재시도와 타임아웃

| 요청 | 자동 재시도 |
|---|---|
| 조회, 리포트, ack | 429, 500/502/503/504, 네트워크 오류 |
| 발송 (`idempotencyKey` 있음) | 429, 500/502/503/504, 네트워크 오류 |
| 발송 (`idempotencyKey` 없음), 업로드 | 429만 (중복 발송 방지) |

기본값은 최대 2회 재시도(지수 백오프 0.5·1·2…초 ±25%, 최대 8초, `Retry-After` 우선·최대 60초), 타임아웃 30초(연결 5초)입니다.
자동 재시도(타임아웃·5xx·429 뒤)한 발송이 `A301`을 받으면 `alreadyAccepted = true`와 "이전 시도가 이미 접수됨" 안내가 담긴 `DuplicateRequestException`을 냅니다(메시지는 한 번만 나감). 리다이렉트(3xx)는 따르지도 재시도하지도 않고 `InvalidResponseException`(`HTTP <상태>`)입니다.

```php
use Bizgo\AppInfo;
use Bizgo\BizgoClient;

new BizgoClient(maxRetries: 3, timeout: 10.0, connectTimeout: 3.0);

// 프록시·사내 CA는 내장 cURL 전송의 옵션으로 (TLS 검증은 항상 켜져 있음)
new BizgoClient(proxy: 'http://proxy.example:3128', caInfo: '/etc/ssl/company-ca.pem');

// Guzzle·Symfony HttpClient도 받습니다: SDK가 리다이렉트·자체 인증·쿠키를 끄고 요청을 보냅니다.
// 재시도·리다이렉트 미들웨어가 있는 Guzzle, RetryableHttpClient, 그 밖의 PSR-18 클라이언트는 거부합니다(trustHttpClient 참고).
new BizgoClient(httpClient: new \GuzzleHttp\Client(['proxy' => 'http://proxy.example:3128']));

// 로그: PSR-3 로거에 시도마다 한 줄 (debug)
new BizgoClient(logger: $logger); // "POST /api/comm/v1/send/omni -> 200 (85 ms, attempt 1)" (경로 템플릿만)

// 앱 식별: User-Agent 끝에 app/<name>-<version> (영문·숫자·._-+만, 개인정보 금지)
new BizgoClient(appInfo: new AppInfo('myshop', '1.4.2'));
```

내장 cURL 전송은 `https_proxy`/`no_proxy` 환경변수도 따르고, 한 클라이언트 안에서 cURL 연결을 재사용합니다(대량 발송에 유리). Guzzle·Symfony 클라이언트를 쓰면 타임아웃은 그 클라이언트 설정을 따릅니다.

## 테스트 도구 (`Bizgo\Testing`)

네트워크와 API Key 없이 **여러분의 코드**를 테스트할 수 있게 패키지에 포함되어 있습니다(쓸 때만 로드되며 운영 코드에는 필요 없음).

```php
use Bizgo\Testing\FakeTransport;
use Bizgo\Webhook\WebhookReceiver;

$fake = new FakeTransport();
$client = $fake->client();   // test-api-key-not-real, sandbox, 속도 제한 끔, 실제 대기 없음

$client->send->sms(to: '01000000000', from: '01000000000', text: 'hello'); // 기본: 수신자마다 A000 + 가짜 msgKey
$fake->lastRequest()->operationId;  // 'sendOmni'
$fake->lastRequest()->json;         // 보냈을 JSON 본문 (헤더는 이름만 기록, 값과 키는 기록하지 않음)

$fake->on('sendOmni')->fail('service', 200, 'A020');                  // 오류 주입 -> RateLimitException
$fake->on('sendOmni')->sendResult('A000', 'A306');                    // 두 번째 수신자 거절
$fake->on('getReportPolling')->fail('gateway', 401, 'A401');
$fake->on('GET', '/api/comm/v1/report/inquiry/{msgKey}')->data(['report' => []]);
$fake->on('getMessageStatusByMsgKey')->networkError();                // 또는 ->timeout()

// 웹훅 테스트 요청: X-IB-Timestamp·X-IB-Signature 헤더와 본문 (리포트의 필수 필드를 모두 넣으세요)
$signed = FakeTransport::signWebhook('test-webhook-secret', [
    'msgKey' => 'KEY001', 'serviceType' => 'SMS', 'reportTime' => '2026-09-23T09:00:00', 'reportType' => 'RS', 'reportCode' => '10000',
]);
$report = (new WebhookReceiver('test-webhook-secret'))->report($signed->headers, $signed->body);
```

기본 응답은 operation별 성공 봉투입니다: 발송(`sendOmni`, `createReservation`, `addReservationRecipients`)은 수신자마다 `A000`과 가짜 `msgKey`(`createReservation`은 가짜 `resvKey`도), MMS/RCS 업로드는 가짜 `fileKey`/`media`, 그 밖은 `data.data = {}`. 스펙에 없는 경로는 404입니다.

## 관측 (hooks, OpenTelemetry)

```php
use Bizgo\BizgoClient;
use Bizgo\Observability\Hook;
use Bizgo\Observability\RequestEvent;

final class Metrics implements Hook
{
    public function onRequestStart(RequestEvent $event): void
    {
    }

    public function onRequestEnd(RequestEvent $event): void
    {
        // $event->operationId, ->operation(`alimtalk.templates.list`), ->method, ->pathTemplate,
        // ->status, ->layer, ->code, ->errorType, ->success, ->attempts, ->durationMs
    }
}

$client = new BizgoClient(hooks: [new Metrics()]);
```

- 호출(재시도 포함) 한 번에 start/end가 한 번씩 불립니다. 이벤트에는 **경로 템플릿**(`/api/comm/v1/report/inquiry/{msgKey}`)만 있고 본문·쿼리·헤더 값·실제 경로 값·전화번호·키는 없습니다.
- hook이나 logger가 던진 예외는 SDK가 삼키고(클래스마다 경고 한 번) 결과·재시도·대량 발송 결과에 영향을 주지 않습니다. 파싱에 실패한 응답은 성공으로 보고하지 않습니다.
- OpenTelemetry: `composer require open-telemetry/api`(SDK는 suggest만 합니다) 후 `OpenTelemetryHook::create()`를 hooks에 넣습니다. 설치되지 않았으면 `null`을 돌려줍니다. span 이름 `bizgo <resource>.<method>`, 속성 `http.request.method`, `url.template`, `http.response.status_code`, `bizgo.code`, `bizgo.layer`, `bizgo.retry_count`.

```php
use Bizgo\BizgoClient;
use Bizgo\Observability\OpenTelemetryHook;

$otel = OpenTelemetryHook::create(); // Globals::tracerProvider()의 'bizgo' tracer
$client = new BizgoClient(hooks: $otel === null ? [] : [$otel]);
```

## 보안

- API Key는 **접두어 없이** 키 값만 씁니다(`Bearer`/`ApiKey` 접두어는 401). 공백·줄바꿈·비ASCII가 있으면 조용히 잘라내지 않고 설정 오류로 알립니다. 환경변수나 시크릿 저장소에서 읽고, 코드·저장소·클라이언트 앱에 넣지 않습니다.
- 키는 `#[\SensitiveParameter]`로 스택 트레이스에서 가려지고, `var_dump`/`print_r`/문자열 변환에 나오지 않으며, 클라이언트는 직렬화할 수 없습니다. 발송 메서드와 요청 모델의 인자(전화번호·본문)도 스택 트레이스에서 가려집니다.
- SDK는 키, 요청 본문, 쿼리 문자열, 전화번호를 로그나 예외 메시지에 남기지 않습니다. 로그는 `METHOD path -> status (ms, attempt n)`만 남깁니다. HTTP 라이브러리 예외는 URL을 담고 있어 원인(previous)으로 연결하지 않습니다.
- TLS 검증을 끄는 옵션은 없습니다. 리다이렉트는 따르지도 재시도하지도 않습니다(키와 본문이 base URL 밖으로 나가지 않음). 응답은 압축 해제 후 최대 16MiB까지만 읽습니다.
- **HTTP 클라이언트 정책** (SDK-DESIGN.md §12.6): 내장 cURL 전송을 권장합니다. 프록시·사내 CA는 `proxy`/`caInfo`/`caPath` 옵션으로 설정합니다. Guzzle은 요청마다 `allow_redirects: false`, `auth: null`, `cookies: false`, `http_errors: false`로 보내고, 리다이렉트·인증·쿠키 cURL 옵션(`CURLOPT_FOLLOWLOCATION`, `CURLOPT_HTTPAUTH`, `CURLOPT_USERPWD`, `CURLOPT_COOKIE*` 등)이나 기본(`http_errors`/`allow_redirects`/`cookies`/`prepare_body`) 외 미들웨어가 있는 handler는 설정 오류로 거부합니다(응답은 압축 해제 후 16MiB에서 끊는 sink로 받음). Symfony `Psr18Client`는 `max_redirects: 0`과 자체 인증 해제로 감싸고, `RetryableHttpClient`가 들어 있으면 거부합니다. 그 밖의 PSR-18 클라이언트는 SDK가 점검할 수 없으므로 **기본적으로 거부**합니다. `trustHttpClient: true`로 받을 수는 있지만, 그 클라이언트는 리다이렉트·재시도·자체 인증을 하면 안 되며 중복 발송과 데이터(키·전화번호) 유출 위험은 사용자 책임입니다. 사용자 클라이언트의 TLS 설정은 그대로 적용됩니다(SDK 자체에는 TLS 검증을 끄는 옵션이 없음).
- `http://` base URL과 사용자 정보·쿼리·프래그먼트가 있는 base URL은 거부합니다(테스트용 localhost http만 예외).
- 모든 요청에 `User-Agent: bizgo-sdk-comm-php/<버전> php/<버전> (<os>; <arch>)`와 `X-Bizgo-Client: bizgo-sdk-comm-php/<버전>`을 붙입니다(OS·아키텍처는 `linux`/`x64` 같은 대략의 값만, 호스트명·사용자명 없음). SDK가 따로 데이터를 보내는 기능은 없습니다. 이 두 헤더와 `Authorization`은 요청마다 SDK가 설정하며 헤더 파라미터로 바꿀 수 없습니다.
- 모델·결과의 `var_dump`/`print_r`/문자열 변환에서 전화번호는 가운데를 가리고(`010****0000`), 메시지 내용은 길이만, 이름·닉네임은 첫 글자만 보여 줍니다(속성 값 자체는 그대로). 치환 값(`replaceWords` 등)은 키와 길이만 보여 줍니다. API Key·웹훅 secret·프록시 자격 증명은 `var_export()`·배열 캐스트·`json_encode()`에도 나오지 않습니다. 모델의 `var_export()`는 가리지 않으니 로그에 쓰지 마세요.
- 업로드 파일을 읽지 못하면 PHP 경고 없이 `ValidationException`(파일 이름만, 전체 경로 없음)을 냅니다. 응답·웹훅 JSON은 64단계 중첩까지만 받습니다.
- `ApiException::$body`에는 응답 원문(전화번호 포함 가능)이 있으니 그대로 로그에 남기지 마세요(`var_dump`/`print_r`에는 나오지 않게 해 두었습니다).
- 취약점 신고는 [SECURITY.md](SECURITY.md)를 참고하세요.

## 이전 SDK에서 옮기기

`infobank/infobank-omni-api-php7-sdk`(PHP 7.2+)와 `infobank/infobank-omni-api-php-sdk`(PHP 5)는 더 이상 유지보수되지 않습니다. API·인증 방식이 다르므로 코드 호환은 없고, 아래처럼 옮깁니다.

```bash
composer remove infobank/infobank-omni-api-php7-sdk   # 또는 infobank/infobank-omni-api-php-sdk
composer require icomm-api/bizgo-sdk-comm:^1.2
```

| 이전 SDK (`Infobank\`) | bizgo-sdk-comm (`Bizgo\`) |
|---|---|
| `https://omni.ibapi.kr`, ID/PW로 토큰 발급·캐시 | `https://mars.ibapi.kr`, API Key 하나 (토큰 발급 없음) |
| `new InfobankClient($baseUrl, $token, $clientId, $password)` | `new BizgoClient()` (키는 `BIZGO_API_KEY`) |
| `$client->sendMessage(new SmsMessage(...))` 등 채널별 발송 | `$client->send->omni(to: ..., messages: [...])` 하나 (채널은 모델로 구분) |
| `Fallback` 객체 | `messages` 배열의 순서가 곧 대체발송 순서 |
| `registImgFile()` / `uploadImage()` | `$client->files->uploadMms()` / `uploadRcs()` / `uploadBrandMessage()` |
| `reportPollingGet()` + `reportPollingDel()` (`getReports()` + `deleteReports()`) | `$client->reports->consume($handler)` 또는 `poll()` + `ack()` |
| `registForm()` 등 Form API | 없음 (알림톡·브랜드메시지는 템플릿 자동 치환 발송 사용) |
| `{code, result, data}` 응답, 코드 직접 확인 | `{common, data}` 응답을 SDK가 풀고 실패는 예외로 |
| Guzzle·Monolog 의존 | PSR 인터페이스만 의존 (Guzzle·Monolog는 원하면 주입) |
| PHP 5 / 7.2+ | PHP 8.2+ |

이전 저장소의 예제에 있던 인증 정보·전화번호는 그대로 쓰지 마세요.

## 개발

```bash
composer install
vendor/bin/phpunit                     # 네트워크·키 없이 mock HTTP와 127.0.0.1 내장 서버로 실행
vendor/bin/phpstan analyse             # level max + strict rules
vendor/bin/phpcs                       # PSR-12
php bin/generate-models.php            # spec/openapi.yaml이 바뀌었을 때
php bin/generate-models.php --check    # 생성 결과가 스펙과 같은지 (CI)
composer audit
```

AI 코딩 도구로 이 저장소를 수정할 때의 규칙은 [AGENTS.md](AGENTS.md)에 있습니다.
SDK를 **사용하는** 코드를 AI로 작성할 때는 [llms.txt](llms.txt)를 컨텍스트로 넣으면 정확도가 높아집니다.

## 라이선스

[Apache-2.0](LICENSE)
