<?php

declare(strict_types=1);

namespace Bizgo\Testing;

/**
 * A signed webhook request built by {@see FakeTransport::signWebhook()}: pass `$headers` and `$body` to
 * {@see \Bizgo\Webhook\WebhookReceiver} (or your HTTP test client) to test your webhook endpoint.
 */
final readonly class SignedWebhook
{
    /**
     * @param array<string, string> $headers `Content-Type`, `X-IB-Timestamp` and `X-IB-Signature`
     */
    public function __construct(
        public array $headers,
        public string $body,
    ) {
    }
}
