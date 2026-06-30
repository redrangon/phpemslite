<?php

namespace PHPEMS\Lib\Auth\Drivers;

use PHPEMS\Lib\Session\SessionProvider;

class RedisDriver extends Driver implements DriverInterface
{
    protected $dbName = 'redis.default';
    protected $db;

    private static $instance;

    private function __construct(?string $dbName = null)
    {
        if($dbName??false){
            $this->dbName = $dbName;
        }
        $this->db = DI($this->dbName)->getClient();
    }

    public static function getInstance(?string $dbName = null): self
    {
        if (!self::$instance) {
            self::$instance = new self($dbName);
        }
        return self::$instance;
    }

    public function bind(string $sessionId, int $userId, string $authToken)
    {
        $this->db->setex("user:{$userId}:current_session", self::SESSION_LIFETIME, $sessionId);
        $this->db->setex("session:{$sessionId}:auth_token", self::SESSION_LIFETIME, $authToken);
        $this->db->setex("session:{$sessionId}:auth_salt", self::SESSION_LIFETIME, bin2hex(random_bytes(32)));
    }

    public function forget(string $sessionId, int $userId)
    {
        $oldSessionId = $this->db->get("user:{$userId}:current_session");
        if ($oldSessionId && $oldSessionId !== $sessionId) {
            $this->db->del("session:{$oldSessionId}:auth_token");
            $this->db->del("session:{$oldSessionId}:auth_salt");
        }
    }

    public function getAuthToken(string $sessionId): string
    {
        return $this->db->get("session:{$sessionId}:auth_token");
    }

    public function getAuthSalt(string $sessionId): string
    {
        return $this->db->get("session:{$sessionId}:auth_salt");
    }
}