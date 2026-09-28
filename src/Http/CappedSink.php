<?php

declare(strict_types=1);

namespace Bizgo\Http;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

/**
 * Guzzle `sink` that stops the transfer once the (decompressed) body exceeds {@see HttpSender::MAX_RESPONSE_BYTES}:
 * a short write makes cURL abort, so a gzip bomb never fills memory or disk. Loaded only with Guzzle.
 *
 * @internal
 */
final class CappedSink implements StreamInterface
{
    use StreamDecoratorTrait;

    public bool $exceeded = false;
    private int $written = 0;
    private StreamInterface $stream;

    public function __construct()
    {
        $this->stream = Utils::streamFor(Utils::tryFopen('php://temp', 'w+'));
    }

    public function write($string): int
    {
        if ($this->written + strlen($string) > HttpSender::MAX_RESPONSE_BYTES) {
            $this->exceeded = true;

            return 0; // cURL: CURLE_WRITE_ERROR, the transfer stops
        }
        $this->written += strlen($string);

        return $this->stream->write($string);
    }
}
