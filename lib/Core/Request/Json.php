<?php

namespace PHPEMS\Lib\Core\Request;

use Exception;
use PHPEMS\Lib\Utils\Crypto\WasmCrypto;

class Json extends BaseRequest implements RequestInterface
{
    private string $type = 'json';
    public function __construct()
    {
        $jsonString = file_get_contents('php://input');
        $jsonData = json_decode($jsonString, true);
        $route = $_SERVER['PATH_INFO'] ?? $_SERVER['ORIG_PATH_INFO'] ?? null;
        if($route !== null)
        {
            $route = explode('/', trim($route, '/'));
            $route = array_map(function($item){
                if(preg_match('/^\w+$/', $item))return strtolower($item);
                return null;
            }, $route);
        }
        $this->request['route'] = r($route);
        $this->request['data'] = $jsonData??$_POST;
        $this->request['files'] = $this->parseFiles($_FILES);
    }
}