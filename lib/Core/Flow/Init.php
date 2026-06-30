<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\Lib\Http\Cookie;

class Init
{
    public function handle(callable $next)
    {
        $config = DI(\PHPEMS\Lib\Config\Site\Cookie::class);
        date_default_timezone_set('Asia/Shanghai');
        header('Content-Type: text/html; charset=utf-8');
        header('X-Frame-Options:SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: '.$config->domain?:'http://127.0.0.1');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, App-Agent');
        Cookie::setDefaultOptions($config->getRaw());
        return $next();
    }
}