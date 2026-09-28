<?php

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Model\ApiResponse;
use Bizgo\Model\Report;
use Bizgo\Model\ReportInquiryResponse;
use Bizgo\Model\ReportPollingResponse;
use Bizgo\Result\ReportBatch;

/**
 * Delivery reports: `/api/comm/v1/report/*`. Access as `$client->reports`.
 *
 * Pick one primary method in the console per API key: POLLING (this class) or WEBHOOK
 * (see {@see \Bizgo\Webhook\WebhookReceiver}). Use {@see self::inquiry()} to fill gaps.
 */
final class Reports
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * Fetches the next batch of reports. `$batch->isEmpty()` is true when there is nothing new.
     *
     * Call {@see self::ack()} with `$batch->reportId` after storing the batch; otherwise the same reports are
     * returned again. Prefer {@see self::consume()}, which does this for you.
     *
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function poll(): ReportBatch
    {
        $inner = $this->transport->call('getReportPolling', ReportPollingResponse::class)->data?->data;

        return new ReportBatch($inner->reportId ?? '', $inner->report ?? []);
    }

    /**
     * Confirms receipt of a polled batch.
     *
     * @throws ValidationException the report ID is empty or a dot segment
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function ack(#[\SensitiveParameter] string $reportId): void
    {
        $this->transport->call('ackReportPolling', ApiResponse::class, path: ['reportId' => $reportId]);
    }

    /**
     * Polls until no reports are left, calling `$handler` for each batch and acking it only if the handler
     * returns without throwing. Returns the number of reports handled.
     *
     * If `$handler` throws, the batch is not acked and will be delivered again, so make the handler idempotent
     * (for example upsert by `msgKey`). The exception is rethrown.
     *
     * @param callable(list<Report>): mixed $handler
     * @param int|null $maxBatches stop after this many batches (null: until a batch is empty)
     *
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     * @throws ValidationException
     */
    public function consume(callable $handler, ?int $maxBatches = null): int
    {
        $handled = 0;
        $batches = 0;
        while ($maxBatches === null || $batches < $maxBatches) {
            $batch = $this->poll();
            if ($batch->isEmpty()) {
                break;
            }
            $handler($batch->reports);
            $this->ack($batch->reportId);
            $handled += count($batch->reports);
            $batches++;
        }

        return $handled;
    }

    /**
     * Looks up the reports of one message (up to 30 days old).
     *
     * @return list<Report>
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function inquiry(#[\SensitiveParameter] string $msgKey): array
    {
        $response = $this->transport->call('getReportInquiry', ReportInquiryResponse::class, path: ['msgKey' => $msgKey]);

        return $response->data->data->report ?? [];
    }
}
