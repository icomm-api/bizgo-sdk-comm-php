<?php

declare(strict_types=1);

namespace Bizgo\Observability;

/**
 * Request start/end callbacks for metrics and tracing (SDK-DESIGN.md §11.4).
 *
 * ```php
 * final class Metrics implements Hook
 * {
 *     public function onRequestStart(RequestEvent $event): void {}
 *
 *     public function onRequestEnd(RequestEvent $event): void
 *     {
 *         histogram('bizgo.request', $event->durationMs, ['op' => $event->operation, 'status' => $event->status]);
 *     }
 * }
 * $client = new BizgoClient(hooks: [new Metrics()]);
 * ```
 *
 * One API call (with its retries) produces one start and one end, with the same {@see RequestEvent}. Anything a
 * hook throws is caught and ignored (a warning is logged once per hook class), so observability can never change a
 * result, a retry or a bulk send. Hooks run in the calling process; keep them fast.
 */
interface Hook
{
    /** Before the first attempt. Only the operation fields are set. */
    public function onRequestStart(RequestEvent $event): void;

    /** After the call finished, successfully or not, after all retries. */
    public function onRequestEnd(RequestEvent $event): void;
}
