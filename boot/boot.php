<?php

use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Core\Request\RequestProvider;
use PHPEMS\Lib\Core\Router;
use PHPEMS\Lib\DataBase\DBProvider;
use PHPEMS\Lib\DI\DI;
use PHPEMS\Lib\Tpl\TplProvider;
use PHPEMS\Lib\Utils\Style;

define("TIME",time());
define("SSL","OFF");
require_once(PEPATH."/vendor/autoload.php");
require_once (PEPATH."/boot/helpers.php");
DI::bind(RequestInterface::class,function(){
    return DI(RequestProvider::class)->getInstance();
});
DI::bind('mysql.default',function(){
    return DBProvider::Create();
});
DI::bind('mysql.phpems',function(){
    return DBProvider::Create('phpems');
});
DI::bind('redis.default',function(){
    return DBProvider::CreateRedis();
});
DI::bind('tpl.default', function(){
    return TplProvider::Create();
});
DI::bind('style.default', function(){
    return new Style();
});
Router::regFlow(['Init']);
Router::dispatch();
