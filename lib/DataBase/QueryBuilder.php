<?php

namespace PHPEMS\Lib\DataBase;

use Closure;
use PDOException;

class QueryBuilder
{
    protected $connection;
    protected $tableWithAlias = '';
    protected $table = '';
    protected $columns = ['*'];
    protected $joins = [];
    protected $wheres = [];
    protected $groups = [];
    protected $havings = [];
    protected $orders = [];
    protected $limit = null;
    protected $offset = null;

    // ============================================
    // 锁相关属性
    // ============================================
    protected $lockMode = null;          // 锁模式：'for_update', 'shared', null
    protected $lockSkipLocked = false;   // 是否使用 SKIP LOCKED
    protected $lockNowait = false;       // 是否使用 NOWAIT

    public function __construct(DB $db, string $table)
    {
        $this->connection = $db;
        if ($table) {
            $this->from($table);
        }
    }

    // ============================================
    // 锁方法
    // ============================================

    /**
     * 添加 FOR UPDATE 锁
     *
     * @param bool $skipLocked 是否使用 SKIP LOCKED
     * @param bool $nowait 是否使用 NOWAIT
     * @return self
     */
    public function lockForUpdate(bool $skipLocked = false, bool $nowait = false): self
    {
        $this->lockMode = 'for_update';
        $this->lockSkipLocked = $skipLocked;
        $this->lockNowait = $nowait;
        return $this;
    }

    /**
     * 添加 LOCK IN SHARE MODE 锁
     *
     * @param bool $skipLocked 是否使用 SKIP LOCKED
     * @param bool $nowait 是否使用 NOWAIT
     * @return self
     */
    public function sharedLock(bool $skipLocked = false, bool $nowait = false): self
    {
        $this->lockMode = 'shared';
        $this->lockSkipLocked = $skipLocked;
        $this->lockNowait = $nowait;
        return $this;
    }

    /**
     * 清除锁设置
     */
    public function clearLock(): self
    {
        $this->lockMode = null;
        $this->lockSkipLocked = false;
        $this->lockNowait = false;
        return $this;
    }

    // ============================================
    // 核心方法
    // ============================================

    /**
     * 字段名转义，防止 SQL 注入
     */
    protected function wrap(string $value): string
    {
        if ($value === '*') return $value;
        // 移除已有的反引号，然后重新包裹，防止重复包裹
        $value = str_replace('`', '', $value);
        return '`' . $value . '`';
    }

    /**
     * 转义字段名，支持 `table.column` 格式，也支持表达式
     */
    protected function wrapColumn(string $column): string
    {
        // 如果是表达式（包含括号、空格、函数调用），不转义
        if (preg_match('/[()\s,.]/', $column) && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            // 简单的字段名检测：如果是纯字母数字下划线，则转义
            // 否则视为表达式
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?$/', $column)) {
                return $column;
            }
        }

        // 如果是 table.column 格式，分别转义
        if (strpos($column, '.') !== false) {
            $parts = explode('.', $column);
            $parts = array_map([$this, 'wrap'], $parts);
            return implode('.', $parts);
        }

