<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamMember;
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
            'refresh' => 'RefreshNumber'
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

    public function RefreshNumber():array | Error
    {
        $basicId = $this->request->basicId??null;
        if($basicId)
        {
            $basic = Basic::find($basicId);
            if(!$basic->basicId) return error(['error' => '考场不存在']);
            $query = ExamMember::getQuery()->where('embasicId', $basicId);
            $number = $query->count();
            if($number != $basic->basicNumber)
            {
                $basic->basicNumber = $number;
                $basic->save();
            }
        }
        return ['msg' => '刷新成功'];
    }

    public function Data():array | Error
    {
        $basicId = $this->request->basicId??null;
        if($basicId)
        {
            $page = $this->request->page??1;
            if($page < 1)$page = 1;
            $limit = $this->request->limit??20;
            $query = ExamMember::getQuery()->where('embasicId', $basicId);
            $search = $this->request->search??[];
            if($search['username']??false)
            {
                $user = User::findByUserName($search['username']);
                $query->where('empassport', $user->userpassport);
            }
            $data = $query->paginate($page, $limit);
            $passports = [];
            foreach($data['data'] as $item)
            {
                $passports[] = $item['empassport'];
            }
            $members = \PHPEMS\App\Member\Service\Model\Member::getQuery()->select(['mpassport','mname'])
                ->whereIn('mpassport', $passports)
                ->get();
            $members = array_column($members,null,'mpassport');
            array_walk($data['data'],function(&$item) use($members){
                $item['emstarttime'] = date('Y-m-d',$item['emstarttime']);
                $item['emendtime'] = date('Y-m-d',$item['emendtime']);
                $item['mname'] = $members[$item['empassport']]['mname']??'';
            });
            return $data;
        }
        return error(['error' => '参数错误']);
    }

    public function Delete():array | Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        ExamMember::getQuery()->whereIn('emid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array | Error
    {
        $cmId = $this->request->emId??null;
        if(!$cmId) return error(['error' => '参数错误']);
        $model = ExamMember::find($cmId);
        if(!$model->emId) return error(['error' => '记录不存在']);
        $cmendtime = $this->request->emendtime??null;
        if(!$cmendtime) return error(['error' => '请输入到期时间']);
        $cmendtime = strtotime($cmendtime);
        if($cmendtime < TIME) return error(['error' => '请输入正确的到期时间']);
        $model->emendtime = $cmendtime;
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function AddByPassport():array | Error
    {
        $mids = $this->request->mids??[];
        if(empty($mids)) return error(['error' => '请选择用户']);
        $basicId = $this->request->basicId??null;
        if(!$basicId) return error(['error' => '请选择课程']);
        $endTime = $this->request->endTime??'';
        $endTime = strtotime($endTime);
        if($endTime < TIME)return error(['error' => '请输入正确的到期时间']);
        foreach($mids as $passport)
        {
            $model = ExamMember::findByPassportAndSubjectId($passport,$basicId);
            if(!$model->emId)
            {
                $model = ExamMember::fillWithInit([
                    'embasicid' => $basicId,
                    'emendtime' => $endTime,
                    'emstarttime' => TIME,
                    'empassport' => $passport,
                ]);
                $result = $model->toValidate();
                if($result !== true)return $result;
                if(!$model->save()) return error(['error' => '通行证ID：'.$passport.'用户添加失败']);
            }
            else
            {
                if($model->emendtime != $endTime)
                {
                    $model->emendtime = $endTime;
                    if(!$model->save()) return error(['error' => '通行证ID：'.$passport.'用户添加失败']);
                }
            }
        }
        return ['msg' => '添加成功'];
    }

    public function Add():array | Error
    {
        $data = [
            'embasicId' => $this->request->embasicId??null,
            'emendtime' => strtotime($this->request->emendtime)??null,
            'emstarttime' => TIME,
            'empassport' => $this->request->empassport??null,
        ];
        if($data['emendtime'] < TIME) return error(['error' => '请输入正确的到期时间']);
        $model = ExamMember::findByPassportAndSubjectId($data['empassport'],$data['embasicId']);
        if(!$model->emId)
        {
            $model = ExamMember::fillWithInit($data);
            $result = $model->toValidate();
            if($result !== true)return $result;
            if(!$model->save()) return error(['error' => '添加失败']);
        }
        else
        {
            if($data['emendtime'] < $model->emendtime)return error(['error' => '请输入正确的到期时间']);
            $model->emendtime = $data['emendtime'];
            if(!$model->save()) return error(['error' => '添加失败']);
        }
        return ['msg' => '添加成功'];
    }
    public function Index():array | Error
    {
        return [];
    }
}