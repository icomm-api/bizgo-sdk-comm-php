<?php

declare(strict_types=1);

namespace Bizgo\Http;

use Bizgo\Upload;

/**
 * A `multipart/form-data` body built once, so every retry sends the same bytes.
 *
 * @internal
 */
final readonly class Multipart
{
    /**
     * @param list<string> $names form field names, in order (for the testing tools; never the values)
     */
    private function __construct(
        public string $boundary,
        #[\SensitiveParameter]
        public string $body,
        public array $names = [],
    ) {
    }

    /**
     * @param array<string, string|null> $fields text fields; null values are left out
     */
    public static function build(string $fileField, #[\SensitiveParameter] Upload $file, #[\SensitiveParameter] array $fields): self
    {
        $parts = [['name' => $fileField, 'contents' => $file->contents, 'filename' => $file->filename, 'type' => $file->contentType]];
        foreach ($fields as $name => $value) {
            if ($value !== null) {
                $parts[] = ['name' => $name, 'contents' => $value, 'filename' => null, 'type' => null];
            }
        }

        return self::fromParts($parts);
    }

    /**
     * @param list<array{name: string, contents: string, filename: string|null, type: string|null}> $parts
     *        file parts have a filename; a JSON part has type `application/json` and no filename
     */
    public static function fromParts(#[\SensitiveParameter] array $parts): self
    {
        do {
            $boundary = '----bizgo-' . bin2hex(random_bytes(16));
            $found = false;
            foreach ($parts as $part) {
                $found = $found || str_contains($part['contents'], $boundary);
            }
        } while ($found);

        $body = '';
        $names = [];
        foreach ($parts as $part) {
            $names[] = $part['name'];
            $body .= '--' . $boundary . "\r\n" . 'Content-Disposition: form-data; name="' . self::quote($part['name']) . '"';
            if ($part['filename'] !== null) {
                $body .= '; filename="' . self::quote($part['filename']) . '"';
            }
            $body .= "\r\n";
            if ($part['type'] !== null) {
                $body .= 'Content-Type: ' . self::quote($part['type']) . "\r\n";
            }
            $body .= "\r\n" . $part['contents'] . "\r\n";
        }
        $body .= '--' . $boundary . "--\r\n";

        return new self($boundary, $body, $names);
    }

    public function contentType(): string
    {
        return 'multipart/form-data; boundary=' . $this->boundary;
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return ['names' => $this->names, 'size' => strlen($this->body)];
    }

    /** Escapes a Content-Disposition parameter; control characters (header injection) are dropped. */
    private static function quote(string $value): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value) ?? '';

        return str_replace(['\\', '"'], ['\\\\', '%22'], $value);
    }
}
