<?php
// Core/Session/Drivers/RedisSessionDriver.php

namespace PHPEMS\Lib\Session\Drivers;

use PHPEMS\Lib\Config\Config;
use PHPEMS\Lib\DataBase\RedisClient;
use PHPEMS\Lib\Session\SessionDriverInterface;

class RedisSessionDriver implements SessionDriverInterface
{
    protected $redis;
    protected $prefix;
    protected $lifetime;

    /**
     * @param array $config 配置示例：
     * [
     *     'connection_name' => 'session',   // 可选，用于多连接
     *     'redis' => [                      // RedisClient 配置（仅首次使用时需要）
     *         'host' => '127.0.0.1',
     *         'port' => 6379,
     *         'password' => null,
     *         'database' => 1,
     *     ],
     *     'prefix' => 'sess:',
     *     'lifetime' => 7200,
     * ]
     */
    public function __construct(array $config)
    {
        // 使用 RedisClient 单例获取连接
        $redisClient = DI('redis.default');
        $this->redis = $redisClient->getClient(); // 获取底层 \Redis 实例
        $this->prefix = $config['prefix'] ?? 'sess:';
        $this->lifetime = $config['lifetime'] ?? 1440;
        $this->registerHandlers();
    }

    protected function registerHandlers(): void
    {
        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroyHandler'], // 注意：这里指向 destroyHandler
            [$this, 'gc']
        );
    }

    // --- Handler for session_destroy() callback ---
    public function destroyHandler(string $id): bool
    {
        $result = $this->redis->del($this->prefix . $id);
        if($result === false)return false;
        return true;
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- Session Handler Interface ---
    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        $data = $this->redis->get($this->prefix . $id);
        return $data ?: '';
    }

    public function write($id, $data): bool
    {
        // 使用 setex 自动设置过期时间
        return $this->redis->setex($this->prefix . $id, $this->lifetime, $data);
    }    

    public function gc($maxlifetime): int
    {
        // Redis 依靠 TTL 自动过期，无需手动清理
        return 0;
    }

    // --- SessionDriverInterface 方法 ---
    public function get(string $key, $default = null)
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function delete(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        $this->start();
        $_SESSION = [];
    }

    public function regenerateId(): void
    {
        $this->start();
        session_regenerate_id(true);
    }
    
    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION = [];
        }
    }
}