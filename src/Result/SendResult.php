<?php

declare(strict_types=1);

namespace Bizgo\Result;

use Bizgo\Internal\Mask;
use Bizgo\Model\SendDestinationResult;

/**
 * Acceptance result of a send request.
 *
 * Acceptance is not delivery: the final result arrives later as a report (polling, webhook or inquiry).
 * Always check {@see self::failed()}: a request can succeed while some recipients were rejected.
 * Every destination is in exactly one of {@see self::succeeded()}, {@see self::duplicates()} and {@see self::failed()}.
 */
final readonly class SendResult
{
    /** Per-recipient code for a recipient already accepted earlier with the same `idempotencyKey`. */
    public const DUPLICATE_CODE = 'A301';

    /**
     * @param list<SendDestinationResult> $destinations per-recipient acceptance results, in request order
     * @param string|null $ref the `ref` you sent with the request
     * @param string|null $trackingId `common.infobankTrId`, for support inquiries
     */
    public function __construct(
        public array $destinations,
        public ?string $ref = null,
        public ?string $trackingId = null,
    ) {
    }

    /**
     * Recipients accepted with code `A000`.
     *
     * @return list<SendDestinationResult>
     */
    public function succeeded(): array
    {
        return array_values(array_filter($this->destinations, static fn (SendDestinationResult $d): bool => $d->code === 'A000'));
    }

    /**
     * Recipients already accepted earlier with the same `idempotencyKey` (per-recipient code `A301`).
     *
     * A resend with the same key within `idempotencyTtl` is answered with HTTP 200 and `A301` per recipient. These
     * recipients are not sent again by this request (the earlier acceptance stands), so they are neither newly
     * accepted ({@see self::succeeded()}) nor rejected ({@see self::failed()}).
     *
     * @return list<SendDestinationResult>
     */
    public function duplicates(): array
    {
        return array_values(array_filter(
            $this->destinations,
            static fn (SendDestinationResult $d): bool => $d->code === self::DUPLICATE_CODE,
        ));
    }

    /**
     * Recipients rejected at acceptance (code other than `A000` and `A301`). They will not receive the message.
     *
     * Retrying with the same `idempotencyKey` is safe: recipients already accepted come back in
     * {@see self::duplicates()}, not here.
     *
     * @return list<SendDestinationResult>
     */
    public function failed(): array
    {
        return array_values(array_filter(
            $this->destinations,
            static fn (SendDestinationResult $d): bool => $d->code !== 'A000' && $d->code !== self::DUPLICATE_CODE,
        ));
    }

    /**
     * Message keys of recipients newly accepted by this request ({@see self::succeeded()}), for report and status lookups.
     *
     * @return list<string>
     */
    public function msgKeys(): array
    {
        $keys = [];
        foreach ($this->succeeded() as $destination) {
            if ($destination->msgKey !== null && $destination->msgKey !== '') {
                $keys[] = $destination->msgKey;
            }
        }

        return $keys;
    }

    /**
     * For var_dump()/print_r(): phone numbers masked and message content hidden (the properties keep the real values).
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return Mask::fields(get_object_vars($this));
    }

    /** A log-safe description. */
    public function __toString(): string
    {
        return Mask::describe('SendResult', get_object_vars($this));
    }
}
