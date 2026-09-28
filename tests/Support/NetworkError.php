<?php

declare(strict_types=1);

namespace Bizgo\Tests\Support;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

/** A PSR-18 network error whose message contains the full URL, like real HTTP clients produce. */
final class NetworkError extends \RuntimeException implements NetworkExceptionInterface
{
    public function __construct(private readonly RequestInterface $request, string $message = '')
    {
        parent::__construct($message !== '' ? $message : 'Connection refused for ' . $request->getUri());
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
