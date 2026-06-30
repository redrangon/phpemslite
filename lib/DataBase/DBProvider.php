<?php

namespace PHPEMS\Lib\DataBase;

use Closure;
use PHPEMS\Lib\Config\DataBase\MySql;
use PHPEMS\Lib\Config\DataBase\Redis;

class DBProvider
{
    public static function Create($configName = 'default',$type = 'MySQL'): DB
    {
        $config = match ($type) {
            default => DI(MySql::class, $configName),
        };
        return DB::getInstance($config);
    }

    public static function CreateRedis($configName = 'default')
    {
        $configName = match ($configName) {
            default => 'default',
        };
        $config = DI(Redis::class,$configName);
        return RedisClient::getInstance($config->getRaw());
    }
}