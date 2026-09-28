<?php

declare(strict_types=1);

// Receive counsel (상담톡) user messages: <your webhook URL>/cstalk/message.
// Counsel webhooks are not signed (signatures apply only to report and MO webhooks), so no webhook secret is needed.

namespace Bizgo\Examples\CounselWebhook;

use Bizgo\Exception\WebhookPayloadException;
use Bizgo\Model\CounselMessageWebhookPayload;
use Bizgo\Webhook\WebhookReceiver;

use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

/**
 * @param \Closure(CounselMessageWebhookPayload): void $store saves the event idempotently (by msgKey; retries repeat it)
 *
 * @return array{int, string} HTTP status and JSON body to answer with
 */
function handle(string $body, \Closure $store): array
{
    try {
        $event = WebhookReceiver::parseCounselMessage($body); // size, JSON depth and field checks
    } catch (WebhookPayloadException) {
        return [400, '{}'];
    }
    $store($event); // end-user data: keep it for the counsel, never log it

    return [200, json_encode(WebhookReceiver::counselAck(), JSON_THROW_ON_ERROR)];
}

if (isMain(__FILE__)) {
    // as for any webhook endpoint: serve it over HTTPS and allow only the Bizgo webhook source IPs
    $store = static function (CounselMessageWebhookPayload $event): void {
        // save $event to your database here
    };
    [$status, $answer] = handle((string) file_get_contents('php://input'), $store);
    http_response_code($status);
    header('Content-Type: application/json');
    echo $answer;
}
