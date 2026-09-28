<?php

declare(strict_types=1);

namespace Bizgo;

use Bizgo\Exception\ValidationException;

/**
 * A file to upload, read once into memory so a retry sends exactly the same bytes.
 *
 * The upload methods accept a local path (string or `SplFileInfo`), an open stream resource, or an instance of
 * this class (use {@see self::fromContents()} for bytes you already have, or to choose the file name).
 */
final readonly class Upload
{
    private const TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'mp4' => 'video/mp4',
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
    ];

    private function __construct(
        /** File name sent in the multipart part (no directories). */
        public string $filename,
        /** File bytes. */
        #[\SensitiveParameter]
        public string $contents,
        /** Part Content-Type, from the file extension unless given. */
        public string $contentType,
    ) {
    }

    /**
     * Bytes you already have, for example from a database or an HTTP upload.
     */
    public static function fromContents(
        #[\SensitiveParameter]
        string $contents,
        string $filename = 'image.jpg',
        ?string $contentType = null,
    ): self {
        $name = self::baseName($filename);

        return new self($name, $contents, $contentType ?? self::typeOf($name));
    }

    /**
     * A local file. Stream wrappers and URLs (`http://`, `phar://`, ...) are refused: only plain paths are read.
     *
     * A missing or unreadable file (also one outside `open_basedir`) is reported as a ValidationException that
     * names the file name only, never the full path, and without PHP warnings.
     *
     * @throws ValidationException when the file cannot be read
     */
    public static function fromPath(
        #[\SensitiveParameter]
        string|\SplFileInfo $path,
        ?string $filename = null,
        string $field = 'file',
    ): self {
        $path = $path instanceof \SplFileInfo ? $path->getPathname() : $path;
        if ($path === '' || str_contains($path, "\0") || preg_match('~^[a-z][a-z0-9+.-]*://~i', $path) === 1) {
            throw ValidationException::of($field, '로컬 파일 경로만 쓸 수 있습니다 (URL, 스트림 래퍼 불가)');
        }
        $name = self::baseName($path);
        // is_file()/file_get_contents() warn under open_basedir or on permission errors: report the SDK error instead
        set_error_handler(static fn (): bool => true);
        try {
            $contents = is_file($path) && is_readable($path) ? file_get_contents($path) : false;
        } catch (\Throwable) {
            $contents = false;
        } finally {
            restore_error_handler();
        }
        if (!is_string($contents)) {
            throw ValidationException::of($field, "파일을 읽을 수 없습니다 ({$name}: 없거나 읽을 권한이 없음)");
        }

        return self::fromContents($contents, $filename ?? $name);
    }

    /**
     * An open, readable stream. It is read from its current position to the end.
     *
     * @param resource $stream
     *
     * @throws ValidationException when the stream cannot be read
     */
    public static function fromStream(#[\SensitiveParameter] mixed $stream, ?string $filename = null, string $field = 'file'): self
    {
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw ValidationException::of($field, '읽을 수 있는 스트림이어야 합니다');
        }
        set_error_handler(static fn (): bool => true);
        try {
            $contents = stream_get_contents($stream);
        } finally {
            restore_error_handler();
        }
        if ($contents === false) {
            throw ValidationException::of($field, '스트림을 읽을 수 없습니다');
        }
        $uri = stream_get_meta_data($stream)['uri'] ?? '';

        return self::fromContents($contents, $filename ?? ($uri !== '' ? self::baseName($uri) : 'image.jpg'));
    }

    /**
     * Normalises what the upload methods accept.
     *
     * @param string|\SplFileInfo|self|resource $file
     *
     * @throws ValidationException
     *
     * @internal
     */
    public static function from(#[\SensitiveParameter] mixed $file, #[\SensitiveParameter] ?string $filename, string $field = 'file'): self
    {
        if ($file instanceof self) {
            return $filename === null ? $file : new self(self::baseName($filename), $file->contents, self::typeOf($filename));
        }
        if (is_string($file) || $file instanceof \SplFileInfo) {
            return self::fromPath($file, $filename, $field);
        }
        if (is_resource($file)) {
            return self::fromStream($file, $filename, $field);
        }
        throw ValidationException::of($field, '파일 경로, SplFileInfo, 스트림 또는 Upload여야 합니다');
    }

    /** @return array<string, mixed> */
    public function __debugInfo(): array
    {
        return ['filename' => $this->filename, 'contentType' => $this->contentType, 'size' => strlen($this->contents)];
    }

    private static function baseName(string $filename): string
    {
        $name = basename(str_replace('\\', '/', $filename));

        return $name === '' ? 'image.jpg' : $name;
    }

    private static function typeOf(string $filename): string
    {
        return self::TYPES[strtolower(pathinfo($filename, PATHINFO_EXTENSION))] ?? 'application/octet-stream';
    }
}
