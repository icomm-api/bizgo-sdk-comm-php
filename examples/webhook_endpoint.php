<?php

declare(strict_types=1);

// Receive delivery report webhooks with plain PHP (php -S, PHP-FPM, ...).
//
// Production checklist:
// - serve over HTTPS and allow only the Bizgo webhook source IPs (see the API overview page)
// - answer within 5 seconds; do slow work asynchronously
// - deduplicate by msgKey (Bizgo retries up to 3 times)
// - do not write request headers or bodies to access logs: they contain phone numbers

namespace Bizgo\Examples\WebhookEndpoint;

use Bizgo\Exception\WebhookVerificationException;
use Bizgo\Webhook\WebhookReceiver;

use function Bizgo\Examples\env;
use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

/**
 * Framework-independent handler, easy to test and to reuse in a controller.
 *
 * @param array<array-key, mixed> $headers
 * @param array<string, true> $seen stand-in for a persistent dedup store
 *
 * @return array{int, array<string, string>} HTTP status and JSON body
 */
function handle(WebhookReceiver $receiver, array $headers, string $body, array &$seen): array
{
    try {
        $report = $receiver->report($headers, $body);
    } catch (WebhookVerificationException) {
        return [401, []];
    }
    $msgKey = $report->msgKey ?? '';
    if (!isset($seen[$msgKey])) {
        $seen[$msgKey] = true;
        // enqueue $report for processing here
    }

    return [200, WebhookReceiver::ack($msgKey)];
}

if (isMain(__FILE__)) {
    $seen = [];
    $raw = file_get_contents('php://input', false, null, 0, WebhookReceiver::MAX_BODY_BYTES + 1);
    [$status, $payload] = handle(new WebhookReceiver(env('BIZGO_WEBHOOK_SECRET')), $_SERVER, $raw === false ? '' : $raw, $seen);
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload === [] ? new \stdClass() : $payload, JSON_THROW_ON_ERROR);
}
