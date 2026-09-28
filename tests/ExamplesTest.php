<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Testing\FakeTransport;
use Bizgo\Upload;
use Bizgo\Webhook\WebhookReceiver;

use function Bizgo\Examples\AlimtalkTemplates\run as alimtalkTemplates;
use function Bizgo\Examples\BulkSend\run as bulkSend;
use function Bizgo\Examples\CounselWebhook\handle as counselWebhook;
use function Bizgo\Examples\MessageHistory\run as messageHistory;
use function Bizgo\Examples\PollReports\run as pollReports;
use function Bizgo\Examples\SendAlimtalkFallback\run as sendAlimtalkFallback;
use function Bizgo\Examples\SendMms\run as sendMms;
use function Bizgo\Examples\SendSms\run as sendSms;
use function Bizgo\Examples\WebhookEndpoint\handle as webhookHandle;

/** Runs every example against the mock HTTP client so the examples never drift from the SDK. */
final class ExamplesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $files = glob(__DIR__ . '/../examples/*.php');
        self::assertIsArray($files);
        foreach ($files as $file) {
            require_once $file;
        }
    }

    /** @return array<string, mixed> */
    private static function acceptedOne(): array
    {
        return self::envelope(['destinations' => [['to' => '01000000000', 'msgKey' => 'K001', 'code' => 'A000', 'result' => 'Success']]]);
    }

    private function capture(\Closure $run): string
    {
        ob_start();
        try {
            $run();
        } finally {
            $output = (string) ob_get_clean();
        }

        return $output;
    }

    public function testSendSms(): void
    {
        $this->http->push(self::reply(self::acceptedOne()));
        $output = $this->capture(fn () => sendSms($this->client, '01000000000', '01000000000'));
        self::assertStringContainsString('K001', $output);
        self::assertSame(1, $this->http->count());
    }

    public function testSendAlimtalkFallback(): void
    {
        $this->http->push(self::reply(self::acceptedOne()));
        $this->capture(fn () => sendAlimtalkFallback($this->client, '01000000000', '01000000000', 'SENDER_KEY_EXAMPLE', 'TEMPLATE_CODE_EXAMPLE'));
        $body = $this->http->lastJson();
        self::assertIsArray($this->http->lastAt('messageFlow.0.alimtalk'));
        self::assertIsArray($this->http->lastAt('messageFlow.1.sms'));
        self::assertIsString($body['idempotencyKey']);
        self::assertStringNotContainsString('01000000000', $body['idempotencyKey']); // the number is hashed
    }

    public function testSendAlimtalkFallbackHandlesDuplicates(): void
    {
        $this->http->push(self::reply(self::envelope(code: 'A301')));
        self::assertStringContainsString('이미 발송된', $this->capture(fn () => sendAlimtalkFallback($this->client, '01000000000', '01000000000', 'SENDER_KEY_EXAMPLE', 'TEMPLATE_CODE_EXAMPLE')));
    }

    public function testSendMms(): void
    {
        $this->http
            ->push(self::reply(self::envelope(['fileKey' => 'FILE_KEY_001'])))
            ->push(self::reply(self::acceptedOne()));
        $this->capture(fn () => sendMms($this->client, '01000000000', '01000000000', Upload::fromContents("\xff\xd8jpeg", 'banner.jpg')));
        self::assertSame(['FILE_KEY_001'], $this->http->lastAt('messageFlow.0.mms.fileKey'));
    }

    public function testPollReports(): void
    {
        $this->http
            ->push(self::reply(self::envelope(['reportId' => 'R1', 'report' => [['msgKey' => 'K001', 'reportCode' => '10000']]])))
            ->push(self::reply(self::envelope()))
            ->push(self::reply(self::envelope(['reportId' => '', 'report' => null])));
        $store = [];
        $this->capture(function () use (&$store): void {
            pollReports($this->client, $store);
        });
        self::assertSame(['K001' => 'delivered'], $store);
    }

    public function testMessageHistory(): void
    {
        $this->http
            ->push(self::reply(self::envelope(['messages' => [['msgKey' => 'K001', 'reportCode' => '63020']], 'hasNext' => false])))
            ->push(self::reply(self::envelope(['messages' => [['msgKey' => 'K001', 'serviceType' => 'ALIMTALK', 'reportCode' => '63020']]])));
        self::assertStringContainsString('ALIMTALK 63020', $this->capture(fn () => messageHistory($this->client)));
        self::assertSame('/api/comm/v1/message/inquiry/msgKey/K001', $this->http->last()->getUri()->getPath());
    }

    public function testWebhookEndpoint(): void
    {
        $receiver = new WebhookReceiver('test-webhook-secret');
        $timestamp = (string) (int) (microtime(true) * 1000);
        $signature = hash_hmac('sha256', $timestamp, 'test-webhook-secret');
        $body = '{"msgKey":"K001","serviceType":"SMS","reportTime":"t","reportType":"0","reportCode":"10000"}';
        $seen = [];
        $headers = ['HTTP_X_IB_TIMESTAMP' => $timestamp, 'HTTP_X_IB_SIGNATURE' => $signature];
        self::assertSame([200, ['msgKey' => 'K001']], webhookHandle($receiver, $headers, $body, $seen));
        self::assertSame([200, ['msgKey' => 'K001']], webhookHandle($receiver, $headers, $body, $seen)); // duplicate: acked again
        self::assertSame(['K001' => true], $seen);
        self::assertSame([401, []], webhookHandle($receiver, ['HTTP_X_IB_TIMESTAMP' => $timestamp, 'HTTP_X_IB_SIGNATURE' => str_repeat('0', 64)], $body, $seen));
    }

    public function testBulkSend(): void
    {
        $fake = new FakeTransport();
        $numbers = array_map(static fn (int $i): string => sprintf('010000%05d', $i), range(0, 449));
        $output = $this->capture(static fn () => bulkSend($fake->client(), '01000000000', $numbers, 'campaign-test'));
        self::assertStringContainsString('접수 450건', $output);
        self::assertCount(3, $fake->requests());
        self::assertStringNotContainsString('0100000', $output);
    }

    public function testAlimtalkTemplates(): void
    {
        $fake = new FakeTransport();
        $fake->on('listAlimtalkTemplates')
            ->data(['alimtalk' => ['templates' => [['templateCode' => 'T1', 'inspectionStatus' => 'APR']]]])
            ->data(['alimtalk' => ['templates' => [['templateCode' => 'T1'], ['templateCode' => 'T2']]]]);
        $output = $this->capture(static fn () => alimtalkTemplates($fake->client(), 'SENDER_KEY_EXAMPLE'));
        self::assertStringContainsString('T1 APR', $output);
        self::assertStringContainsString('승인된 템플릿: 2개', $output);
        // a page shorter than the limit is the last one: no further request
        self::assertCount(2, $fake->requests());
        self::assertSame(['senderKey' => 'SENDER_KEY_EXAMPLE', 'inspectionStatus' => 'APR', 'offset' => '0', 'limit' => '100'], $fake->lastRequest()->query);
    }

    public function testCounselWebhook(): void
    {
        $stored = [];
        $store = static function (\Bizgo\Model\CounselMessageWebhookPayload $event) use (&$stored): void {
            $stored[] = $event->msgKey;
        };
        $body = '{"msgKey":"K001","userKey":"USER_KEY_EXAMPLE","senderKey":"SENDER_KEY_EXAMPLE","serviceType":"CSTALK",'
            . '"msgType":"TEXT","requestType":"message","sendTime":"t","reportTime":"t","kakaoTime":"t","contents":[{"comment":"문의"}]}';
        // no webhook secret: counsel webhooks are not signed
        self::assertSame([200, '{"code":"A000","result":"Success"}'], counselWebhook($body, $store));
        self::assertSame(400, counselWebhook('not json', $store)[0]);
        self::assertSame(['K001'], $stored);
    }
}
