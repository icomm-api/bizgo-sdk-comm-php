<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Exception\InvalidResponseException;
use Psr\Http\Message\ResponseInterface;

/**
 * Turns a PSR-7 response into an {@see HttpResponse}, reading the body in chunks and stopping at
 * {@see HttpSender::MAX_RESPONSE_BYTES} (counted after decompression by the client).
 *
 * @internal
 */
final class ResponseBody
{
    private function __construct()
    {
    }

    /**
     * @throws InvalidResponseException the body is larger than the cap or could not be read (the message names the
     *         exception class only)
     */
    public static function read(#[\SensitiveParameter] ResponseInterface $response): HttpResponse
    {
        $status = $response->getStatusCode();
        $body = '';
        try {
            $stream = $response->getBody();
            while (!$stream->eof()) {
                $chunk = $stream->read(65536);
                if ($chunk === '') {
                    break;
                }
                $body .= $chunk;
                if (strlen($body) > HttpSender::MAX_RESPONSE_BYTES) {
                    $stream->close();
                    throw new InvalidResponseException('응답 본문이 너무 큽니다 (최대 16MiB)', $status);
                }
            }
        } catch (InvalidResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // a response arrived but its body failed (a client's own size guard, a broken stream): stream exceptions
            // can carry the URL, so keep the class name only
            throw new InvalidResponseException('응답 본문을 읽지 못했습니다 (' . self::shortName($e) . ')', $status);
        }
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[strtolower((string) $name)] = implode(', ', $values);
        }

        return new HttpResponse($status, $headers, $body);
    }

    public static function shortName(object $object): string
    {
        $parts = explode('\\', $object::class);

        return $parts[count($parts) - 1];
    }
}
