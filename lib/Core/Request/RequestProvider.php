<?php

namespace PHPEMS\Lib\Core\Request;


use PHPEMS\Lib\Config\Site\Site;

/**
 * @mixin PHPEMS\Lib\Core\Request\Json
 */

class RequestProvider
{

    private static ?RequestProvider $instance = null;
    private mixed $request;

    public static function Create()
    {
        if(self::$instance == null)
        {
            $config = new Site();
            if($config->responseType == 'json')self::$instance = new static(DI(Json::class));
            else self::$instance =  new static(DI(Request::class));
        }
        return self::$instance;
    }

    private function __construct(mixed $request)
    {
        $this->request = $request;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->request, $name))
        {
            return call_user_func_array([$this->request, $name], $arguments);
        }
        return null;
    }

    public function __get($key): mixed
    {
        return $this->request->get($key);
    }

    public function __set($key,$value)
    {
        $this->request->$key = $value;
    }
}