<?php

use PHPEMS\Lib\DI\DI;
use PHPEMS\Lib\Rules\Error;
if (!function_exists('r')) {
    function r($route)
    {
        $route[0] = $route[0]??'core';
        if($route[0] === 'plugins')
        {
            $route[2] = $route[2]??'app';
            $route[3] = $route[3]??'index';
        }
        else
        {
            $route[1] = $route[1]??'app';
            $route[2] = $route[2]??'index';
        }
        return $route;
    }
}

if(!function_exists('DI'))
{
    function DI($class,$args = null)
    {
        return DI::getInstance()->get($class,$args);
    }
}

if (!function_exists('e')) {
    function e($str): string
    {
        return htmlspecialchars($str ?: '', ENT_QUOTES, 'UTF-8');
    }
}

if(!function_exists('error'))
{
    function error($msg): Error
    {
        $msg['code'] = $msg['code']??300;
        $msg['error'] = $msg['error']??'Error';
        return Error::create($msg);
    }
}

if (!function_exists('vite')) {
    function vite(string $baseDir = ''): ?string {
        // 如果未传入 baseDir，默认认为当前文件在项目根目录或子目录
        if ($baseDir === '') {
            $baseDir = PEPATH; // 假设此函数在 helpers/ 等子目录中
        }
        $assetsDir = $baseDir . '/public/assets';
        // 检查目录是否存在
        if (!is_dir($assetsDir)) {
            return null;
        }
        // 扫描目录中的所有 .js 文件
        $files = glob($assetsDir . '/main*.js');
        if (empty($files)) {
            return null;
        }
        // 取第一个匹配的文件（通常只有一个）
        $filePath = $files[0];
        // 返回文件名（不含路径）
        return basename($filePath);
    }
}
