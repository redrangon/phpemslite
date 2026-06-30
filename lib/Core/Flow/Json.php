<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\Lib\Http\Cookie;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Auth\Auth;
use PHPEMS\Lib\Utils\Crypto\WasmCrypto;

class Json {
    public function handle(callable $next)
    {
        $result = $next();
        if($result instanceof Error)
        {
            $result = $result();
        }
        else
        {
            $msg = $result['msg']??false;
            $result = [
                'code' => 200,
                'data' => $result
            ];
            if($msg)
            {
                $result['msg'] = $msg;
                unset($result['data']['msg']);
            }
        }
        return $result;
    }
}