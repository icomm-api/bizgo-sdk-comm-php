<?php

declare(strict_types=1);

// Every other API is generated from the spec: $client-><resource>-><method>(). Here: list AlimTalk templates.

namespace Bizgo\Examples\AlimtalkTemplates;

use Bizgo\BizgoClient;
use Bizgo\Environment;

use function Bizgo\Examples\env;
use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

function run(BizgoClient $client, string $senderKey): void
{
    // one page
    foreach ($client->alimtalk->templates->list(senderKey: $senderKey, limit: 50) as $template) {
        echo $template->templateCode ?? '(코드 없음)', ' ', $template->inspectionStatus ?? '-', PHP_EOL;
    }
    // every page: the iterator follows the documented pagination (here offset/limit) and stops by itself
    $approved = 0;
    foreach ($client->alimtalk->templates->iterList(senderKey: $senderKey, inspectionStatus: 'APR', limit: 100) as $template) {
        $approved++;
    }
    echo '승인된 템플릿: ', $approved, '개', PHP_EOL;
}

if (isMain(__FILE__)) {
    run(new BizgoClient(environment: Environment::Sandbox), env('BIZGO_SENDER_KEY'));
}
