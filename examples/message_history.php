<?php

declare(strict_types=1);

// Walk the send history of the last hour, then look up one failed message's status.

namespace Bizgo\Examples\MessageHistory;

use Bizgo\BizgoClient;
use Bizgo\Environment;

use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

function run(BizgoClient $client): void
{
    $since = new \DateTimeImmutable('-1 hour'); // any time zone: converted to KST automatically
    $failed = [];
    foreach ($client->messages->iterHistory($since, serviceType: ['SMS', 'ALIMTALK']) as $message) { // follows pages
        if ($message->reportCode !== '10000') {
            $failed[] = $message;
        }
    }
    echo '최근 1시간 실패 ', count($failed), '건', PHP_EOL;
    foreach (array_slice($failed, 0, 1) as $message) {
        foreach ($client->messages->status($message->msgKey ?? '') as $step) { // one entry per channel tried
            echo $step->serviceType, ' ', $step->reportCode, ' ', $step->reportText, PHP_EOL;
        }
    }
}

if (isMain(__FILE__)) {
    run(new BizgoClient(environment: Environment::Sandbox));
}
