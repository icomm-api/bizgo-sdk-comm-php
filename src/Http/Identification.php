<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\AppInfo;
use Bizgo\Version;

/**
 * SDK identification headers (SDK-DESIGN.md §2.1), built once per client:
 *
 * - `User-Agent: bizgo-sdk-comm-php/<sdk> php/<version> (<os>; <arch>)[ app/<name>-<version>]`
 * - `X-Bizgo-Client: bizgo-sdk-comm-php/<sdk>` (kept when a proxy rewrites the User-Agent)
 *
 * The OS and architecture are coarse values only (`linux`, `windows`, `darwin`, `freebsd`, `other`; `x64`, `arm64`,
 * `x86`, `arm`, `other`); no host name, user name or kernel version. Nothing is sent anywhere else.
 *
 * @internal
 */
final class Identification
{
    public const CLIENT = 'bizgo-sdk-comm-php/' . Version::SDK;

    private function __construct()
    {
    }

    public static function userAgent(?AppInfo $app = null, ?string $osFamily = null, ?string $machine = null): string
    {
        $ua = sprintf(
            '%s php/%d.%d.%d (%s; %s)',
            self::CLIENT,
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
            PHP_RELEASE_VERSION,
            self::os($osFamily ?? PHP_OS_FAMILY, PHP_OS),
            self::arch($machine ?? php_uname('m')),
        );

        return $app === null ? $ua : $ua . ' ' . $app->userAgentToken();
    }

    public static function os(string $family, string $os = ''): string
    {
        return match ($family) {
            'Linux' => 'linux',
            'Windows' => 'windows',
            'Darwin' => 'darwin',
            'BSD' => strcasecmp($os, 'FreeBSD') === 0 ? 'freebsd' : 'other',
            default => 'other',
        };
    }

    public static function arch(string $machine): string
    {
        $machine = strtolower($machine);

        return match (true) {
            in_array($machine, ['x86_64', 'amd64', 'x64'], true) => 'x64',
            in_array($machine, ['aarch64', 'arm64', 'armv8', 'armv8l'], true) => 'arm64',
            in_array($machine, ['i386', 'i486', 'i586', 'i686', 'x86'], true) => 'x86',
            str_starts_with($machine, 'arm') => 'arm',
            default => 'other',
        };
    }
}
