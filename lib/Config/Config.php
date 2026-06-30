<?php

namespace PHPEMS\Lib\Config;

abstract class Config
{
    protected array $config = array();
    protected string $name = 'default';
    protected static self|null $instance = null;

    public function __construct($name = 'default')
    {
        $this->name = $name;
    }

    public function __get($key)
    {
        return $this->config[$this->name][$key]??null;
    }

    public function getRaw():array
    {
        return $this->config[$this->name];
    }

    static public function getConfig($key)
    {
        if(self::$instance == null)
        {
            self::$instance = new static();
        }
        return self::$instance->$key;
    }
}