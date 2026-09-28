<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/** The key is valid but not allowed to do this (blocked account, IP not allowed, ...). */
final class PermissionDeniedException extends ApiException
{
}
