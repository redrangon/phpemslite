<?php

namespace PHPEMS\App\Course\Controller\App;

use PHPEMS\App\Course\Service\CourseService;
use PHPEMS\App\Course\Service\Model\CourseLog;
use PHPEMS\App\Course\Service\Model\CourseSession;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Course extends Controller implements ControllerInterface
{

    static protected array $publicFlows = ['Auth','Course','Json'];
    protected CourseSession $session;
    protected CourseSubject $subject;

    public function __construct()
    {
        parent::__construct();
        $this->session = $this->request->getStore('session');
        $this->subject = $this->request->getStore('subject');

    }

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'subject' => 'Subject',
            'finish' => 'Finish',
            'progress' => 'Progress',
            'verify' => 'FaceVerify'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??static::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    public function FaceVerify(): array | Error
    {
        $user = $this->request->getUser();
        $courseId = $this->request->courseid??null;
        $course = \PHPEMS\App\Course\Service\Model\Course::find($courseId);
        if(!$course->courseid || $course->coursecsid != $this->subject->csid)return error(['error' => '课程不存在']);
        $face = $this->request->face??null;
        if(!$face)return error(['error' => '人脸不存在']);
        $log = CourseLog::findByUserCourse($user->userpassport, $courseId);
        $log->logfaces = $log->logfaces?json_decode($log->logfaces, true):[];
        $log->logfaces = json_encode($log->logfaces, JSON_UNESCAPED_UNICODE);
        $log->save();
        $this->session->csfadtime = TIME;
        $this->session->save();
        return ['faceVerify' => 1];
    }

    public function Finish(): array | Error
    {
        $user = $this->request->getUser();
        $courseId = $this->request->courseid??null;
        $course = \PHPEMS\App\Course\Service\Model\Course::find($courseId);
        if(!$course->courseid || $course->coursecsid != $this->subject->csid)return error(['error' => '课程不存在']);
        $log = CourseLog::findByUserCourse($user->userpassport, $courseId);
        if(!$log->logid)return error(['error' => '课程记录不存在']);
        if(!$log->logstatus)
        {
            $log->logstatus = 1;
            $log->logendtime = TIME;
            $log->logprogress = 0;
        }
        else $log->logprogress = 0;
        if(!$log->save())return error(['error' => '保存失败']);
        CourseService::getCourseProgress($user->userpassport,$course->coursecsid);
        return ['msg' => '保存成功'];
    }

    public function Progress(): array | Error
    {
        $user = $this->request->getUser();
        $courseId = $this->request->courseid??null;
        $course = \PHPEMS\App\Course\Service\Model\Course::find($courseId);
        if(!$course->courseid || $course->coursecsid != $this->subject->csid)return error(['error' => '课程不存在']);
        $log = CourseLog::findByUserCourse($user->userpassport, $courseId);
        $time = $this->request->time??0;
        if(!$log->logid)$log = CourseLog::fillWithInit([
            'logpassport' => $user->userpassport,
            'logcourseid' => $courseId,
            'logstatus' => 0,
            'logtime' => TIME,
            'logprogress' => $time,
            'logendtime' => 0
        ]);
        else $log->logprogress = max($time, $log->logprogress);
        $log->save();
        if($this->subject->csfacetime && (TIME - $this->session->csfadtime > $this->subject->csfacetime * 60))
        {
            return ['faceVerify' => 1];
        }
        return ['msg' => '保存成功'];
    }

    public function Data(): array | Error
    {
        $query = \PHPEMS\App\Course\Service\Model\Course::getQuery()
            ->select(['courseid', 'coursetitle', 'coursemodule', 'coursecsid','coursedirid'])
            ->orderBy('coursesequence', 'desc')
            ->orderBy('courseid', 'DESC');
        $courses = $query->where('coursecsid', $this->subject->csid)->get();
        $ids = [];
        foreach ($courses as $course)
        {
            $ids[] = $course['courseid'];
        }
        $user = $this->request->getUser();
        $logs = CourseLog::findByUserCourses($user->userpassport,$ids);
        $swap = [];
        foreach ($logs as $log)
        {
            $swap[$log['logcourseid']] = $log;
        }
        array_walk($courses, function (&$course) use ($swap) {
            $course['logstatus'] = $swap[$course['courseid']]['logstatus']??0;
            $course['logprogress'] = $swap[$course['courseid']]['logprogress']??0;
        });
        return $courses;
    }

    public function Subject(): array | Error
    {
        return $this->subject->getRaw();
    }

    public function index():array | Error
    {
        $courseId = $this->request->courseid??null;
        $course = \PHPEMS\App\Course\Service\Model\Course::find($courseId);
        if(!$course->courseid || $course->coursecsid != $this->subject->csid)return error(['error' => '课程不存在']);
        $user = $this->request->getUser();
        $log = CourseLog::findByUserCourse($user->userpassport, $courseId);
        if(!$log->logid)
        {
            $log = CourseLog::fillWithInit([
                'logpassport' => $user->userpassport,
                'logcourseid' => $courseId,
                'logstatus' => $course->coursemodule == 'video'?0:1,
                'logtime' => TIME,
                'logprogress' => 0,
                'logendtime' => 0
            ]);
            $log->save();
            $course->logprogress = 0;
        }
        else $course->logprogress = $log->logprogress??0;
        return $course->getRaw();
    }
}