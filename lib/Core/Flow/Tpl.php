<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\Lib\Http\Cookie;

use PHPEMS\Lib\Tpl\TplProvider;

class Tpl {
    public function handle(callable $next)
    {
        $result = $next();
        TplProvider::Create()->render($result['tpl'], $result['data']??[]);
		exit();
    }
}