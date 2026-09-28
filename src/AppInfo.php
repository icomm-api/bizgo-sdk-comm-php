<?php

declare(strict_types=1);

namespace Bizgo;

use Bizgo\Exception\ConfigurationException;

/**
 * Your application's name and version, appended to the `User-Agent` header as `app/<name>-<version>` so Bizgo can
 * see which integration sends a request (SDK-DESIGN.md §2.1).
 *
 * Only `[A-Za-z0-9._-]{1,50}` (name) and `[A-Za-z0-9._+-]{1,30}` (version) are accepted, so the value can never
 * inject a header. Do not put personal data (e-mail addresses, phone numbers, customer names) in it.
 */
final readonly class AppInfo
{
    /**
     * @throws ConfigurationException the name or version has other characters or is too long
     */
    public function __construct(
        public string $name,
        public string $version,
    ) {
        if (preg_match('/^[A-Za-z0-9._-]{1,50}$/D', $name) !== 1) {
            throw new ConfigurationException('appInfo name은 영문·숫자·._- 1~50자여야 합니다');
        }
        if (preg_match('/^[A-Za-z0-9._+-]{1,30}$/D', $version) !== 1) {
            throw new ConfigurationException('appInfo version은 영문·숫자·._+- 1~30자여야 합니다');
        }
    }

    /** `app/<name>-<version>` */
    public function userAgentToken(): string
    {
        return 'app/' . $this->name . '-' . $this->version;
    }
}
