<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/** The client is configured incorrectly (for example the API key is missing or the base URL is not https). */
final class ConfigurationException extends \InvalidArgumentException implements BizgoException
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
