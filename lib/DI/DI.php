<?php

namespace PHPEMS\Lib\DI;

use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class DI
{
    protected $instances = [];
    protected $bindings = [];
    protected static $instance = null;

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function forget($id)
    {
        if(self::getInstance()->instances[$id]??false)
        {
            unset(self::getInstance()->instances[$id]);
        }
    }

    public static function flush()
    {
        self::getInstance()->instances = [];
    }

    public static function bind($id, $concrete = null)
    {
        self::getInstance()->bindings[$id] = $concrete;
    }

    public static function has($id)
    {
        return isset(self::getInstance()->instances[$id]);
    }

    public static function get($id, $args = null)
    {
        return self::getInstance()->factory($id, $args);
    }

    /**
     * @throws Exception
     */
    public function factory($id, $args = null)
    {
        if($id === null)return null;
        if($args === null && isset($this->instances[$id]))return $this->instances[$id];
        if($args)
        {
            if(is_string($args))$args = [$args];
            if(!is_array($args))throw new Exception('DI参数错误');
        }
        $concrete = $this->bindings[$id]??$id;
        if ($concrete instanceof \Closure) {
            $instance = $concrete($args);
        } elseif (is_string($concrete) && class_exists($concrete)) {
            $instance = $this->build($concrete,$args);
        } else {
            $instance = $concrete;
        }
        if(is_object($instance) && isset($this->bindings[$id]))
        {
            $this->instances[$id] = $instance;
        }
        return $instance;
    }

    private function build($class,$args = [])
    {
        $reflector = new ReflectionClass($class);
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $class;
        }
        $parameters = $constructor->getParameters();
        $dependencies = [];
        if (!empty($args) && !array_is_list($args)) {
            foreach ($parameters as $parameter) {
                $paramName = $parameter->getName();
                if (array_key_exists($paramName, $args)) {
                    $dependencies[] = $args[$paramName];
                } else {
                    $dependencies[] = $this->resolve($parameter);
                }
            }
        }
        // 2. 处理数字索引数组（按位置注入）
        elseif (!empty($args)) {

            foreach ($parameters as $index => $parameter) {
                if (array_key_exists($index, $args)) {
                    $dependencies[] = $args[$index];
                } else {
                    $dependencies[] = $this->resolve($parameter);
                }
            }
        }
        // 3. 未传参，完全自动解析
        else {
            foreach ($parameters as $parameter) {
                $dependencies[] = $this->resolve($parameter);
            }
        }
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * 解析单个参数（支持联合类型与自动降级）
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    private function resolve(ReflectionParameter $parameter): mixed
    {
        // 1. 优先使用默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $type = $parameter->getType();

        // 2. 如果没有类型声明，直接抛出异常
        if (!$type) {
            throw new Exception("Cannot resolve untyped parameter: [{$parameter->getName()}]");
        }

        // 3. 处理联合类型 (PHP 8.0+)
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $subType) {
                // 跳过内置标量类型和 null
                if ($subType->isBuiltin() || $subType->getName() === 'null') {
                    continue;
                }
                // 尝试从容器中解析
                try {
                    return $this->factory($subType->getName());
                } catch (Exception $e) {
                    // 当前类型解析失败，继续尝试下一个
                    continue;
                }
            }
            // 所有类都解析失败，如果允许为 null，则安全返回 null
            if ($type->allowsNull()) {
                return null;
            }
            throw new Exception("Cannot resolve any class in union type for parameter: [{$parameter->getName()}]");
        }

        // 4. 处理单一类型 (ReflectionNamedType)
        if ($type instanceof ReflectionNamedType) {
            // 内置类型（如 int, string）容器无法解析
            if ($type->isBuiltin()) {
                // 如果允许为 null，则返回 null
                if ($type->allowsNull()) {
                    return null;
                }
                throw new Exception("Cannot resolve builtin type: [{$type->getName()}] for parameter: [{$parameter->getName()}]");
            }

            // 类或接口，直接从容器解析
            return $this->factory($type->getName());
        }

        throw new Exception("Unsupported reflection type for parameter: [{$parameter->getName()}]");
    }
}