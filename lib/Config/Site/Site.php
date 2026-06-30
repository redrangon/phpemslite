<?php

namespace PHPEMS\Lib\Config\Site;

use PHPEMS\Lib\Config\Config;

class Site extends Config
{
    public array $config = [
        'default' => [
            'baseUri' => 'https://phpems.net',
            'responseType' => 'json',
            'randCodeTTFPath' => PEPATH . '/resources/styles/phpems/fonts/DejaVuSans.ttf',
            'fontRootPath' => PEPATH . '/resources/styles/phpems/fonts',
            'certImageSavePath' => PEPATH . '/storage/cert/',
            'faceImageSavePath' => PEPATH . '/storage/face/',
            'errorLogPath' => PEPATH . '/storage/logs/error.log',
            'AILogPath' => PEPATH . '/storage/logs/ai.log',
            'tmpDir' => PEPATH . '/storage/tmp/',
            'wasmKeyFile' => PEPATH . '/secret/wasm/wasm_keys.php'
        ]
    ];
}