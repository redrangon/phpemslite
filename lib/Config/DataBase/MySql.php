<?php

namespace PHPEMS\Lib\Config\DataBase;

use PHPEMS\Lib\Config\Config;

class MySql extends Config
{
    public array $config = [
        'default' => [
            'type' => 'mysql',
            'name' => 'mysqlDefault',
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'root',
            'password' => 'Zdr5NSqnyjAPwNvL',
            'database' => 'phpemsvue',
            'charset' => 'utf8mb4',
            'tablePrefix' => 'x2_'
        ]
    ];
}