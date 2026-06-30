<?php

namespace PHPEMS\App\Course\Controller\Master;

use PHPEMS\App\Course\Service\CourseService;
use PHPEMS\App\Course\Service\Model\CourseMember;
use PHPEMS\App\Course\Service\Model\Course;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Member extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'add' => 'Add',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'data' => 'Data',
            'addbypassport' => 'AddByPassport',
            'refresh' => 'RefreshNumber',
            'stats' => 'Stats'
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

    public function Stats():array|Error
    {
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 200;
        $csId = $this->request->csId??null;
        $member = CourseMember::getQuery()
            ->select(['cmpassport','cmcsid','cmid'])
            ->where('cmcsid', $csId)
            ->paginate($page,$limit);
        $number = count($member['data']);
        if($number && $number <= $limit)
        {
            $courseNumber = Course::getQuery()->where('coursecsid',$csId)
                ->where('coursemodule','!=','dir')
                ->count();
            if($courseNumber)
            {
                array_walk($member['data'], function (&$item) use ($csId,$courseNumber){
                    $finishNumber = CourseService::getCourseLogQueryWithCourse()->where('coursecsid',$csId)
                        ->where('logpassport',$item['cmpassport'])
                        ->where('logstatus',1)
                        ->count();


                    if($finishNumber >= $courseNumber)
                    {
                        $progress = CourseMember::find($item['cmid']);
                        $progress->cmStatus = 1;
                        $progress->save();
                    }
                });
            }
            $member['status'] = true;
        }
        else
        {
            $member['status'] = false;
        }
        unset($member['data']);
        return $member;
    }

    public function RefreshNumber():array | Error
    {
        $csId = $this->request->csId??null;
        if($csId)
        {
            $subject = CourseSubject::find($csId);
            if(!$subject->csId) return error(['error' => '课程不存在']);
            $query = CourseMember::getQuery()->where('cmcsId', $csId);
            $number = $query->count();
            if($number != $subject->csNumber)
            {
                $subject->csNumber = $number;
                $subject->save();
            }
        }
        return ['msg' => '刷新成功'];
    }

    public function Data():array | Error
    {
        $csId = $this->request->csId??null;
        if($csId)
        {
            $page = $this->request->page??1;
            if($page < 1)$page = 1;
            $limit = $this->request->limit??20;
            $query = CourseMember::getQuery()->where('cmcsid', $csId);
            $search = $this->request->search??[];
            if($search['username']??false)
            {
                $user = User::findByUserName($search['username']);
                $query->where('cmpassport', $user->userpassport);
            }
            $data = $query->paginate($page, $limit);
            $passports = [];
            foreach($data['data'] as $item)
            {
                $passports[] = $item['cmpassport'];
            }
            $members = \PHPEMS\App\Member\Service\Model\Member::getQuery()->select(['mpassport','mname'])
                ->whereIn('mpassport', $passports)
                ->get();
            $members = array_column($members,null,'mpassport');
            array_walk($data['data'],function(&$item) use($members){
                $item['cmstarttime'] = date('Y-m-d',$item['cmstarttime']);
                $item['cmendtime'] = date('Y-m-d',$item['cmendtime']);
                $item['mname'] = $members[$item['cmpassport']]['mname']??'';
            });
            return $data;
        }
        return error(['error' => '参数错误']);
    }

    public function Delete():array | Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        CourseMember::getQuery()->whereIn('cmid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array | Error
    {
        $cmId = $this->request->cmId??null;
        if(!$cmId) return error(['error' => '参数错误']);
        $model = CourseMember::find($cmId);
        if(!$model->cmId) return error(['error' => '记录不存在']);
        $cmendtime = $this->request->cmendtime??null;
        if(!$cmendtime) return error(['error' => '请输入到期时间']);
        $cmendtime = strtotime($cmendtime);
        if($cmendtime < TIME) return error(['error' => '请输入正确的到期时间']);
        $model->cmendtime = $cmendtime;
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function AddByPassport():array | Error
    {
        $mids = $this->request->mids??[];
        if(empty($mids)) return error(['error' => '请选择用户']);
        $csId = $this->request->csId??null;
        if(!$csId) return error(['error' => '请选择课程']);
        $endTime = $this->request->endTime??'';
        $endTime = strtotime($endTime);
        if($endTime < TIME)return error(['error' => '请输入正确的到期时间']);
        foreach($mids as $passport)
        {
            $model = CourseMember::findByPassportAndSubjectId($passport,$csId);
            if(!$model->cmId)
            {
                $model = CourseMember::fillWithInit([
                    'cmcsid' => $csId,
                    'cmendtime' => $endTime,
                    'cmstarttime' => TIME,
                    'cmpassport' => $passport,
                ]);
                $result = $model->toValidate();
                if($result !== true)return $result;
                if(!$model->save()) return error(['error' => '通行证ID：'.$passport.'用户添加失败']);
            }
            else
            {
                if($model->cmendtime != $endTime)
                {
                    $model->cmendtime = $endTime;
                    if(!$model->save()) return error(['error' => '通行证ID：'.$passport.'用户添加失败']);
                }
            }
        }
        return ['msg' => '添加成功'];
    }

    public function Add():array | Error
    {
        $data = [
            'cmcsid' => $this->request->cmcsid??null,
            'cmendtime' => strtotime($this->request->cmendtime)??null,
            'cmstarttime' => TIME,
            'cmpassport' => $this->request->cmpassport??null,
        ];
        if($data['cmendtime'] < TIME) return error(['error' => '请输入正确的到期时间']);
        $model = CourseMember::findByPassportAndSubjectId($data['cmpassport'],$data['cmcsid']);
        if(!$model->cmId)
        {
            $model = CourseMember::fillWithInit($data);
            $result = $model->toValidate();
            if($result !== true)return $result;
            if(!$model->save()) return error(['error' => '添加失败']);
        }
        else
        {
            if($data['cmendtime'] < $model->cmendtime)return error(['error' => '请输入正确的到期时间']);
            $model->cmendtime = $data['cmendtime'];
            if(!$model->save()) return error(['error' => '添加失败']);
        }
        return ['msg' => '添加成功'];
    }
    public function Index():array | Error
    {
        return [];
    }
}