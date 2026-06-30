<?php

namespace PHPEMS\Lib\Core;

use PHPEMS\Lib\Auth\Auth;
use PHPEMS\Lib\Http\Cookie;
use PHPEMS\Lib\Rules\Error;

class Disclose
{
    static public function export(mixed $result)
    {
        if($result instanceof Error)
        {
            $result = $result();
            self::discloseJson($result);
        }
        else
        {
            if(DI('request')->getType() == 'json')self::discloseJson($result);
            else self::discloseTpl($result);
        }
    }

    static private function discloseJson(array $result)
    {
        $authToken = Auth::enforceRotation();
        if($authToken)
        {
            Cookie::queue(session_name(),session_id(),60);
            $result['token'] = $authToken;
        }
        header('Content-Type: application/json');
        Cookie::sendQueuedCookies();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    static private function discloseTpl(array $result)
    {
        DI('tpl.default')->render($result['tpl'], $result['data']);
    }
}