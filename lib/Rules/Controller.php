<?php

namespace PHPEMS\Lib\Rules;

use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Core\Request\RequestProvider;

abstract class Controller
{
    static protected array $publicFlows = [];

    public function __construct(
        protected RequestInterface $request
    )
    {}

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