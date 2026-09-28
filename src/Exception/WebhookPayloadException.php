<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * The webhook body is not usable: not JSON, too large, nested deeper than 64 levels, missing required fields or with
 * fields of the wrong type. Answer with HTTP 400. A subclass of {@see WebhookVerificationException}, so catching that
 * also catches this.
 */
final class WebhookPayloadException extends WebhookVerificationException
{
}
