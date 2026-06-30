<?php

namespace PHPEMS\Lib\DataBase;
use PDO;
use PDOException;
use Closure;
use PDOStatement;
use PHPEMS\Lib\Config\Config;

class DB
{
    /**
     * 所有数据库连接实例（按配置名索引）
     * @var array<string, self>
     */
    private static array $instances = [];

    /**
     * 当前活跃的连接名（默认 'default'）
     * @var string
     */
    private static $currentConnection;

    /** @var PDO */
    private PDO $pdo;

    /** @var Config */
    protected Config $config;

    /**
     * 私有构造函数，防止外部实例化
     */
    private function __construct($config)
    {
        // 如果配置中有自定义 DSN，直接使用
        if (isset($config->dsn)) {
            $dsn = $config->dsn;
        } else {
            $dsn = "{$config->type}:host={$config->host};dbname={$config->database};charset={$config->charset}";
        }
        $options = $config->options ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO(
                $dsn,
                $config->user ?? '',
                $config->password ?? '',
                $options
            );
            $this->config = $config;
        } catch (PDOException $e) {
            //
        }
    }

    public function getConfig($key)
    {
        return $this->config->$key??null;
    }

    /**
     * 获取或创建指定名称的数据库连接实例
     */
    public static function getInstance(Config $config):static
    {
        if (!isset(self::$instances[$config->name])) {
            self::$instances[$config->name] = new self($config);
        }
        return self::$instances[$config->name];
    }

    public function query(string $sql, array $bindings = []): bool|PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    public function fetch(string $sql, array $bindings = [])
    {
        return $this->query($sql, $bindings)->fetch();
    }

    public function fetchAll(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    public function execute(string $sql, array $bindings = []): int
    {
        return $this->query($sql, $bindings)->rowCount();
    }

    public function insertGetId(string $sql, array $bindings = []): int
    {
        $this->query($sql, $bindings);
        return (int)$this->pdo->lastInsertId();
    }

    // ========== 事务控制 ==========

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * @throws PDOException
     */
    public function transaction(Closure $callback)
    {
        $this->beginTransaction();
        try {
            $result = $callback();
            $this->commit();
            return $result;
        } catch (PDOException $e) {
            $this->rollBack();
            throw $e;
        }
    }
}