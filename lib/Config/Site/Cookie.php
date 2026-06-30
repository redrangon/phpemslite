<?php

namespace PHPEMS\Lib\Config\Site;

use PHPEMS\Lib\Config\Config;

class Cookie extends Config
{
    public array $config = [
        'default' => [
            'domain' => '',
            'expire' => 43200,
        ]
    ];
}