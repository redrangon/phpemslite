<?php

namespace PHPEMS\Lib\Rules;

class Message
{
    public const ROLE_USER = 'user';
    public const ROLE_ASSISTANT = 'assistant';
    public const ROLE_SYSTEM = 'system';
    public $role;
    public $content;
    public $name = null;

    public function __construct(string $role,string $content,string $name = null) {
        $this->role = $role;
        $this->content = $content;
        $this->name = $name;
    }

    public function toArray(): array
    {
        $data = ['role' => $this->role, 'content' => $this->content];
        if ($this->name) {
            $data['name'] = $this->name;
        }
        return $data;
    }

    public static function user(string $content): self
    {
        return new self(self::ROLE_USER, $content);
    }

    public static function assistant(string $content): self
    {
        return new self(self::ROLE_ASSISTANT, $content);
    }

    public static function system(string $content): self
    {
        return new self(self::ROLE_SYSTEM, $content);
    }
}