<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/** The API key is missing, wrong or expired. Check the key and that the calling server IP is registered. */
final class AuthenticationException extends ApiException
{
}
