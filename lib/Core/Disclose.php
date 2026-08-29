<?php

namespace PHPEMS\Lib\Core;

use PDOException;
use PHPEMS\Lib\Auth\Auth;
use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Http\Cookie;

use Throwable;

class Disclose
{
    static public function export(mixed $result): void
    {
        $payload = match (true) {
            $result instanceof PDOException => [
                'code' => 300,
                'error' => '数据库执行错误',
            ],
            $result instanceof Throwable => [
                'code' => $result->getCode(),
                'error' => $result->getMessage(),
            ],
            default => $result
        };
        if(DI(Site::class)->responseType == 'json')self::discloseJson($payload);
        else self::discloseTpl($payload);
    }

    static private function discloseJson(array $result): void
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

    static private function discloseTpl(array $result): void
    {
        DI('tpl.default')->render($result['tpl'], $result['data']);
    }
}