        return $this->wrap($column);
    }

    public function buildTableName(string $table, bool $useAlias = true): string
    {
        $table = str_replace('`', '', $table);
        if ($prefix = $this->connection->getConfig('tablePrefix')) {
            return $useAlias ? $prefix . $table . ' AS ' . $table : $prefix . $table;
        }
        return $useAlias ? $table . ' AS ' . $table : $table;
    }

    public function from(string $table): self
    {
        $this->table = $this->buildTableName($table, false);
        $this->tableWithAlias = $this->buildTableName($table);
        return $this;
    }

    public function select(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    // ===== JOIN =====

    public function join(string $table, $first, string $operator = null, string $second = null): self
    {
        $conditions = $this->normalizeJoinConditions($first, $operator, $second);
        $this->joins[] = ['type' => 'INNER', 'table' => $this->buildTableName($table), 'conditions' => $conditions];
        return $this;
    }

    public function leftJoin(string $table, $first, string $operator = null, string $second = null): self
    {
        $conditions = $this->normalizeJoinConditions($first, $operator, $second);
        $this->joins[] = ['type' => 'LEFT', 'table' => $this->buildTableName($table), 'conditions' => $conditions];
        return $this;
    }

    public function rightJoin(string $table, $first, string $operator = null, string $second = null): self
    {
        $conditions = $this->normalizeJoinConditions($first, $operator, $second);
        $this->joins[] = ['type' => 'RIGHT', 'table' => $this->buildTableName($table), 'conditions' => $conditions];
        return $this;
    }

    protected function normalizeJoinConditions($first, ?string $operator, ?string $second): array
    {
        if (is_array($first)) {
            $conditions = [];
            foreach ($first as $condition) {
                if (is_array($condition) && count($condition) === 3) {
                    $conditions[] = [
                        'first' => $condition[0],
                        'operator' => $condition[1],
                        'second' => $condition[2]
                    ];
                }
            }
            return $conditions;
        }
        return [
            ['first' => $first, 'operator' => $operator, 'second' => $second]
        ];
    }

    // ===== WHERE =====

    public function where($column, $operator = null, $value = null): self
    {
        if ($column instanceof Closure) {
            $this->wheres[] = ['type' => 'Nested', 'callback' => $column, 'boolean' => 'and'];
            return $this;
        }
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = ['type' => 'Basic', 'column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => 'and'];
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null): self
    {
        if ($column instanceof Closure) {
            $this->wheres[] = ['type' => 'Nested', 'callback' => $column, 'boolean' => 'or'];
            return $this;
        }
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = ['type' => 'Basic', 'column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => 'or'];
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = ['type' => 'In', 'column' => $column, 'values' => $values, 'not' => false, 'boolean' => 'and'];
        return $this;
    }

    public function whereNotIn(string $column, array $values): self
    {
        $this->wheres[] = ['type' => 'In', 'column' => $column, 'values' => $values, 'not' => true, 'boolean' => 'and'];
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = ['type' => 'Null', 'column' => $column, 'not' => false, 'boolean' => 'and'];
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = ['type' => 'Null', 'column' => $column, 'not' => true, 'boolean' => 'and'];
        return $this;
    }

    public function whereRaw(string $sql, array $bindings = []): self
    {
        $this->wheres[] = [
            'type' => 'Raw',
            'sql' => $sql,
            'bindings' => $bindings,
            'boolean' => 'and'
        ];
        return $this;
    }

    public function orWhereRaw(string $sql, array $bindings = []): self
    {
        $this->wheres[] = [
            'type' => 'Raw',
            'sql' => $sql,
            'bindings' => $bindings,
            'boolean' => 'or'
        ];
        return $this;
    }

    // ===== GROUP / HAVING =====

    public function groupBy($columns): self
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        foreach ($columns as $column) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                $this->groups[] = $this->wrap($column);
            } else {
                $this->groups[] = $column;
            }
        }
        return $this;
    }

    public function groupByRaw(string $sql): self
    {
        $this->groups[] = $sql;
        return $this;
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->havings[] = ['type' => 'Basic', 'column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => 'and'];
        return $this;
    }

    public function havingRaw(string $sql, array $bindings = []): self
    {
        $this->havings[] = [
            'type' => 'Raw',
            'sql' => $sql,
            'bindings' => $bindings,
            'boolean' => 'and'
        ];
        return $this;
    }

    public function orHavingRaw(string $sql, array $bindings = []): self
    {
        $this->havings[] = [
            'type' => 'Raw',
            'sql' => $sql,
            'bindings' => $bindings,
            'boolean' => 'or'
        ];
        return $this;
    }

    // ===== ORDER / LIMIT =====

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';

        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            $this->orders[] = $this->wrap($column) . " {$dir}";
        } else {
            $this->orders[] = "{$column} {$dir}";
        }
        return $this;
    }

    public function orderByRaw(string $sql): self
    {
        $this->orders[] = $sql;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    // ===== 执行 =====

    public function get(): array
    {
        [$sql, $bindings] = $this->toSql();
        return $this->connection->fetchAll($sql, $bindings);
    }

    public function first(): ?array
    {
        $clone = clone $this;
        $result = $clone->limit(1)->get();
        return $result[0] ?? null;
    }

    /**
     * 获取第一个结果并加锁（FOR UPDATE SKIP LOCKED）
     * 专门用于 Worker 拉取任务
     */
    public function firstForUpdate(bool $skipLocked = true): ?array
    {
        $clone = clone $this;
        $clone->lockForUpdate($skipLocked);
        $result = $clone->limit(1)->get();
        return $result[0] ?? null;
    }

    public function count(string $column = '*'): int
    {
        $origGroups = $this->groups;
        $origHavings = $this->havings;
        $origOrders = $this->orders;
        $origLimit = $this->limit;
        $origOffset = $this->offset;
        $origColumns = $this->columns;
        $origLockMode = $this->lockMode;
        $origLockSkipLocked = $this->lockSkipLocked;
        $origLockNowait = $this->lockNowait;

        $this->groups = [];
        $this->havings = [];
        $this->orders = [];
        $this->limit = null;
        $this->offset = null;
        $this->columns = ["COUNT({$column}) as aggregate"];
        $this->lockMode = null;
        $this->lockSkipLocked = false;
        $this->lockNowait = false;

        try {
            $row = $this->first();
            return (int) ($row['aggregate'] ?? 0);
        } finally {
            $this->groups = $origGroups;
            $this->havings = $origHavings;
            $this->orders = $origOrders;
            $this->limit = $origLimit;
            $this->offset = $origOffset;
            $this->columns = $origColumns;
            $this->lockMode = $origLockMode;
            $this->lockSkipLocked = $origLockSkipLocked;
            $this->lockNowait = $origLockNowait;
        }
    }

    public function countGroup(): int
    {
        $original = $this->columns;
        $origLockMode = $this->lockMode;
        $origLockSkipLocked = $this->lockSkipLocked;
        $origLockNowait = $this->lockNowait;

        $this->columns = ['1'];
        $this->lockMode = null;
        $this->lockSkipLocked = false;
        $this->lockNowait = false;

        [$innersql, $bindings] = $this->toSubSql();
        $sql = "SELECT COUNT(*) AS aggregate FROM ({$innersql}) AS grouped";
        $result = $this->connection->fetch($sql, $bindings);

        $this->columns = $original;
        $this->lockMode = $origLockMode;
        $this->lockSkipLocked = $origLockSkipLocked;
        $this->lockNowait = $origLockNowait;

        return (int) ($result['aggregate'] ?? 0);
    }

    // ===== 分页 =====

    public function paginate(int $page = 1, int $limit = 20): array
    {
        if ($this->groups || $this->havings) {
            $total = $this->countGroup();
        } else {
            $total = $this->count();
        }
        $offset = ($page - 1) * $limit;
        $data = $this->limit($limit)->offset($offset)->get();

        return [
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
            'data' => $data
        ];
    }

    // ===== 写操作 =====

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
        $values = array_values($data);

        $values = array_map(function($item) {
            if (is_array($item)) {
                return json_encode($item, JSON_UNESCAPED_UNICODE);
            }
            return $item;
        }, $values);

        return $this->connection->insertGetId($sql, $values);
    }

    public function insertMultiple(array $data): int
    {
        if (empty($data)) {
            return 0;
        }

        $columns = array_keys($data[0]);
        $placeholders = '(' . str_repeat('?,', count($columns) - 1) . '?)';
        $valuePlaceholders = implode(', ', array_fill(0, count($data), $placeholders));

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES {$valuePlaceholders}";

        $values = [];
        foreach ($data as $row) {
            foreach ($row as $val) {
                if (is_array($val)) {
                    $values[] = json_encode($val, JSON_UNESCAPED_UNICODE);
                } else {
                    $values[] = $val;
                }
            }
        }

        return $this->connection->execute($sql, $values);
    }

    public function update(array $data): int
    {
        $set = [];
        $bindings = [];
        foreach ($data as $col => $val) {
            if($val instanceof Closure)
            {
                $set[] = "{$col} = ".$val();
            }
            else
            {
                $set[] = "{$col} = ?";
                $bindings[] = $val;
            }
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        [$whereSql, $whereBindings] = $this->compileWheres();
        if ($whereSql) {
            $sql .= " WHERE {$whereSql}";
            $bindings = array_merge($bindings, $whereBindings);
        }
        return $this->connection->execute($sql, $bindings);
    }

    public function delete(): int
    {
        if (empty($this->wheres)) {
            throw new PDOException('Delete without where clause is not allowed.');
        }
        $sql = "DELETE FROM {$this->table}";
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql .= " WHERE {$whereSql}";
        return $this->connection->execute($sql, $whereBindings);
    }

    // ============================================
    // SQL 构建
    // ============================================

    public function toSubSql(): array
    {
        $columns = implode(', ', $this->columns);
        $sql = "SELECT {$columns} FROM {$this->tableWithAlias}";

        foreach ($this->joins as $join) {
            $onConditions = [];
            foreach ($join['conditions'] as $condition) {
                $onConditions[] = "{$condition['first']} {$condition['operator']} {$condition['second']}";
            }
            $onClause = implode(' AND ', $onConditions);
            $sql .= " {$join['type']} JOIN {$join['table']} ON {$onClause}";
        }

        [$whereSql, $bindings] = $this->compileWheres();
        if ($whereSql) {
            $sql .= " WHERE {$whereSql}";
        }

        if (!empty($this->groups)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }

        [$havingSql, $havingBindings] = $this->compileHavings();
        if ($havingSql) {
            $sql .= " HAVING {$havingSql}";
            $bindings = array_merge($bindings, $havingBindings);
        }

        return [$sql, $bindings];
    }

    public function toSql(): array
    {
        [$sql, $bindings] = $this->toSubSql();

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . (int) $this->limit;
            if ($this->offset !== null) {
                $sql .= ' OFFSET ' . (int) $this->offset;
            }
        }

        // 锁支持（MySQL 8.0+）
        if ($this->lockMode !== null) {
            if ($this->lockMode === 'for_update') {
                $sql .= ' FOR UPDATE';
            } elseif ($this->lockMode === 'shared') {
                $sql .= ' LOCK IN SHARE MODE';
            }
            if ($this->lockSkipLocked) {
                $sql .= ' SKIP LOCKED';
            }
            if ($this->lockNowait) {
                $sql .= ' NOWAIT';
            }
        }

        return [$sql, $bindings];
    }

    // ============================================
    // 编译方法
    // ============================================

    protected function compileWheres(): array
    {
        return $this->compileConditions($this->wheres);
    }

    protected function compileHavings(): array
    {
        return $this->compileConditions($this->havings);
    }

    protected function compileConditions(array $conditions): array
    {
        if (empty($conditions)) {
            return ['', []];
        }

        $parts = [];
        $bindings = [];

        foreach ($conditions as $i => $cond) {
            $connector = $i === 0 ? '' : strtoupper($cond['boolean']);

            switch ($cond['type']) {
                case 'Basic':
                    $column = $this->wrapColumn($cond['column']);
                    $parts[] = ltrim("{$connector} {$column} {$cond['operator']} ?");
                    $bindings[] = $cond['value'];
                    break;

                case 'Raw':
                    $parts[] = ltrim("{$connector} ({$cond['sql']})");
                    $bindings = array_merge($bindings, $cond['bindings']);
                    break;

                case 'In':
                    $not = $cond['not'] ? 'NOT ' : '';
                    if (empty($cond['values'])) {
                        $parts[] = ltrim("{$connector} 1 = 0");
                        break;
                    }
                    $column = $this->wrapColumn($cond['column']);
                    $placeholders = str_repeat('?,', count($cond['values']) - 1) . '?';
                    $parts[] = ltrim("{$connector} {$not}{$column} IN ({$placeholders})");
                    $bindings = array_merge($bindings, $cond['values']);
                    break;

                case 'Null':
                    $column = $this->wrapColumn($cond['column']);
                    $isNull = $cond['not'] ? 'IS NOT NULL' : 'IS NULL';
                    $parts[] = ltrim("{$connector} {$column} {$isNull}");
                    break;

                case 'Nested':
                    $nested = new static($this->connection, '');
                    $cond['callback']($nested);
                    [$nestedSql, $nestedBindings] = $nested->compileWheres();
                    if ($nestedSql) {
                        $parts[] = ltrim("{$connector} ({$nestedSql})");
                        $bindings = array_merge($bindings, $nestedBindings);
                    }
                    break;
            }
        }

        return [implode(' ', $parts), $bindings];
    }
}