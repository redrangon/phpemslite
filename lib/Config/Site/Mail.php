<?php

namespace PHPEMS\Lib\Config\Site;

use PHPEMS\Lib\Config\Config;

class Mail extends Config
{
    public array $config = [
        'default' => [
            'name' => 'QQ',
            'host' => 'smtp.qq.com',
            'username' => '278768688@qq.com',
            'password' => 'xxxxxxxxxxxx',
            'port' => 587,
            'encryption' => 'tls',
            'charset' => 'UTF-8',
        ]
    ];
}