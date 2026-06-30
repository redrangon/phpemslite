<?php

namespace PHPEMS\Lib\Core\Flow;

use PHPEMS\App\Course\Service\Model\CourseSession;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\Rules\Error;

class Course
{
    private function init():CourseSession | Error
    {
        $user = DI('request')->getUser();
        $session = CourseSession::findByPassport($user->userpassport);
        if(!$session->csnid) {
            return error(['error' => '会话不存在']);
        }
        $subject = CourseSubject::find($session->csncsid);
        if(!$subject->csid) {
            return error(['error' => '课程不存在']);
        }
        DI('request')->setStore('session', $session);
        DI('request')->setStore('subject', $subject);
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