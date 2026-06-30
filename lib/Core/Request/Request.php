<?php

namespace PHPEMS\Lib\Core\Request;

use PHPEMS\Lib\Utils\FileService\FileUploader;

class Request extends BaseRequest implements RequestInterface
{
    private string $type = 'request';
    public function __construct()
    {
        $route = $_GET['route']??null;
        if($route === null)
        {
            $keys = array_keys($_GET);
            $route = $keys[0]?? null;
        }
        if($route === null)
        {
            $route = [];
        }
        else
        {
            $route = explode('-', $route);
            $route = array_map(function($item){
                if(preg_match('/^\w+$/', $item))return strtolower($item);
                return null;
            }, $route);
        }
        $this->request['route'] = r($route);
        $this->request['data'] = $_REQUEST;
        $this->request['files'] = $this->parseFiles($_FILES);
    }
}