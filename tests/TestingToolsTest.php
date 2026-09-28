<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\AuthenticationException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\NotFoundException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\TimeoutException;
use Bizgo\Model\ReservationCreateRequest;
use Bizgo\Model\SmsMessage;
use Bizgo\Testing\FakeTransport;
use Bizgo\Testing\RecordedFile;
use Bizgo\Upload;
use Bizgo\Webhook\WebhookReceiver;
use PHPUnit\Framework\TestCase;

/** The `Bizgo\Testing` tools for customers' own tests (SDK-DESIGN.md §11.3). */
final class TestingToolsTest extends TestCase
{
    public function testDefaultSendAcceptsEveryRecipientAndRecordsTheRequest(): void
    {
        $fake = new FakeTransport();
        $client = $fake->client();
        $result = $client->send->sms(to: ['01000000000', '01000000001'], from: '01000000000', text: 'hello', ref: 'order-1');
        self::assertCount(2, $result->msgKeys());
        self::assertSame([], $result->failed());
        self::assertSame('order-1', $result->ref);

        $request = $fake->lastRequest();
        self::assertSame('sendOmni', $request->operationId);
        self::assertSame('send.omni', $request->operation);
        self::assertSame('POST', $request->method);
        self::assertSame('/api/comm/v1/send/omni', $request->pathTemplate);
        self::assertIsArray($request->json);
        self::assertSame('hello', \Bizgo\Tests\Support\SpecSamples::get($request->json, 'messageFlow', 0, 'sms', 'text'));
        // header names only, never values
        self::assertContains('authorization', $request->headerNames);
        self::assertStringNotContainsString(FakeTransport::API_KEY, serialize($fake->requests()));
    }

    public function testCreateReservationDefaultHasAResvKey(): void
    {
        $fake = new FakeTransport();
        $result = $fake->client()->reservations->create(ReservationCreateRequest::fromArray([
            'destinations' => [['to' => '01000000000']],
            'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'hi']]],
            'resvSendTime' => '2026-12-01 10:00:00',
        ]));
        self::assertSame('FAKE-RESVKEY-000001', $result->resvKey);
        self::assertSame('A000', $result->data?->destinations[0]->code ?? null);
    }

    public function testUploadsReturnFakeKeysAndRecordFileSizesOnly(): void
    {
        $fake = new FakeTransport();
        $client = $fake->client();
        self::assertSame('FAKE-FILEKEY-000001', $client->files->uploadMms(Upload::fromContents("\xff\xd8jpeg", 'a.jpg'))->fileKey);
        $form = $fake->lastRequest()->form;
        self::assertIsArray($form);
        self::assertEquals(new RecordedFile('a.jpg', 'image/jpeg', 6), $form['file']);
        self::assertSame('FAKE-MEDIA-000001', $client->files->uploadRcs(Upload::fromContents('png', 'a.png'))->media);
    }

    /** An operation that takes JSON or multipart also has a `WithFiles` variant that sends the form. */
    public function testMultipartVariantOfAJsonOperation(): void
    {
        $fake = new FakeTransport();
        $fake->client()->alimtalk->templates->requestInspectionWithFiles(
            senderKey: 'SENDER_KEY_EXAMPLE',
            templateCode: 'TEMPLATE_CODE_EXAMPLE',
            comment: '검수 요청',
            attachment: [Upload::fromContents('%PDF', 'guide.pdf'), Upload::fromContents('png', 'shot.png')],
        );
        $request = $fake->lastRequest();
        self::assertSame('requestAlimtalkTemplateInspection', $request->operationId);
        self::assertNull($request->json);
        self::assertIsArray($request->form);
        self::assertSame('검수 요청', $request->form['comment']);
        self::assertEquals([new RecordedFile('guide.pdf', 'application/pdf', 4), new RecordedFile('shot.png', 'image/png', 3)], $request->form['attachment']);
    }

    public function testOtherOperationsReturnEmptyResults(): void
    {
        $client = (new FakeTransport())->client();
        self::assertSame([], $client->reports->inquiry('KEY001'));
        self::assertSame([], $client->alimtalk->templates->list(senderKey: 'SENDER_KEY_EXAMPLE'));
        self::assertNull($client->alimtalk->templates->get(senderKey: 'SENDER_KEY_EXAMPLE', templateCode: 'T1')->templateCode);
    }

