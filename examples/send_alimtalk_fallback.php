<?php

declare(strict_types=1);

// Send a Kakao AlimTalk message; if it fails, Bizgo sends the SMS instead (fallback).

namespace Bizgo\Examples\SendAlimtalkFallback;

use Bizgo\BizgoClient;
use Bizgo\Environment;
use Bizgo\Exception\DuplicateRequestException;
use Bizgo\Model\AlimtalkMessage;
use Bizgo\Model\Destination;
use Bizgo\Model\SmsMessage;

use function Bizgo\Examples\env;
use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

function run(BizgoClient $client, string $from, string $to, string $senderKey, string $templateCode): void
{
    $orderId = '20260923-0001';
    try {
        $result = $client->send->omni(
            to: [new Destination(to: $to, replaceWords: ['name' => '홍길동', 'order' => $orderId])],
            messages: [
                new AlimtalkMessage(
                    senderKey: $senderKey,
                    templateCode: $templateCode,
                    msgType: 'AT',
                    text: '#{name}님, 주문(#{order})이 접수되었습니다.', // must match the approved template
                ),
                new SmsMessage(from: $from, text: '#{name}님, 주문(#{order})이 접수되었습니다.'),
            ],
            // same key => Bizgo rejects a second send, so retries after a timeout are safe
            idempotencyKey: 'order-' . $orderId . '-' . substr(hash('sha256', $to), 0, 16),
            ref: $orderId,
        );
    } catch (DuplicateRequestException) {
        echo '이미 발송된 주문입니다.', PHP_EOL;

        return;
    }
    echo '접수: ', implode(', ', $result->msgKeys()), ' / 실패: ', count($result->failed()), PHP_EOL;
}

if (isMain(__FILE__)) {
    run(
        new BizgoClient(environment: Environment::Sandbox),
        env('BIZGO_FROM'),
        env('BIZGO_TO'),
        env('BIZGO_KAKAO_SENDER_KEY'),
        env('BIZGO_KAKAO_TEMPLATE_CODE'),
    );
}
