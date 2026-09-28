<?php

declare(strict_types=1);

// Send an SMS and check the per-recipient acceptance result.

namespace Bizgo\Examples\SendSms;

use Bizgo\BizgoClient;
use Bizgo\Environment;

use function Bizgo\Examples\env;
use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

function run(BizgoClient $client, string $from, string $to): void
{
    $result = $client->send->sms(to: $to, from: $from, text: '[비즈고] 인증번호는 123456 입니다.', ref: 'signup-otp');

    foreach ($result->failed() as $rejected) { // accepted != delivered; rejected recipients never get the message
        echo '접수 실패: ', $rejected->code, ' ', $rejected->result, PHP_EOL;
    }
    echo '접수된 메시지 키: ', implode(', ', $result->msgKeys()), PHP_EOL; // the final result comes later as a report
}

if (isMain(__FILE__)) {
    run(new BizgoClient(environment: Environment::Sandbox), env('BIZGO_FROM'), env('BIZGO_TO')); // key from BIZGO_API_KEY
}
