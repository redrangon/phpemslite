<?php

namespace PHPEMS\Lib\Auth;

use PHPEMS\Lib\Auth\Drivers\MySQLDriver;
use PHPEMS\Lib\Auth\Drivers\RedisDriver;

class DataProvider
{
    public static function Create($type = 'mysql',$dbName = 'mysql.default')
    {
        return match ($type) {
            'mysql' => MySqlDriver::getInstance($dbName),
            default => RedisDriver::getInstance($dbName),
        };
    }
}