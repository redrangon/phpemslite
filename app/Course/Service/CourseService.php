<?php

namespace PHPEMS\App\Course\Service;

use PHPEMS\App\Course\Service\Model\Course;
use PHPEMS\App\Course\Service\Model\CourseLog;
use PHPEMS\App\Course\Service\Model\CourseMember;
use PHPEMS\App\Course\Service\Model\CourseProgress;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\DataBase\QueryBuilder;

class CourseService
{
    public static function getCourseLogQueryWithCourse():QueryBuilder
    {
        $logTable = CourseLog::getTableName();
        $courseKey = Course::getPrimaryKey();
        return Course::getQuery()->leftJoin($logTable, 'logcourseid', '=', $courseKey);
    }

    public static function getCourseSubjectQueryWithMember():QueryBuilder
    {
        $memberTable = CourseMember::getTableName();
        $subjectKey = CourseSubject::getPrimaryKey();
        return CourseSubject::getQuery()->join($memberTable, 'cmcsid', '=', $subjectKey);
    }

    public static function getCourseProgress(string $passport, int $csId):void
    {
        $progress = CourseProgress::findByPassportWithCourseId($passport,$csId);
        if(!$progress->pcpStatus)
        {
            if(!$progress->pcpId)$progress = CourseProgress::fillWithInit([
                'cpcsid' => $csId,
                'cppassport' => $passport,
                'cpstatus' => 0
            ]);
            $courseNumber = Course::getQuery()->where('coursecsid',$csId)
                ->where('coursemodule','!=','dir')
                ->count();
            $finishNumber = self::getCourseLogQueryWithCourse()->where('coursecsid',$csId)
                ->where('logpassport',$passport)
                ->where('logstatus',1)
                ->count();
            if($courseNumber)
            {
                if($finishNumber >= $courseNumber)
                {
                    $progress->cpStatus = 1;
                    $progress->save();
                }
                else
                {
                    if(!$progress->cpId)$progress->save();
                }
            }
        }
    }
}