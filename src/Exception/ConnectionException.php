<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/**
 * The request failed before a response arrived.
 *
 * For send requests the message may or may not have been accepted. Re-sending can cause a duplicate
 * unless you set `idempotencyKey`; check with `$client->messages->status()` first.
 *
 * The message never contains the request URL (its query string can carry phone numbers), and the
 * underlying HTTP library exception is not chained for the same reason.
 */
class ConnectionException extends \RuntimeException implements BizgoException
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
