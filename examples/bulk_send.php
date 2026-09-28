<?php

declare(strict_types=1);

// Send one message to a long recipient list, 200 at a time, safely re-runnable with an idempotency key prefix.

namespace Bizgo\Examples\BulkSend;

use Bizgo\BizgoClient;
use Bizgo\Environment;
use Bizgo\Model\SmsMessage;

use function Bizgo\Examples\env;
use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

/**
 * @param list<string> $recipients
 */
function run(BizgoClient $client, string $from, array $recipients, string $campaign): void
{
    // Re-run with the same list and chunkSize after a crash: chunks already accepted are not delivered twice.
    $result = $client->send->bulk(
        to: $recipients,
        messages: [new SmsMessage(from: $from, text: '[비즈고] 정기 점검 안내입니다.')],
        chunkSize: 200,
        idempotencyKeyPrefix: $campaign,
    );
    foreach ($result->errors as $error) { // a whole chunk failed: its recipient index range, never the numbers
        echo '청크 실패: ', $error->chunkIndex, ' (', $error->start, '-', $error->end - 1, ') ', $error->error::class, PHP_EOL;
    }
    echo '접수 ', count($result->msgKeys()), '건, 거절 ', count($result->failed()), '건', PHP_EOL;
}

if (isMain(__FILE__)) {
    $numbers = array_map('trim', explode(',', env('BIZGO_TO_LIST'))); // comma-separated
    $recipients = array_values(array_filter($numbers, static fn (string $n): bool => $n !== ''));
    run(new BizgoClient(environment: Environment::Sandbox), env('BIZGO_FROM'), $recipients, 'campaign-2026-09');
}
