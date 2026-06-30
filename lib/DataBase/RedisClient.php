<?php

namespace PHPEMS\Lib\DataBase;

use PHPEMS\Lib\Config\Config;

class RedisClient
{
    protected $client;
    protected $config;
    protected static $instances = [];

    /**
     * 获取指定名称的 Redis 实例（支持多连接）
     */
    public static function getInstance(array $config): self
    {
        if (!isset(self::$instances[$config['name']])) {
            self::$instances[$config['name']] = new self($config);
        }
        return self::$instances[$config['name']];
    }

    /**
     * @param array $config 配置数组，示例：
     * [
     *     'host' => '127.0.0.1',
     *     'port' => 6379,
     *     'password' => null,
     *     'database' => 0,
     *     'timeout' => 0.0,
     *     'retry_interval' => 0,
     *     'read_timeout' => 0.0,
     * ]
     */
    public function __construct(array $config = [])
    {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('Redis extension (phpredis) is not installed.');
        }

        $this->config = array_merge([
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => null,
            'database' => 0,
            'timeout' => 0.0,
            'retry_interval' => 0,
            'read_timeout' => 0.0,
        ], $config);

        $this->client = new \Redis();
        $this->connect();
    }

    protected function connect(): void
    {
        $config = $this->config;

        // 支持 Unix Socket
        if (isset($config['path'])) {
            $connected = $this->client->connect($config['path'], $config['timeout'] ?? 0);
        } else {
            $connected = $this->client->connect(
                $config['host'],
                $config['port'],
                $config['timeout'],
                null,
                $config['retry_interval']
            );
        }

        if (!$connected) {
            throw new \RuntimeException('Failed to connect to Redis server.');
        }

        if ($config['read_timeout'] > 0) {
            $this->client->setOption(\Redis::OPT_READ_TIMEOUT, $config['read_timeout']);
        }

        if (!empty($config['password'])) {
            $this->client->auth($config['password']);
        }

        if (isset($config['database']) && $config['database'] !== 0) {
            $this->client->select($config['database']);
        }
    }

    // --- 基础操作 ---
    public function get(string $key)
    {
        return $this->client->get($key);
    }

    public function set(string $key, $value, ?int $ttl = null): bool
    {
        if ($ttl !== null) {
            return $this->client->setex($key, $ttl, $value);
        }
        return $this->client->set($key, $value);
    }

    public function delete($keys): int
    {
        $keys = is_array($keys) ? $keys : [$keys];
        return $this->client->del($keys);
    }

    public function exists(string $key): bool
    {
        return $this->client->exists($key) > 0;
    }

    public function ttl(string $key): int
    {
        return $this->client->ttl($key);
    }

    public function expire(string $key, int $seconds): bool
    {
        return $this->client->expire($key, $seconds);
    }

    // --- Hash 操作 ---
    public function hGet(string $key, string $field)
    {
        return $this->client->hGet($key, $field);
    }

    public function hSet(string $key, string $field, $value): int
    {
        return $this->client->hSet($key, $field, $value);
    }

    public function hGetAll(string $key): array
    {
        return $this->client->hGetAll($key) ?: [];
    }

    public function hDel(string $key, $fields): int
    {
        $fields = is_array($fields) ? $fields : [$fields];
        return $this->client->hDel($key, ...$fields);
    }

    // --- List 操作 ---
    public function lPush(string $key, ...$values): int
    {
        return $this->client->lPush($key, ...$values);
    }

    public function rPop(string $key)
    {
        return $this->client->rPop($key);
    }

    public function lRange(string $key, int $start, int $stop): array
    {
        return $this->client->lRange($key, $start, $stop) ?: [];
    }

    // --- Set 操作 ---
    public function sAdd(string $key, ...$values): int
    {
        return $this->client->sAdd($key, ...$values);
    }

    public function sMembers(string $key): array
    {
        return $this->client->sMembers($key) ?: [];
    }

    // --- Pub/Sub ---
    public function publish(string $channel, string $message): int
    {
        return $this->client->publish($channel, $message);
    }

    // --- 其他 ---
    public function flushDb(): bool
    {
        return $this->client->flushDB();
    }

    public function ping(): string
    {
        return $this->client->ping();
    }

    // 获取原生 Redis 实例（用于高级操作）
    public function getClient(): \Redis
    {
        return $this->client;
    }
}