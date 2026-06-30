<?php

namespace PHPEMS\Lib\Rules;

use PHPEMS\Lib\Core\Request\RequestProvider;

abstract class Controller
{
    static protected array $publicFlows = [];
    protected RequestProvider $request;

    public function __construct()
    {
        $this->request = DI('request');
    }

    static public function getRoutes(): array
    {
        return ['index' => 'Index'];
    }

    static public function withFlows(): array
    {
        return self::$publicFlows;
    }

    static public function withOutFlows(): array
    {
        return [];
    }
}