<?php
// Core/Session/SessionManager.php

namespace PHPEMS\Lib\Session;

use PHPEMS\Lib\Session\Drivers\NativeSessionDriver;
use PHPEMS\Lib\Session\Drivers\RedisSessionDriver;

class SessionProvider
{
    protected static $instance = null;
    protected $driver;

    private function __construct(string $driver , array $config = [])
    {
        $this->driver = match ($driver) {
            'native' => new NativeSessionDriver($config),
            'redis' => new RedisSessionDriver($config),
            default => throw new \InvalidArgumentException("Unsupported session driver: {$driver}"),
        };
        $this->driver->start();
    }

    public static function getInstance(string $driver = 'redis', array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($driver, $config);
        }
        return self::$instance;
    }

    public static function has(string $key): bool
    {
        $instance = self::getInstance();
        return $instance->driver->get($key) !== null;
    }

    // 静态快捷方法（可选）
    public static function __callStatic($name, $arguments)
    {
        $instance = self::getInstance();
        if (!method_exists($instance->driver, $name)) {
            throw new \BadMethodCallException("Method [{$name}] not exists on session driver.");
        }
        return $instance->driver->$name(...$arguments);
    }
}