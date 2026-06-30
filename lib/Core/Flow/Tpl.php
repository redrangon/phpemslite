<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\Lib\Http\Cookie;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Tpl\TplProvider;

class Tpl {
    public function handle(callable $next)
    {
        $result = $next();
        if($result instanceof Error){
            return $result;
        }
        else
        {
            TplProvider::Create()->render($result['tpl'], $result['data']);
            exit();
        }
    }
}