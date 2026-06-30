<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\Lib\Rules\Error;

class Exam
{
    private function init():ExamSession | Error
    {
        $user = DI('request')->getUser();
        $session = ExamSession::findByPassport($user->userpassport);
        if(!$session->esid) {
            return error(['error' => '会话不存在']);
        }
        $basic = Basic::find($session->esbasicid);
        if(!$basic->basicid) {
            return error(['error' => '课程不存在']);
        }
        DI('request')->setStore('session', $session);
        DI('request')->setStore('basic', $basic);
        return $session;
    }

    public function handle(callable $next)
    {
        $result = $this->init();
        if($result instanceof Error)
        {
            return $result;
        }
        else
        {
            return $next();
        }
    }
}