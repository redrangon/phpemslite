<?php

namespace PHPEMS\Lib\DataBase;

use Closure;

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

    public function __construct(DB $db,string $table)
    {
        $this->connection = $db;
        if ($table) {
            $this->from($table);
        }
    }

    public function buildTableName(string $table, bool $useAlias = true): string
    {
        if ($prefix = $this->connection->getConfig('tablePrefix')) {
            return $useAlias? $prefix . $table .' AS ' . $table:$prefix . $table ;
        }
        return $table;
    }

    public function from(string $table): self
    {
        $this->table = $this->buildTableName($table,false);
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

    /**
     * 标准化 JOIN 条件
     * 
     * @param mixed $first 第一个字段或条件数组
     * @param string|null $operator 运算符
     * @param string|null $second 第二个字段
     * @return array 标准化的条件数组
     */
    protected function normalizeJoinConditions($first, ?string $operator, ?string $second): array
    {
        // 如果是数组，说明是多条件
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
        
        // 单条件（向后兼容）
        return [
            ['first' => $first, 'operator' => $operator, 'second' => $second]
        ];
    }

    // ===== WHERE (支持嵌套/OR) =====
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

    public function whereNull(string $column): self
    {
        $this->wheres[] = ['type' => 'Null', 'column' => $column, 'not' => false, 'boolean' => 'and'];
        return $this;
    }

    // ===== GROUP / HAVING / ORDER / LIMIT =====
    public function groupBy(string $column): self
    {
        $this->groups[] = $column;
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

    public function orHavingRaw(string $sql, array $bindings = [])
    {
        $this->havings[] = [
            'type' => 'Raw',
            'sql' => $sql,
            'bindings' => $bindings,
            'boolean' => 'or'
        ];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc')
    {
        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
        $this->orders[] = "{$column} {$dir}";
        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset)
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

    public function paginate( int $page = 1,int $limit = 20):array
    {
        if($this->groups || $this->havings)$total = $this->countGroup();
        else $total = $this->count();
        $offset = ($page - 1) * $limit;
        $data = $this->limit($limit)->offset($offset)->get();
        return ['page' => $page, 'total' => $total, 'limit' => $limit, 'data' => $data];
    }

    public function first(): ?array
    {
        $clone = clone $this;
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

        // 清除所有可能干扰 COUNT(*) 的子句
        $this->groups = $this->havings = $this->orders = [];
        $this->limit = $this->offset = null;
        $this->columns = ["COUNT({$column}) as aggregate"];

        try {
            $row = $this->first();
            return (int) ($row['aggregate'] ?? 0);
        } finally {
            // 恢复原始状态
            $this->groups = $origGroups;
            $this->havings = $origHavings;
            $this->orders = $origOrders;
            $this->limit = $origLimit;
            $this->offset = $origOffset;
            $this->columns = $origColumns;
        }
    }

    public function countGroup(): int
    {
        $original = $this->columns;
        $this->columns = ['1'];
        [$innersql, $bindings] = $this->toSubSql();
        $sql = "SELECT COUNT(*) AS aggregate FROM ({$innersql}) AS grouped";
        $result = $this->connection->fetch($sql, $bindings);
        $this->columns = $original;
        return (int) ($result['aggregate'] ?? 0);
    }

    // ===== 写操作 =====
    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
        $values = array_values($data);
        $values = array_map(function($item){
            if(is_array($item))$item = json_encode($item, JSON_UNESCAPED_UNICODE);
            return $item;
        }, $values);
        return $this->connection->insertGetId($sql, $values);
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
            throw new \LogicException('Delete without where clause is not allowed.');
        }
        $sql = "DELETE FROM {$this->table}";
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql .= " WHERE {$whereSql}";
        return $this->connection->execute($sql, $whereBindings);
    }

    // ===== SQL 生成 =====
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
        if ($whereSql) $sql .= " WHERE {$whereSql}";

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
        [$sql,  $bindings] =  $this->toSubSql();

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . (int)$this->limit;
            if ($this->offset !== null) {
                $sql .= ' OFFSET ' . (int)$this->offset;
            }
        }
        return [$sql, $bindings];
    }

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
        if (empty($conditions)) return ['', []];

        $parts = [];
        $bindings = [];

        foreach ($conditions as $i => $cond) {
            $connector = $i === 0 ? '' : strtoupper($cond['boolean']);

            switch ($cond['type']) {
                case 'Basic':
                    $parts[] = ltrim("{$connector} {$cond['column']} {$cond['operator']} ?");
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
                    $placeholders = str_repeat('?,', count($cond['values']) - 1) . '?';
                    $parts[] = ltrim("{$connector} {$not}{$cond['column']} IN ({$placeholders})");
                    $bindings = array_merge($bindings, $cond['values']);
                    break;
                case 'Null':
                    $isNull = $cond['not'] ? 'IS NOT NULL' : 'IS NULL';
                    $parts[] = ltrim("{$connector} {$cond['column']} {$isNull}");
                    break;
                case 'Nested':
                    $nested = new static();
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