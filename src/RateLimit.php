<?php

declare(strict_types=1);

namespace Bizgo;

use Bizgo\Exception\ConfigurationException;

/**
 * Client-side rate limits (SDK-DESIGN.md §11.2), one token bucket each:
 *
 * - **send**: messages (recipients) per second, default 200. A send request costs as many tokens as it has
 *   `destinations` (minimum 1), so a 200-recipient request uses a whole second. Only the operations marked
 *   `x-sdk-rate: send` in the spec use it: `sendOmni` (`$client->send->omni/request/sms/lms/mms/bulk`),
 *   `createReservation`, `addReservationRecipients`, `createBrandMessageGroupSend` and the counsel sends
 *   `sendCounselPlain`/`sendCounselRich` (cost 1, no recipient list).
 * - **other**: requests per second for every other API, default 5.
 *
 * A bucket holds one second of tokens (capacity = rate) and starts full, like the server's token bucket. Every HTTP
 * attempt, retries included, waits (usleep) until its tokens are there. A request that costs more than the capacity
 * waits until the bucket is full and leaves the balance negative, so it never blocks forever and the average rate holds.
 *
 * The limits belong to one client instance in one PHP process. The Bizgo limit is per account: several PHP-FPM
 * workers, queue workers or servers using the same key do not share these buckets, so coordinate them yourself
 * (or lower the rates per process); HTTP 429 is still retried. Pass `rateLimit: null` to the client to turn it off.
 */
final readonly class RateLimit
{
    /**
     * @param float $send messages (recipients) per second for send operations
     * @param float $other requests per second for every other operation
     *
     * @throws ConfigurationException a rate is not a finite number above 0
     */
    public function __construct(
        public float $send = 200.0,
        public float $other = 5.0,
    ) {
        foreach (['send' => $send, 'other' => $other] as $name => $rate) {
            if (!is_finite($rate) || $rate <= 0) {
                throw new ConfigurationException("RateLimit {$name}는 0보다 큰 초당 한도여야 합니다");
            }
        }
    }
}
