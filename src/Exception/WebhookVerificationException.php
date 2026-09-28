<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/** A webhook request failed signature, timestamp or body checks. Answer it with HTTP 401. */
class WebhookVerificationException extends \RuntimeException implements BizgoException
{
    /**
     * The message only for var_dump()/print_r(): the stack trace (whose arguments can hold request data) is left out.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['message' => $this->getMessage()];
    }
}
