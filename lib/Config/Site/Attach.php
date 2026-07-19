<?php

namespace PHPEMS\Lib\Config\Site;

use PHPEMS\Lib\Config\Config;

class Attach extends Config
{
    public array $config = [
        'default' => [
            'privateSavePath' => PEPATH . '/storage/attach/private',
            'publicSavePath' => PEPATH . '/storage/attach/public',
            'convertSavePath' => PEPATH . '/storage/attach/convert',
        ]
    ];
}