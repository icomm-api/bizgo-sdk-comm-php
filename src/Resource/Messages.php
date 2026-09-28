<?php

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Exception\ApiException;
use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Http\Transport;
use Bizgo\Internal\Dates;
use Bizgo\Model\MessageStatistics;
use Bizgo\Model\MessageStatisticsResponse;
use Bizgo\Model\MessageStatus;
use Bizgo\Model\MessageStatusListResponse;
use Bizgo\Model\MoMessage;
use Bizgo\Model\MoMessageListResponse;
use Bizgo\Result\MessagePage;
use Bizgo\Result\MoPage;

/**
 * Message status, history and statistics: `/api/comm/v1/message/*`. Access as `$client->messages`.
 *
 * These are non-send APIs, limited to 5 requests per second by default.
 *
 * Dates: a `DateTimeInterface` is converted to Asia/Seoul; a string is sent as given. Note that a PHP
 * `DateTime` created without a zone uses `date.timezone`, so pass an Asia/Seoul `DateTimeImmutable` (or a
 * string) when you mean Korean time.
 */
final class Messages
{
    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * Acceptance/send/report status of one message. With fallback there is one entry per channel tried.
     *
     * @return list<MessageStatus>
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function status(#[\SensitiveParameter] string $msgKey): array
    {
        $response = $this->transport->call('getMessageStatusByMsgKey', MessageStatusListResponse::class, path: ['msgKey' => $msgKey]);

        return $response->data->data->messages ?? [];
    }

    /**
     * Status of every message in one broadcast request. `$requestId` is `msgKey` without its last 3 characters.
     *
     * @return list<MessageStatus>
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function statusByRequestId(#[\SensitiveParameter] string $requestId): array
    {
        $response = $this->transport->call(
            'getMessageStatusByRequestId',
            MessageStatusListResponse::class,
            path: ['requestId' => $requestId],
        );

        return $response->data->data->messages ?? [];
    }

    /**
     * Daily acceptance and report counts.
     *
     * @param \DateTimeInterface|string $startDate `YYYYMMDD` or a date (converted to Asia/Seoul)
     * @param \DateTimeInterface|string|null $endDate `YYYYMMDD` or a date
     * @param string|null $serviceType `SMS`, `MMS`, `RCS`, `ALIMTALK`, `BRANDMESSAGE`
     *
     * @return list<MessageStatistics>
     *
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     * @throws ValidationException
     */
    public function statistics(
        \DateTimeInterface|string $startDate,
        \DateTimeInterface|string|null $endDate = null,
        ?string $serviceType = null,
        ?string $groupKey = null,
    ): array {
        $query = [
            'startDate' => Dates::yyyymmdd($startDate, 'startDate'),
            'endDate' => $endDate === null ? null : Dates::yyyymmdd($endDate, 'endDate'),
            'serviceType' => $serviceType,
            'groupKey' => $groupKey,
        ];
        $response = $this->transport->call('getMessageStatistics', MessageStatisticsResponse::class, query: $query);

        return $response->data->data->statistics ?? [];
    }

    /**
     * One page of send history from `$requestTime` (KST). Use {@see self::iterHistory()} to walk all pages.
     *
     * @param \DateTimeInterface|string $requestTime `yyyy-MM-dd'T'HH:mm:ss` in KST, or a date-time (converted to KST)
     * @param string|list<string>|null $serviceType one type or several (`['SMS', 'ALIMTALK']`)
     * @param int|null $lastSeq cursor from the previous page
     * @param int|null $limit page size, 1-1000
     *
     * @throws ValidationException `$limit` is out of range
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function history(
        \DateTimeInterface|string $requestTime,
        string|array|null $serviceType = null,
        ?string $groupKey = null,
        ?int $lastSeq = null,
        ?int $limit = null,
    ): MessagePage {
        $query = [
            'requestTime' => Dates::localTime($requestTime),
            'serviceType' => is_array($serviceType) ? implode(',', $serviceType) : $serviceType,
            'groupKey' => $groupKey,
            'lastSeq' => $lastSeq,
            'limit' => self::limit($limit),
        ];
        $inner = $this->transport->call('getMessageHistory', MessageStatusListResponse::class, query: $query)->data?->data;

        return new MessagePage($inner->messages ?? [], $inner?->lastSeq, $inner->hasNext ?? false);
    }

    /**
     * Iterates over all send history from `$requestTime`, following `lastSeq` across pages. Stops when
     * `hasNext` is false, `lastSeq` is missing or the cursor does not move.
     *
     * @param \DateTimeInterface|string $requestTime
     * @param string|list<string>|null $serviceType
     *
     * @return \Generator<int, MessageStatus>
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterHistory(
        \DateTimeInterface|string $requestTime,
        string|array|null $serviceType = null,
        ?string $groupKey = null,
        ?int $limit = null,
    ): \Generator {
        $lastSeq = null;
        while (true) {
            $page = $this->history($requestTime, $serviceType, $groupKey, $lastSeq, $limit);
            foreach ($page->messages as $message) {
                yield $message; // sequential keys, so iterator_to_array() keeps every item
            }
            if (!$page->hasNext || $page->lastSeq === null || $page->lastSeq === $lastSeq) {
                return;
            }
            $lastSeq = $page->lastSeq;
        }
    }

    /**
     * Looks up an MO (inbound) message by key.
     *
     * @return list<MoMessage>
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function mo(#[\SensitiveParameter] string $msgKey): array
    {
        $response = $this->transport->call('getMoByMsgKey', MoMessageListResponse::class, path: ['msgKey' => $msgKey]);

        return $response->data->data->messages ?? [];
    }

    /**
     * One page of MO history, newest first, from `$occurredTime`.
     *
     * @param \DateTimeInterface|string $occurredTime `yyyy-MM-dd'T'HH:mm:ss+09:00`, or a date-time (sent in KST)
     * @param string|null $from filter by sender number
     * @param string|null $to filter by MO number
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function moHistory(
        \DateTimeInterface|string $occurredTime,
        #[\SensitiveParameter]
        ?string $from = null,
        #[\SensitiveParameter]
        ?string $to = null,
        ?int $lastSeq = null,
        ?int $limit = null,
    ): MoPage {
        $query = [
            'occurredTime' => Dates::offsetTime($occurredTime),
            'from' => $from,
            'to' => $to,
            'lastSeq' => $lastSeq,
            'limit' => self::limit($limit),
        ];
        $inner = $this->transport->call('getMoHistory', MoMessageListResponse::class, query: $query)->data?->data;

        return new MoPage($inner->messages ?? [], $inner?->lastSeq, $inner->hasNext ?? false);
    }

    /**
     * Iterates over all MO history pages.
     *
     * @return \Generator<int, MoMessage>
     *
     * @throws ValidationException
     * @throws ApiException
     * @throws ConnectionException
     * @throws InvalidResponseException
     */
    public function iterMoHistory(
        \DateTimeInterface|string $occurredTime,
        #[\SensitiveParameter]
        ?string $from = null,
        #[\SensitiveParameter]
        ?string $to = null,
        ?int $limit = null,
    ): \Generator {
        $lastSeq = null;
        while (true) {
            $page = $this->moHistory($occurredTime, $from, $to, $lastSeq, $limit);
            foreach ($page->messages as $message) {
                yield $message; // sequential keys, so iterator_to_array() keeps every item
            }
            if (!$page->hasNext || $page->lastSeq === null || $page->lastSeq === $lastSeq) {
                return;
            }
            $lastSeq = $page->lastSeq;
        }
    }

    /** @throws ValidationException */
    private static function limit(?int $limit): ?int
    {
        if ($limit !== null && ($limit < 1 || $limit > 1000)) {
            throw ValidationException::of('limit', '1~1000이어야 합니다');
        }

        return $limit;
    }
}
