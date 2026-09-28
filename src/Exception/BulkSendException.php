<?php

declare(strict_types=1);

namespace Bizgo\Exception;

use Bizgo\Result\BulkSendResult;

/**
 * A bulk send stopped early because of a configuration error or a PHP `Error` (not an API or network failure,
 * which are reported per chunk). {@see self::$partial} holds the chunks already answered, so accepted messages are
 * never lost: do not resend those.
 */
final class BulkSendException extends \RuntimeException implements BizgoException
{
    public function __construct(
        /** Results of the chunks sent before the stop (the chunk that stopped is not in it). */
        public readonly BulkSendResult $partial,
        /** The chunk that could not be sent. */
        public readonly int $chunkIndex,
        \Throwable $previous,
    ) {
        parent::__construct(sprintf(
            '대량 발송이 %d번 청크에서 멈췄습니다 (%s). 이미 접수된 청크 %d개의 결과는 partial에 있습니다',
            $chunkIndex,
            $previous::class,
            count(array_filter($partial->results, static fn (mixed $r): bool => $r !== null)),
        ), 0, $previous);
    }
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