    public function testStubsByOperationOrPathTemplate(): void
    {
        $fake = new FakeTransport();
        $fake->on('GET', '/api/comm/v1/report/inquiry/{msgKey}')->data(['report' => [['msgKey' => 'KEY001', 'result' => 'Success']]]);
        $reports = $fake->client()->reports->inquiry('KEY001');
        self::assertSame('KEY001', $reports[0]->msgKey);
        self::assertSame('/api/comm/v1/report/inquiry/KEY001', $fake->lastRequest()->path);
        self::assertSame([], $fake->requestsFor('sendOmni'));
        $this->expectException(\InvalidArgumentException::class);
        $fake->on('noSuchOperation');
    }

    public function testErrorInjection(): void
    {
        $fake = new FakeTransport();
        $client = $fake->client(maxRetries: 0);
        $sms = [new SmsMessage(from: '01000000000', text: 'hi')];

        $fake->on('sendOmni')->fail('service', 200, 'A020')->default();
        try {
            $client->send->omni(to: '01000000000', messages: $sms);
            self::fail('expected RateLimitException');
        } catch (RateLimitException $e) {
            self::assertSame('A020', $e->errorCode);
        }
        self::assertCount(1, $client->send->omni(to: '01000000000', messages: $sms)->msgKeys(), 'the last stub keeps answering');

        $fake->reset();
        $fake->on('getReportPolling')->fail('gateway', 401, 'A401');
        $this->assertThrows(AuthenticationException::class, static fn () => $client->reports->poll());

        $fake->on('sendOmni')->sendResult('A000', 'A306');
        $result = $client->send->omni(to: ['01000000000', '01000000001'], messages: $sms);
        self::assertSame('A306', $result->failed()[0]->code);
        $fake->reset();
        $fake->on('sendOmni')->sendResult('A301');
        $again = $client->send->sms(to: '01000000000', from: '01000000000', text: 'x', idempotencyKey: 'k');
        self::assertCount(1, $again->duplicates());
        self::assertSame([], $again->failed());

        $fake->on('getMessageStatusByMsgKey')->networkError();
        $this->assertThrows(ConnectionException::class, static fn () => $client->messages->status('KEY001'));
        $fake->on('getMoByMsgKey')->timeout();
        $this->assertThrows(TimeoutException::class, static fn () => $client->messages->mo('KEY001'));
    }

    public function testUnknownPathsAnswer404(): void
    {
        $fake = new FakeTransport();
        $response = $fake->send(new \Bizgo\Http\HttpRequest('GET', 'https://sandbox-mars.ibapi.kr/nope', [], null));
        self::assertSame(404, $response->status);
        $this->expectException(NotFoundException::class);
        \Bizgo\Http\Transport::parse($response);
    }

    public function testSignWebhookIsAcceptedByTheReceiver(): void
    {
        foreach (['hex', 'base64'] as $encoding) {
            $payload = ['msgKey' => 'KEY001', 'serviceType' => 'SMS', 'reportTime' => '2026-01-01T10:00:00', 'reportType' => 'RS', 'reportCode' => 'A000'];
            $signed = FakeTransport::signWebhook('test-webhook-secret', $payload, encoding: $encoding);
            $report = (new WebhookReceiver('test-webhook-secret'))->report($signed->headers, $signed->body);
            self::assertSame('KEY001', $report->msgKey);
        }
        $old = FakeTransport::signWebhook('test-webhook-secret', '{"msgKey":"KEY001"}', timestamp: '1000000000000');
        $this->assertThrows(\Bizgo\Exception\WebhookVerificationException::class, static fn () => (new WebhookReceiver('test-webhook-secret'))->report($old->headers, $old->body));
    }

    /** @param class-string<\Throwable> $class */
    private function assertThrows(string $class, \Closure $call): void
    {
        try {
            $call();
            self::fail("expected {$class}");
        } catch (\Throwable $e) {
            self::assertInstanceOf($class, $e);
        }
    }
}
