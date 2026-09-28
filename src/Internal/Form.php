<?php

declare(strict_types=1);

namespace Bizgo\Internal;

use Bizgo\Exception\ValidationException;
use Bizgo\Http\Multipart;
use Bizgo\Model\RequestModel;
use Bizgo\Upload;

/**
 * Builds a `multipart/form-data` body for the generated upload methods. Files are read once ({@see Upload}), so
 * a retry sends the same bytes. JSON parts (`encoding: {<field>: {contentType: application/json}}`) are validated
 * request models.
 *
 * @internal
 */
final class Form
{
    /** @var list<array{name: string, contents: string, filename: string|null, type: string|null}> */
    private array $parts = [];

    /**
     * @param string|\SplFileInfo|Upload|resource|null $value
     *
     * @throws ValidationException missing required file, unreadable file or larger than `$maxBytes`
     */
    public function file(string $name, #[\SensitiveParameter] mixed $value, bool $required, ?int $maxBytes = null): self
    {
        if ($value === null) {
            if ($required) {
                throw ValidationException::of($name, '필수 파일입니다');
            }

            return $this;
        }
        $upload = Upload::from($value, null, $name);
        if ($maxBytes !== null && strlen($upload->contents) > $maxBytes) {
            throw ValidationException::of($name, sprintf('파일은 최대 %sbyte입니다', number_format($maxBytes)));
        }
        $this->addFile($name, $upload);

        return $this;
    }

    /**
     * @param list<string|\SplFileInfo|Upload|resource>|null $values
     *
     * @throws ValidationException
     */
    public function files(string $name, #[\SensitiveParameter] ?array $values, ?int $maxItems = null): self
    {
        if ($values === null) {
            return $this;
        }
        if (!array_is_list($values)) {
            throw ValidationException::of($name, '파일 목록(list)이어야 합니다');
        }
        if ($maxItems !== null && count($values) > $maxItems) {
            throw ValidationException::of($name, "파일은 최대 {$maxItems}개입니다");
        }
        foreach ($values as $i => $value) {
            $upload = Upload::from($value, null, "{$name}[{$i}]");
            $this->addFile($name, $upload);
        }

        return $this;
    }

    /** @throws ValidationException the model cannot be encoded */
    public function json(string $name, #[\SensitiveParameter] ?RequestModel $value): self
    {
        if ($value === null) {
            return $this;
        }
        try {
            $json = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\JsonException) {
            throw ValidationException::of($name, 'JSON으로 만들 수 없습니다 (UTF-8이 아닌 문자열 등)');
        }
        $this->parts[] = ['name' => $name, 'contents' => $json, 'filename' => null, 'type' => 'application/json'];

        return $this;
    }

    public function text(string $name, #[\SensitiveParameter] ?string $value): self
    {
        if ($value !== null) {
            $this->parts[] = ['name' => $name, 'contents' => $value, 'filename' => null, 'type' => null];
        }

        return $this;
    }

    public function build(): Multipart
    {
        return Multipart::fromParts($this->parts);
    }

    private function addFile(string $name, Upload $upload): void
    {
        $this->parts[] = [
            'name' => $name,
            'contents' => $upload->contents,
            'filename' => $upload->filename,
            'type' => $upload->contentType,
        ];
    }
}
