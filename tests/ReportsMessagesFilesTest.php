<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\RateLimitException;
use Bizgo\Exception\ValidationException;
use Bizgo\Model\Report;
use Bizgo\Tests\Support\MockHttpClient;
use Bizgo\Upload;
use PHPUnit\Framework\Attributes\DataProvider;

final class ReportsMessagesFilesTest extends TestCase
{
    private const REPORT = ['msgKey' => 'KEY001', 'serviceType' => 'SMS', 'msgType' => 'SM', 'reportCode' => '10000', 'reportType' => '0'];

    public function testConsumeAcksEachBatchAfterHandler(): void
    {
        $this->http
            ->push(self::reply(self::envelope(['reportId' => 'R1', 'report' => [self::REPORT, self::REPORT]])))
            ->push(self::reply(self::envelope()))
            ->push(self::reply(self::envelope(['reportId' => '', 'report' => null])));
        $seen = [];
        $handled = $this->client->reports->consume(static function (array $reports) use (&$seen): void {
            foreach ($reports as $report) {
                $seen[] = $report->msgKey;
            }
        });
        self::assertSame(2, $handled);
        self::assertSame(['KEY001', 'KEY001'], $seen);
        self::assertSame('DELETE', $this->http->requests[1]->getMethod());
        self::assertSame('/api/comm/v1/report/polling/R1', $this->http->requests[1]->getUri()->getPath());
        self::assertSame(3, $this->http->count());
    }

    public function testConsumeDoesNotAckWhenHandlerFails(): void
    {
        $this->http->push(self::reply(self::envelope(['reportId' => 'R1', 'report' => [self::REPORT]])));
        try {
            $this->client->reports->consume(static function (array $reports): void {
                throw new \RuntimeException('db down');
            });
            self::fail('expected the handler exception');
        } catch (\RuntimeException $e) {
            self::assertSame('db down', $e->getMessage());
        }
        self::assertSame(1, $this->http->count()); // polled, never acked
    }

    public function testConsumeStopsAfterMaxBatches(): void
    {
        $this->http->always(self::reply(self::envelope(['reportId' => 'R1', 'report' => [self::REPORT]])));
        self::assertSame(2, $this->client->reports->consume(static fn (array $reports): null => null, maxBatches: 2));
        self::assertSame(4, $this->http->count()); // poll, ack, poll, ack
    }

    public function testPollReturnsTypedReports(): void
    {
        $this->http->push(self::reply(self::envelope(['reportId' => 'R9', 'report' => [self::REPORT]])));
        $batch = $this->client->reports->poll();
        self::assertFalse($batch->isEmpty());
        self::assertSame('R9', $batch->reportId);
        self::assertInstanceOf(Report::class, $batch->reports[0]);
        self::assertSame('10000', $batch->reports[0]->reportCode);
    }

    public function testPathParametersAreEscaped(): void
    {
        $this->http->always(self::reply(self::envelope(['report' => [self::REPORT]])));
        $this->client->reports->inquiry('a/b?c#d');
        self::assertSame('/api/comm/v1/report/inquiry/a%2Fb%3Fc%23d', $this->http->last()->getUri()->getPath());
        self::assertSame('', $this->http->last()->getUri()->getQuery());
        $this->client->messages->status('K 1%');
        self::assertSame('/api/comm/v1/message/inquiry/msgKey/K%201%25', $this->http->last()->getUri()->getPath());
    }

    /** @return iterable<string, array{string}> */
    public static function badSegments(): iterable
    {
        yield 'empty' => [''];
        yield 'dot' => ['.'];
        yield 'dot dot' => ['..'];
    }

    #[DataProvider('badSegments')]
    public function testDotSegmentsAndEmptyValuesAreRejected(string $value): void
    {
        try {
            $this->client->reports->ack($value);
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame('reportId', $e->errors[0]['path']);
        }
        self::assertSame(0, $this->http->count());
    }

