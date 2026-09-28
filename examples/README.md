# 예제

모든 예제는 **sandbox**(실제 발송 없음)에 연결하고, 값은 환경변수에서 읽습니다.

```bash
composer install
export BIZGO_API_KEY=...            # 콘솔 > 발송관리 > 연동관리 (코드에 쓰지 마세요)
export BIZGO_FROM=...               # 등록한 발신번호
export BIZGO_TO=...                 # 테스트 수신번호
php examples/send_sms.php
```

| 파일 | 내용 |
|---|---|
| `send_sms.php` | SMS 발송과 수신자별 접수 결과 확인 |
| `send_alimtalk_fallback.php` | 알림톡 발송, 실패 시 SMS 대체발송, 멱등성 키 (`BIZGO_KAKAO_SENDER_KEY`, `BIZGO_KAKAO_TEMPLATE_CODE`) |
| `send_mms.php` | 이미지 업로드 후 MMS 발송 (`BIZGO_IMAGE`: jpg, 300KB 이하) |
| `poll_reports.php` | 리포트 Polling 처리(처리 성공 시에만 수신 확인) |
| `message_history.php` | 발송 이력 전체 조회(페이지 자동 순회)와 상태 조회 |
| `webhook_endpoint.php` | 리포트 웹훅 수신(서명 검증, 중복 처리, 5초 안에 응답). `BIZGO_WEBHOOK_SECRET` 필요 |
| `bulk_send.php` | 대량 발송(200명씩 청크, 멱등성 키 접두어로 재실행 안전). `BIZGO_TO_LIST`: 쉼표로 구분한 테스트 번호 |
| `alimtalk_templates.php` | 스펙에서 생성한 API 예: 알림톡 템플릿 한 페이지와 전체 순회. `BIZGO_SENDER_KEY` 필요 |
| `counsel_webhook.php` | 상담톡 메시지 웹훅(서명 없음, webhook secret 불필요, `{"code":"A000","result":"Success"}` 응답) |

각 예제의 `run()`/`handle()` 함수는 테스트(`tests/ExamplesTest.php`)에서 mock HTTP로 실행되므로, 예제 코드는 항상 현재 SDK와 맞습니다.
