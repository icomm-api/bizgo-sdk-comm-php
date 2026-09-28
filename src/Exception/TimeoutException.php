<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/** The request timed out (connect or total timeout). */
final class TimeoutException extends ConnectionException
{
}
