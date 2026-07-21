<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\App\User\Service\Model\User;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Rules\Error;

class Auth {

    public function __construct(
        private readonly RequestInterface $request
    ){}
    public function handle(callable $next)
    {
        $result = $this->check();
        if($result instanceof Error)
        {
            return $result;
        }
        else
        {
            $userid = \PHPEMS\Lib\Auth\Auth::id();
            if($userid)
            {
                $user = User::find($userid);
                $this->request->setUser($user);
            }
            return $next();
        }
    }

    public function admin(callable $next)
    {
        $result = $this->check();
        if($result instanceof Error)
        {
            return $result;
        }
        else
        {
            $userid = \PHPEMS\Lib\Auth\Auth::id();
            if($userid)
            {
                $user = User::find($userid);
                if($user->isAdmin())
                {
                    $this->request->setUser($user);
                }
                else
                {
                    return error(['error' => '请以管理员身份登录','code' => 301]);
                }
            }
            return $next();
        }
    }

    protected function check()
    {
        // 2. 检查登录状态
        if (!\PHPEMS\Lib\Auth\Auth::check()) {
            return error(['error' => '请您先登录','code' => 301]);
        }

        // 3. 验证 AuthToken（防止 Session ID 单独被盗用）
        $token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
        //$token = $_COOKIE['token'];
        if (!$token || !\PHPEMS\Lib\Auth\Auth::validateRequestToken($token)) {
            \PHPEMS\Lib\Auth\Auth::logout(); // 失效会话
            return error(['error' => '请您再次登录','code' => 301]);
        }
        return true;
    }
}