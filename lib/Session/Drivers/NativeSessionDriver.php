<?php
// Core/Session/Drivers/NativeSessionDriver.php

namespace PHPEMS\Lib\Session\Drivers;

use PHPEMS\Lib\Session\SessionDriverInterface;

class NativeSessionDriver implements SessionDriverInterface
{
    protected $config = [
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'samesite' => 'lax',
    ];

    public function __construct(array $config = [])
    {
        $this->config = empty($config) ? $this->config : $config;
        if (!session_id()) {
            $lifetime = $this->config['lifetime'] ?? 3600;
            ini_set('session.gc_maxlifetime', (string)$lifetime);
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => $this->config['path'] ?? '/',
                'domain' => $this->config['domain'] ?? '',
                'secure' => $this->config['secure'] ?? false,
                'httponly' => true,
                'samesite' => $this->config['samesite'] ?? 'lax',
            ]);
        }
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }
}