    public function testIterHistoryFollowsLastSeq(): void
    {
        $message = ['msgKey' => 'K', 'serviceType' => 'SMS'];
        $this->http
            ->push(self::reply(self::envelope(['messages' => [$message, $message], 'lastSeq' => 10, 'hasNext' => true])))
            ->push(self::reply(self::envelope(['messages' => [$message], 'lastSeq' => 11, 'hasNext' => false])));
        $since = new \DateTimeImmutable('2026-09-23 09:00:00', new \DateTimeZone('Asia/Seoul'));
        $items = iterator_to_array($this->client->messages->iterHistory($since, serviceType: ['SMS', 'RCS'], limit: 2));
        self::assertCount(3, $items);
        $first = MockHttpClient::query($this->http->requests[0]);
        $second = MockHttpClient::query($this->http->requests[1]);
        self::assertSame(['requestTime' => '2026-09-23T09:00:00', 'serviceType' => 'SMS,RCS', 'limit' => '2'], $first);
        self::assertSame('10', $second['lastSeq']);
    }

    public function testIterHistoryStopsIfCursorDoesNotMove(): void
    {
        $this->http->always(self::reply(self::envelope(['messages' => [['msgKey' => 'K']], 'lastSeq' => 5, 'hasNext' => true])));
        self::assertCount(2, iterator_to_array($this->client->messages->iterHistory('2026-09-23T09:00:00')));
        self::assertSame(2, $this->http->count());
    }

    public function testIterHistoryStopsWithoutLastSeq(): void
    {
        $this->http->always(self::reply(self::envelope(['messages' => [['msgKey' => 'K']], 'hasNext' => true])));
        self::assertCount(1, iterator_to_array($this->client->messages->iterHistory('2026-09-23T09:00:00')));
        self::assertSame(1, $this->http->count());
    }

    public function testIterMoHistory(): void
    {
        $this->http
            ->push(self::reply(self::envelope(['messages' => [['msgKey' => 'M1']], 'lastSeq' => 1, 'hasNext' => true])))
            ->push(self::reply(self::envelope(['messages' => [['msgKey' => 'M2']], 'lastSeq' => 2, 'hasNext' => false])));
        $keys = [];
        foreach ($this->client->messages->iterMoHistory('2026-04-23T14:11:01+09:00', to: '01000000000') as $mo) {
            $keys[] = $mo->msgKey;
        }
        self::assertSame(['M1', 'M2'], $keys);
        self::assertSame('01000000000', MockHttpClient::query($this->http->requests[0])['to']);
    }

    public function testMoHistoryTimeIsSentWithKstOffset(): void
    {
        $this->http->always(self::reply(self::envelope(['messages' => [], 'hasNext' => false])));
        $this->client->messages->moHistory(new \DateTimeImmutable('2026-04-23 14:11:01', new \DateTimeZone('Asia/Seoul')));
        self::assertSame('2026-04-23T14:11:01+09:00', $this->http->lastQuery()['occurredTime']);
        $this->client->messages->moHistory(new \DateTimeImmutable('2026-04-23 05:11:01', new \DateTimeZone('UTC')));
        self::assertSame('2026-04-23T14:11:01+09:00', $this->http->lastQuery()['occurredTime']);
        self::assertSame('/api/comm/v1/message/history/mo', $this->http->last()->getUri()->getPath());
    }

    public function testDateTimeIsConvertedToKst(): void
    {
        $this->http->always(self::reply(self::envelope(['messages' => []])));
        $this->client->messages->history(new \DateTime('2026-09-23 00:00:00', new \DateTimeZone('UTC')));
        self::assertSame('2026-09-23T09:00:00', $this->http->lastQuery()['requestTime']);
        $this->client->messages->history('2026-09-23T09:00:00'); // strings are sent as given
        self::assertSame('2026-09-23T09:00:00', $this->http->lastQuery()['requestTime']);
    }

