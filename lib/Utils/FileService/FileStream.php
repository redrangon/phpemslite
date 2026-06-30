<?php

namespace PHPEMS\Lib\Utils\FileService;
use RuntimeException;
class FileStream
{
    private $resource; //@var resource
    private $seekable;
    private $writable;
    private $readable;

    /**
     * @param string $file
     * $_FILES[0][tmp_name]
     */
    public function __construct(string $file)
    {
        if (!is_file($file)) {
            throw new RuntimeException("File not found: $file");
        }
        $this->resource = fopen($file, 'r');
        $meta = stream_get_meta_data($this->resource);
        $this->seekable = $meta['seekable'];
        $this->readable = true;
        $this->writable = false;
    }

    public function __toString(): string
    {
        if (!$this->resource) {
            return '';
        }
        $this->rewind();
        return stream_get_contents($this->resource);
    }

    public function close(): void
    {
        if ($this->resource) {
            fclose($this->resource);
            $this->resource = null;
        }
    }

    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    public function getSize(): ?int
    {
        if (!$this->resource) {
            return null;
        }
        $stats = fstat($this->resource);
        return $stats['size'] ?? null;
    }

    public function tell(): int
    {
        if (!$this->resource) {
            throw new RuntimeException('Stream is detached');
        }
        $result = ftell($this->resource);
        if ($result === false) {
            throw new RuntimeException('Unable to determine stream position');
        }
        return $result;
    }

    public function eof(): bool
    {
        return !$this->resource || feof($this->resource);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->resource) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->seekable) {
            throw new RuntimeException('Stream is not seekable');
        }
        if (fseek($this->resource, $offset, $whence) !== 0) {
            throw new RuntimeException('Unable to seek to stream position');
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write($string): int
    {
        throw new RuntimeException('Read-only stream');
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read($length): string
    {
        if (!$this->resource) {
            throw new RuntimeException('Stream is detached');
        }
        if ($length < 0) {
            throw new RuntimeException('Length must be >= 0');
        }
        return fread($this->resource, $length);
    }

    public function getContents(): string
    {
        if (!$this->resource) {
            throw new RuntimeException('Stream is detached');
        }
        return stream_get_contents($this->resource);
    }

    public function getMetadata($key = null)
    {
        if (!$this->resource) {
            return $key ? null : [];
        }
        $meta = stream_get_meta_data($this->resource);
        if ($key === null) {
            return $meta;
        }
        return $meta[$key] ?? null;
    }
}