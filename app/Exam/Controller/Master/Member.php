<?php

namespace PHPEMS\App\Exam\Controller\Master;

use Exception;
use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamMember;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Utils\ExcelProvider;
use PHPEMS\Lib\Utils\FileProvider;
use Throwable;


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
            'import' => 'Import'
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

    public function RefreshNumber():array
    {
        $basicId = $this->request->basicId??null;
        if($basicId)
        {
            $basic = Basic::find($basicId);
            if(!$basic->basicId) error(['error' => '考场不存在']);
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

    public function Data():array
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
            if($search['passport']??false)$query->where('empassport', $search['passport']);
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
        error(['error' => '参数错误']);
    }

    public function Import():array
    {
        $file = $this->request->getFile('file');
        $basicId = $this->request->basicId??null;
        try{
            $service = new FileProvider();
            $result = $service->upload($file);
            if($result['success'])
            {
                $data = ExcelProvider::importExcel($result['path'])??[];
                if(empty($data))throw new Exception('导入文件数据为空');
                ExamService::importBasicMember($data,$basicId);
                return ['msg' => '导入成功'];
            }
            else error($result['error']);
        }catch (Throwable $e){
            error($e->getMessage());
        }
    }

    public function Delete():array
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) error(['error' => '未选择要删除的记录']);
        ExamMember::getQuery()->whereIn('emid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array
    {
        $cmId = $this->request->emId??null;
        if(!$cmId) error(['error' => '参数错误']);
        $model = ExamMember::find($cmId);
        if(!$model->emId) error(['error' => '记录不存在']);
        $cmendtime = $this->request->emendtime??null;
        if(!$cmendtime) error(['error' => '请输入到期时间']);
        $cmendtime = strtotime($cmendtime);
        if($cmendtime < TIME) error(['error' => '请输入正确的到期时间']);
        $model->emendtime = $cmendtime;
        if(!$model->save()) error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function AddByPassport():array
    {
        $mids = $this->request->mids??[];
        if(empty($mids)) error(['error' => '请选择用户']);
        $basicId = $this->request->basicId??null;
        if(!$basicId) error(['error' => '请选择课程']);
        $endTime = $this->request->endTime??'';
        $endTime = strtotime($endTime);
        if($endTime < TIME)error(['error' => '请输入正确的到期时间']);
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
                $model->toValidate();
                if(!$model->save()) error(['error' => '通行证ID：'.$passport.'用户添加失败']);
            }
            else
            {
                if($model->emendtime != $endTime)
                {
                    $model->emendtime = $endTime;
                    if(!$model->save()) error(['error' => '通行证ID：'.$passport.'用户添加失败']);
                }
            }
        }
        return ['msg' => '添加成功'];
    }

    public function Add():array
    {
        $data = [
            'embasicId' => $this->request->embasicId??null,
            'emendtime' => strtotime($this->request->emendtime)??null,
            'emstarttime' => TIME,
            'empassport' => $this->request->empassport??null,
        ];
        if($data['emendtime'] < TIME) error(['error' => '请输入正确的到期时间']);
        $model = ExamMember::findByPassportAndSubjectId($data['empassport'],$data['embasicId']);
        if(!$model->emId)
        {
            $model = ExamMember::fillWithInit($data);
            $result = $model->toValidate();
            if($result !== true)return $result;
            if(!$model->save()) error(['error' => '添加失败']);
        }
        else
        {
            if($data['emendtime'] < $model->emendtime)error(['error' => '请输入正确的到期时间']);
            $model->emendtime = $data['emendtime'];
            if(!$model->save()) error(['error' => '添加失败']);
        }
        return ['msg' => '添加成功'];
    }
    public function Index():array
    {
        return [];
    }
}