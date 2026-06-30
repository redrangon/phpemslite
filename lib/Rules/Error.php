<?php

namespace PHPEMS\Lib\Rules;

class Error
{
    private $data = [];
    private $statusCode = 300;
    public function __construct($data) {
        $this->data = $data;
        $this->statusCode = $data['code']??300;
    }

    public function __invoke(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function create(array $msg): self
    {
        return new self($msg);
    }
}