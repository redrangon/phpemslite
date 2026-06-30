<?php

namespace PHPEMS\Lib\Rules;

use PHPEMS\Lib\DataBase\DB;
use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Utils\Validator;

abstract class Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = '';
    protected static $primaryKeys = [];

    protected $properties = [];

    protected static $defaultValues = [];

    protected static $rules = [];

    protected static $ruleMessages = [];
    protected $exists = false;

    public static function fill(array $attributes): array
    {
        if(empty(static::$defaultValues))return $attributes;
        foreach(static::$defaultValues as $key => $value)
        {
            if (!array_key_exists($key, $attributes)) {
                $attributes[$key] = $value;
            }
        }
        return $attributes;
    }

    public static function fillWithInit(array $attributes): static
    {
        return new static(static::fill($attributes));
    }

    public static function validate(array $data): bool|Error
    {
        $validator = new Validator( $data, static::$rules, static::$ruleMessages);
        if($validator->fails())
        {
            return error(['error' => $validator->firstError()]);
        }
        else return true;
    }

    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
        if($properties[$this->getKeyName()]??false)$this->exists = true;
    }

    public function __get($key)
    {
        $key = strtolower($key);
        return $this->properties[$key]??null;
    }

    public function __set($key, $value)
    {
        $key = strtolower($key);
        $this->properties[$key] = $value;
    }

    public function getRaw()
    {
        return $this->properties;
    }

    public function getKeyName(): string
    {
        return static::$primaryKeys[0] ?? 'id';
    }

    public function toValidate(): bool|Error
    {
        return self::validate($this->properties);
    }

    // ===== 持久化 =====
    public function save(): bool
    {
        $query = static::getQuery();
        $pk = $this->getKeyName();
        if ($this->exists) {
            // 更新：使用主键
            $query->where($pk, $this->properties[$pk]);
            $result = $query->update($this->properties);
        } else {
            // 插入
            $id = $query->insert($this->properties);
            if ($id) {
                $this->properties[$pk] = $id;
            }
            $this->exists = true;
            $result = true;
        }
        return (bool)$result;
    }

    public function delete(): bool
    {
        if (!$this->exists) return false;
        $pk = $this->getKeyName();
        return (bool)static::getQuery()->where($pk, $this->properties[$pk])->delete();
    }

    public static function getPrimaryKey()
    {
        return static::$primaryKeys[0];
    }

    public static function getTableName(): string
    {
        return static::$dbTable;
    }

    public static function __callStatic($method, $params)
    {
        $query = static::getQuery();
        if(method_exists($query, $method))
        return $query->$method(...$params);
        else return null;
    }

    public static function find($id): static
    {
        $query = static::getQuery();
        $result = $query->where(static::getPrimaryKey(), $id)->first();
        return new static($result ?? []);
    }


    public static function getQuery(): QueryBuilder
    {
        return new QueryBuilder(DI(static::$dbName), static::$dbTable);
    }

    public static function getDB():DB
    {
        return DI(static::$dbName);
    }
}