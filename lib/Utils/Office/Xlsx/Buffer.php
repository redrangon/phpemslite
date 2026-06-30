<?php

namespace PHPEMS\Lib\Utils\Office\Xlsx;

class Buffer
{
    const BUFFER_FLUSH_SIZE = 8191;

    protected mixed $fd = null;
    protected string $buffer = '';
    protected bool $check_utf8 = false;

    public function __construct(string $filename, string $fd_fopen_flags = 'w', bool $check_utf8 = false)
    {
        $this->check_utf8 = $check_utf8;
        $this->fd = fopen($filename, $fd_fopen_flags);
        if ($this->fd === false) {
            Writer::log("Unable to open $filename for writing.");
        }
    }

    public function write(string $string): void
    {
        $this->buffer .= $string;
        if (isset($this->buffer[self::BUFFER_FLUSH_SIZE])) {
            $this->purge();
        }
    }

    protected function purge(): void
    {
        if ($this->fd) {
            if ($this->check_utf8 && !self::isValidUTF8($this->buffer)) {
                Writer::log("Error, invalid UTF8 encoding detected.");
                $this->check_utf8 = false;
            }
            fwrite($this->fd, $this->buffer);
            $this->buffer = '';
        }
    }

    public function close(): void
    {
        $this->purge();
        if ($this->fd) {
            fclose($this->fd);
            $this->fd = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function ftell(): int
    {
        if ($this->fd) {
            $this->purge();
            return ftell($this->fd);
        }
        return -1;
    }

    public function fseek(int $pos): int
    {
        if ($this->fd) {
            $this->purge();
            return fseek($this->fd, $pos);
        }
        return -1;
    }

    protected static function isValidUTF8(string $string): bool
    {
        if (function_exists('mb_check_encoding')) {
            return mb_check_encoding($string, 'UTF-8');
        }
        return (bool)preg_match("//u", $string);
    }
}