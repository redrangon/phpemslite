<?php

use PHPEMS\Lib\DataBase\DBProvider;
use PHPEMS\Lib\DI\DI;
define("TIME",time());
require_once(PEPATH."/vendor/autoload.php");
require_once (PEPATH."/boot/helpers.php");
DI::bind('mysql.default',function(){
    return DBProvider::Create();
});
DI::bind('mysql.phpems',function(){
    return DBProvider::Create('phpems');
});
DI::bind('redis.default',function(){
    return DBProvider::CreateRedis();
});
