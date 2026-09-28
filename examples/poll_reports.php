<?php

declare(strict_types=1);

// Process delivery reports with polling (the API key must be set to POLLING in the console).

namespace Bizgo\Examples\PollReports;

use Bizgo\BizgoClient;
use Bizgo\Environment;

use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

/**
 * @param array<string, string> $store stand-in for your database
 */
function run(BizgoClient $client, array &$store): void
{
    // polls, calls the handler, acks only after the handler returned
    $handled = $client->reports->consume(static function (array $reports) use (&$store): void {
        foreach ($reports as $report) {
            // upsert by msgKey: a batch is delivered again if this throws before the ack
            $store[$report->msgKey ?? ''] = $report->reportCode === '10000' ? 'delivered' : 'failed:' . $report->reportCode;
        }
    });
    echo "리포트 {$handled}건 처리", PHP_EOL;
}

if (isMain(__FILE__)) {
    $store = [];
    run(new BizgoClient(environment: Environment::Sandbox), $store);
}
