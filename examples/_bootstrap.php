<?php

declare(strict_types=1);

// Shared helpers for the examples: read settings from environment variables (never hard-code keys or numbers).

namespace Bizgo\Examples;

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

function env(string $name): string
{
    $value = getenv($name);
    if (!is_string($value) || $value === '') {
        fwrite(STDERR, "환경변수 {$name}를 설정하세요 (examples/README.md 참고)\n");
        exit(1);
    }

    return $value;
}

/** True when the file is run directly (`php examples/send_sms.php`), false when it is included (tests). */
function isMain(string $file): bool
{
    $script = $_SERVER['SCRIPT_FILENAME'] ?? null;

    return is_string($script) && realpath($script) === realpath($file);
}
