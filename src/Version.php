<?php

declare(strict_types=1);

namespace Bizgo;

/** SDK version. The release workflow checks that the git tag (`v1.2.0`) matches {@see self::SDK}. */
final class Version
{
    public const SDK = '1.2.0';

    private function __construct()
    {
    }
}
