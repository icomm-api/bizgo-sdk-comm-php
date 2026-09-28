<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Exception\ConnectionException;
use Bizgo\Exception\InvalidResponseException;

/**
 * Sends one HTTP request. Implementations must not follow redirects, must verify TLS, must cap the response
 * size at {@see self::MAX_RESPONSE_BYTES} and must throw exceptions whose message has no URL, header or body.
 *
 * @internal
 */
interface HttpSender
{
    /** Responses larger than this are rejected so a broken server cannot exhaust memory. */
    public const MAX_RESPONSE_BYTES = 16 * 1024 * 1024;

    /**
     * @throws ConnectionException when no response arrived
     * @throws InvalidResponseException when the response is larger than the cap
     */
    public function send(HttpRequest $request): HttpResponse;

    /** For the User-Agent header, for example `curl/8.9.1`. */
    public function name(): string;
}
