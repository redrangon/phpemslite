<?php

namespace PHPEMS\Lib\Config\DataBase;

use PHPEMS\Lib\Config\Config;

class Redis extends Config
{
    public array $config = [
        'default' => [
            'name' => 'redisDefault',
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'database' => 0,
            'timeout' => 0.0,
            'retry_interval' => 0,
            'read_timeout' => 0.0
        ]
    ];
}