    public function testLimitRange(): void
    {
        try {
            $this->client->messages->history('2026-09-23T09:00:00', limit: 1001);
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString('1~1000', $e->getMessage());
        }
        $this->expectException(ValidationException::class);
        $this->client->messages->moHistory('2026-09-23T09:00:00+09:00', limit: 0);
    }

    public function testStatistics(): void
    {
        $this->http->push(self::reply(self::envelope(['statistics' => [['statDate' => '20260923', 'recvTotalCnt' => 3]]])));
        $stats = $this->client->messages->statistics(
            new \DateTimeImmutable('2026-09-01', new \DateTimeZone('Asia/Seoul')),
            new \DateTimeImmutable('2026-09-22 16:00:00', new \DateTimeZone('UTC')), // 2026-09-23 01:00 KST
            serviceType: 'SMS',
        );
        self::assertSame(3, $stats[0]->recvTotalCnt);
        self::assertSame(['startDate' => '20260901', 'endDate' => '20260923', 'serviceType' => 'SMS'], $this->http->lastQuery());
    }

    public function testStatusByRequestIdAndMo(): void
    {
        $this->http->always(self::reply(self::envelope(['messages' => [['msgKey' => 'K', 'from' => '01000000000']]])));
        self::assertSame('K', $this->client->messages->statusByRequestId('REQ1')[0]->msgKey);
        self::assertSame('/api/comm/v1/message/inquiry/requestId/REQ1', $this->http->last()->getUri()->getPath());
        self::assertSame('01000000000', $this->client->messages->mo('M1')[0]->from);
        self::assertSame('/api/comm/v1/message/inquiry/mo/msgKey/M1', $this->http->last()->getUri()->getPath());
    }

    public function testResponseKeepsUnknownFields(): void
    {
        $this->http->push(self::reply(self::envelope(['messages' => [['msgKey' => 'K', 'newServerField' => 'v']]])));
        $status = $this->client->messages->status('K')[0];
        self::assertSame(['newServerField' => 'v'], $status->extra);
        self::assertSame(['msgKey' => 'K', 'newServerField' => 'v'], $status->toArray());
    }

    public function testUploadMmsSendsMultipart(): void
    {
        $this->http->push(self::reply(self::envelope(['fileKey' => 'FILE_KEY_001', 'expired' => '2027-04-24T10:45:24+09:00'])));
        $result = $this->client->files->uploadMms(Upload::fromContents("\xff\xd8jpeg-bytes", 'a.jpg'), imageName: 'banner');
        $request = $this->http->last();
        self::assertSame('/api/comm/v1/file/mms', $request->getUri()->getPath());
        self::assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
        $body = (string) $request->getBody();
        self::assertStringContainsString("Content-Disposition: form-data; name=\"file\"; filename=\"a.jpg\"\r\nContent-Type: image/jpeg\r\n\r\n\xff\xd8jpeg-bytes\r\n", $body);
        self::assertStringContainsString("name=\"imageName\"\r\n\r\nbanner\r\n", $body);
        self::assertStringNotContainsString('name="fileKey"', $body); // unset fields are not sent
        self::assertSame('FILE_KEY_001', $result->fileKey);
        self::assertSame('2027-04-24T10:45:24+09:00', $result->expired);
    }

    public function testUploadMmsSizeLimit(): void
    {
        try {
            $this->client->files->uploadMms(Upload::fromContents(str_repeat('x', 300 * 1024 + 1), 'a.jpg'));
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString('300KB', $e->getMessage());
        }
        self::assertSame(0, $this->http->count());
        $this->http->push(self::reply(self::envelope(['fileKey' => 'K'])));
        $this->client->files->uploadMms(Upload::fromContents(str_repeat('x', 300 * 1024), 'a.jpg')); // exactly 300KB: ok
        self::assertSame(1, $this->http->count());
    }

