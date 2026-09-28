<?php

declare(strict_types=1);

namespace Bizgo\Exception;

/** The request was rejected as invalid (HTTP 400, or a service code documented as 400). */
final class BadRequestException extends ApiException
{
}
