<?php

namespace PHPEMS\Lib\Config\Site;
use PHPEMS\Lib\Config\Config;

class AI extends Config
{
    public array $config = [
        'default' => [
            'apiKey' => 'sk-12345678901234567890',
            'baseUrl' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
            'options' => [
                'model' => 'qwen3.6-plus',
                'temperature' => 0.0,
                'max_tokens' => 8192,
            ]
        ]
    ];
}