    public function testUploadBrandMessagePathAndKind(): void
    {
        $this->http->push(self::reply(self::envelope(['imgUrl' => 'https://example.com/img.jpg'])));
        $result = $this->client->files->uploadBrandMessage(Upload::fromContents('img', 'a.png'), kind: 'wideItemList/first');
        self::assertSame('https://example.com/img.jpg', $result->imgUrl);
        self::assertSame('/api/comm/v1/file/brandmessage/wideItemList/first', $this->http->last()->getUri()->getPath());
        self::assertStringContainsString('Content-Type: image/png', (string) $this->http->last()->getBody());
        $this->expectException(ValidationException::class);
        $this->client->files->uploadBrandMessage(Upload::fromContents('img', 'a.png'), kind: '../etc');
    }

    public function testUploadRcsFromPathAndStream(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bizgo');
        self::assertIsString($path);
        try {
            file_put_contents($path, 'rcs-image');
            $this->http->always(self::reply(self::envelope(['media' => 'maapfile://MEDIA_KEY_EXAMPLE'])));
            self::assertSame('maapfile://MEDIA_KEY_EXAMPLE', $this->client->files->uploadRcs($path, filename: 'card.png')->media);
            self::assertStringContainsString("filename=\"card.png\"\r\nContent-Type: image/png\r\n\r\nrcs-image\r\n", (string) $this->http->last()->getBody());

            $stream = fopen($path, 'rb');
            self::assertIsResource($stream);
            $this->client->files->uploadRcs($stream, filename: 'card.gif');
            fclose($stream);
            self::assertStringContainsString("Content-Type: image/gif\r\n\r\nrcs-image\r\n", (string) $this->http->last()->getBody());

            $this->client->files->uploadRcs(new \SplFileInfo($path));
            self::assertStringContainsString('filename="' . basename($path) . '"', (string) $this->http->last()->getBody());
        } finally {
            unlink($path);
        }
    }

    /** @return iterable<string, array{string}> */
    public static function refusedPaths(): iterable
    {
        yield 'http URL' => ['http://example.com/a.jpg'];
        yield 'phar wrapper' => ['phar://archive.phar/a.jpg'];
        yield 'php stream' => ['php://filter/resource=/etc/passwd'];
        yield 'missing file' => [__DIR__ . '/does-not-exist.jpg'];
    }

    #[DataProvider('refusedPaths')]
    public function testOnlyLocalFilesAreRead(string $path): void
    {
        $this->expectException(ValidationException::class);
        $this->client->files->uploadMms($path);
    }

    public function testUploadIsRetriedOnlyOnRateLimitWithTheSameBytes(): void
    {
        $this->http
            ->push(self::reply(['common' => ['authCode' => 'A020', 'authResult' => 'Ratelimit']], 429))
            ->push(self::reply(self::envelope(['fileKey' => 'K'])));
        $this->client->files->uploadMms(Upload::fromContents('jpeg', 'a.jpg'));
        self::assertSame(2, $this->http->count());
        self::assertSame((string) $this->http->requests[0]->getBody(), (string) $this->http->requests[1]->getBody());

        $this->http->always(self::reply('', 503));
        try {
            $this->client->files->uploadMms(Upload::fromContents('jpeg', 'a.jpg'));
            self::fail('expected an ApiException');
        } catch (ApiException $e) {
            self::assertNotInstanceOf(RateLimitException::class, $e);
        }
        self::assertSame(3, $this->http->count()); // the 503 was not retried
    }

    public function testMultipartFilenameCannotInjectHeaders(): void
    {
        $this->http->push(self::reply(self::envelope(['fileKey' => 'K'])));
        $this->client->files->uploadMms(Upload::fromContents('jpeg', "a\"\r\nX-Evil: 1.jpg"));
        $body = (string) $this->http->last()->getBody();
        self::assertStringNotContainsString("\r\nX-Evil", $body);
        self::assertStringContainsString('filename="a%22X-Evil: 1.jpg"', $body);
    }
}
