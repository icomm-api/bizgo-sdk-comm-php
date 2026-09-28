<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Every `php` code block of README.md passes PHPStan at level max with the strict rules (SDK-DESIGN.md §12.13).
 * Each block is analysed as its own file, after a small preamble that defines the variables the prose refers to.
 */
final class ReadmeTest extends TestCase
{
    private const PREAMBLE = <<<'PHP'
        $client = (new \Bizgo\Testing\FakeTransport())->client();
        $msgKey = 'KEY001';
        $requestId = 'REQ001';
        $bytes = 'jpeg-bytes';
        $numbers = ['01000000000', '01000000001'];
        $logger = new \Psr\Log\NullLogger();
        $db = new class {
            public function upsert(?string $msgKey, ?string $code): void
            {
            }
        };
        PHP;

    public function testReadmeCodeBlocksPassPhpstan(): void
    {
        $readme = (string) file_get_contents(__DIR__ . '/../README.md');
        preg_match_all('/```php\n(.*?)```/s', $readme, $m);
        self::assertNotEmpty($m[1]);
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bizgo-readme-' . bin2hex(random_bytes(4));
        mkdir($dir . '/src', 0o777, true);
        try {
            foreach ($m[1] as $i => $code) {
                $uses = [];
                $body = [];
                foreach (explode("\n", $code) as $line) {
                    if (str_starts_with($line, 'use ')) {
                        $uses[] = $line;
                    } else {
                        $body[] = $line;
                    }
                }
                $source = "<?php\n\ndeclare(strict_types=1);\n\nnamespace Bizgo\\Readme\\Block{$i};\n\n" . implode("\n", $uses) . "\n\n"
                    . self::PREAMBLE . "\n\n" . implode("\n", $body) . "\n";
                file_put_contents("{$dir}/src/block{$i}.php", $source);
            }
            $root = dirname(__DIR__);
            $config = "{$dir}/phpstan.neon";
            file_put_contents($config, "includes:\n    - {$root}/vendor/phpstan/phpstan-strict-rules/rules.neon\n"
                . "parameters:\n    level: max\n    phpVersion: 80200\n    tmpDir: {$dir}/cache\n");
            $ini = php_ini_loaded_file();
            $command = escapeshellarg(PHP_BINARY) . (is_string($ini) ? ' -c ' . escapeshellarg($ini) : '') . ' -d memory_limit=1G '
                . escapeshellarg($root . '/vendor/phpstan/phpstan/phpstan') . ' analyse --no-progress --error-format=raw -c '
                . escapeshellarg($config) . ' --autoload-file=' . escapeshellarg($root . '/vendor/autoload.php') . ' ' . escapeshellarg($dir . '/src') . ' 2>&1';
            exec($command, $output, $status);
            self::assertSame(0, $status, "README code blocks fail PHPStan:\n" . implode("\n", $output));
        } finally {
            self::remove($dir);
        }
    }

    private static function remove(string $path): void
    {
        if (is_dir($path)) {
            foreach ((array) scandir($path) as $entry) {
                if (is_string($entry) && $entry !== '.' && $entry !== '..') {
                    self::remove($path . DIRECTORY_SEPARATOR . $entry);
                }
            }
            rmdir($path);
        } elseif (is_file($path)) {
            unlink($path);
        }
    }
}
