<?php

namespace PHPEMS\Lib\DI;

class DI
{
    protected $instances = [];
    protected $bindings = [];
    protected static $instance = null;
    protected $args = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function forget($id)
    {
        if(self::getInstance()->instances[$id]??false)unset(self::$instance->instances[$id]);
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
        return isset(self::$instance->instances[$id]);
    }

    /**
     * @throws \Exception
     */
    public function get($id, $args = null)
    {
        if($id === null)return null;
        if(isset($this->instances[$id]))return $this->instances[$id];
        if($args)
        {
            if(is_string($args))$this->args = [$args];
            elseif(is_array($args))$this->args = $args;
            else throw new \Exception('DI参数错误');
        }
        $concrete = $this->bindings[$id]??$id;
        if ($concrete instanceof \Closure) {
            $instance = $concrete($this);
        } elseif (is_string($concrete) && class_exists($concrete)) {
            $instance = $this->build($concrete);
        } else {
            $instance = $concrete;
        }
        if(is_object($instance) && isset($this->bindings[$id]))
        {
            $this->instances[$id] = $instance;
        }
        return $instance;
    }

    private function build($class)
    {
        $reflector = new \ReflectionClass($class);
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $class;
        }
        $parameters = $constructor->getParameters();
        if($this->args)
        {
            foreach($this->args as $key => $value)
            {
                if(isset($parameters[$key]))
                {
                    $dependencies[] = $value;
                }
            }
            $this->args = null;
        }
        else $dependencies = $this->getDependencies($parameters);
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @throws \Exception
     */
    private function getDependencies($parameters): array
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
                continue;
            }
            $type = $parameter->getType();

            if ($type && $type->allowsNull()) {
                if (!$type->isBuiltin()) {
                    $typeName = $type->getName();
                    try {
                        $dependencies[] = $this->get($typeName);
                    } catch (\Exception $e) {
                        // 如果无法解析，使用 null
                        $dependencies[] = null;
                    }
                } else {
                    $dependencies[] = null;
                }
                continue;
            }
            if ($type && !$type->isBuiltin()) {
                $typeName = $type->getName();
                $dependencies[] = $this->get($typeName);
                continue;
            }
            throw new \Exception('Cannot resolve dependency for ' . $parameter);
        }
        return $dependencies;
    }
}