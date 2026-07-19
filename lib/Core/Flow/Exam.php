<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Rules\Error;

class Exam
{
    public function __construct(
        private readonly RequestInterface $request
    ){}
    private function init():ExamSession | Error
    {
        $user = $this->request->getUser();
        $session = ExamSession::findByPassport($user->userpassport);
        if(!$session->esid) {
            return error(['error' => '会话不存在']);
        }
        $basic = Basic::find($session->esbasicid);
        if(!$basic->basicid) {
            return error(['error' => '课程不存在']);
        }
        $basic->basicexam = json_decode($basic->basicexam, true);
        $basic->basicpoint = json_decode($basic->basicpoint, true);
        $intime = 1;
        if(($basic->basicexam['opentime']??false) && strtotime($basic->basicexam['opentime']) > TIME)
        {
            $intime = 0;
        }
        if(($basic->basicexam['closetime']??false) && strtotime($basic->basicexam['closetime']) < TIME)
        {
            $intime = 0;
        }
        $basic->intime = $intime;
        $this->request->setStore('session', $session);
        $this->request->setStore('basic', $basic);
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