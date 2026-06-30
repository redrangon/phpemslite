<?php

namespace PHPEMS\App\Course\Controller\App;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\Course\Service\Model\CourseMember;
use PHPEMS\App\Course\Service\Model\CourseSession;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Index extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth','Json'];
    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'session' => 'setSession'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??self::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    public function setSession(): array | Error
    {
        $csId = $this->request->csId??null;
        if(!$csId)return error(['error' => '参数错误']);
        $course = CourseSubject::find($csId);
        if(!$course->csid)return error(['error' => '课程不存在']);
        $user = $this->request->getUser();
        $courseMember = CourseMember::findByPassportAndSubjectIdWithTime($user->userpassport,$csId);
        if(!$courseMember->cmid)return error(['error' => '您没有开通本课程']);
        $session = CourseSession::findByPassport($user->userpassport);
        if(!$session->csnid)
        {
            $session = CourseSession::fillWithInit([
                'csnpassport' => $user->userpassport,
                'csncsid' => $csId,
            ]);
            $session->save();
        }
        else
        {
            if($session->csncsid != $csId)
            {
                $session->csncsid = $csId;
                $session->save();
            }
        }
        return ['msg' => '设置成功'];
    }

    public function Index()
    {
        return [];
    }
}