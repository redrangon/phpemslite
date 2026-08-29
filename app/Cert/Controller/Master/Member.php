<?php

namespace PHPEMS\App\Cert\Controller\Master;

use PHPEMS\App\Cert\Service\Model\CertMember;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;


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
            'verify' => 'Verify',
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

    public function Import():array
    {
        $importType = $this->request->importType??null;
        $importData = $this->request->importData??null;
        if($importType && $importData)
        {
            $ceId = $this->request->ceId??null;
            if(!$ceId) error(['error' => '参数错误']);
            $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
            if(!$cert->ceId) error(['error' => '参数错误']);
            $passports = [];
            $importData = explode("\n",$importData);
            foreach($importData as $data)
            {
                $data = trim($data);
                if($importType == 1)
                {
                    $userModel = User::findByUserName($data);
                    if($userModel->userpassport)
                    {
                        $passports[] = [
                            $userModel->userpassport,
                            $data
                        ];
                    }
                    else error(['error' => '用户名'.$data.'不存在']);
                }
                else
                {
                    $memberModel = \PHPEMS\App\Member\Service\Model\Member::findByPassport($data);
                    if($memberModel->mPassport)
                    {
                        $passports[] = [
                            $userModel->userpassport
                        ];
                    }
                    else error(['error' => '通行证'.$data.'不存在']);
                }
            }
            $endTime = TIME + $cert->cedays * 24 * 60 * 60;
            foreach($passports as $indexId => $data) {
                $passport = $data[0];
                $userName = $data[1]??null;
                $model = CertMember::findByPassportAndCeId($passport, $ceId);
                if (!$model->cemId) {
                    $model = CertMember::fillWithInit([
                        'cemceid' => $ceId,
                        'cemexpirytime' => $endTime,
                        'cemtime' => TIME,
                        'cempassport' => $passport,
                        'cemstatus' => 0,
                        'cemsn' => date('YmdHis') . rand(100, 999) . ($indexId >= 1000 ?: str_pad($indexId, 4, '0', STR_PAD_LEFT))
                    ]);
                    $result = $model->toValidate();
                    if ($result !== true) error(['error' => ($userName?'用户名:'.$userName:'').'通行证ID：' . $passport . '用户添加失败']);
                    if (!$model->save()) error(['error' => ($userName?'用户名:'.$userName:'').'通行证ID：' . $passport . '用户添加失败']);
                } else {
                    error(['error' => ($userName?'用户名:'.$userName:'').'通行证ID：' . $passport . '用户有未过期证件']);
                }
            }
            return ['msg' => '导入成功'];
        }
        error(['error' => '参数错误']);
    }

    public function RefreshNumber():array
    {
        $ceId = $this->request->ceId??null;
        if($ceId)
        {
            $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
            if(!$cert->ceId) error(['error' => '证书不存在']);
            $query = CertMember::getQuery()->where('cemceId', $ceId);
            $number = $query->count();
            if($number != $cert->ceNumber)
            {
                $cert->ceNumber = $number;
                $cert->save();
            }
        }
        return ['msg' => '刷新成功'];
    }

    public function Data():array
    {
        $ceId = $this->request->ceId??null;
        if($ceId)
        {
            $page = $this->request->page??1;
            if($page < 1)$page = 1;
            $limit = $this->request->limit??20;
            $query = CertMember::getQuery()->where('cemceid', $ceId);
            $search = $this->request->search??[];
            if($search['username']??false)
            {
                $user = User::findByUserName($search['username']);
                $query->where('cempassport', $user->userpassport);
            }
            $data = $query->paginate($page, $limit);
            $passports = [];
            foreach($data['data'] as $item)
            {
                $passports[] = $item['cempassport'];
            }
            $members = \PHPEMS\App\Member\Service\Model\Member::getQuery()->select(['mpassport','mname'])
                ->whereIn('mpassport', $passports)
                ->get();
            $members = array_column($members,null,'mpassport');
            array_walk($data['data'],function(&$item) use($members){
                $item['cemtime'] = date('Y-m-d',$item['cemtime']);
                $item['cemexpirytime'] = date('Y-m-d',$item['cemexpirytime']);
                $item['mname'] = $members[$item['cempassport']]['mname']??'';
            });
            return $data;
        }
        error(['error' => '参数错误']);
    }

    public function Verify():array
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) error(['error' => '未选择要删除的记录']);
        CertMember::getQuery()->whereIn('cemid', $ids)->update(['cemstatus' => 1]);
        return ['msg' => '删除成功'];
    }

    public function Delete():array
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) error(['error' => '未选择要删除的记录']);
        CertMember::getQuery()->whereIn('cemid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array
    {
        $cemId = $this->request->cemId??null;
        if(!$cemId) error(['error' => '参数错误']);
        $model = CertMember::find($cemId);
        if(!$model->cemId) error(['error' => '记录不存在']);
        $cemexpirytime = $this->request->cemexpirytime??null;
        if(!$cemexpirytime) error(['error' => '请输入到期时间']);
        $cemexpirytime = strtotime($cemexpirytime);
        if($cemexpirytime < TIME) error(['error' => '请输入正确的到期时间']);
        $model->cemexpirytime = $cemexpirytime;
        if(!$model->save()) error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function AddByPassport():array
    {
        $mids = $this->request->mids??[];
        if(empty($mids)) error(['error' => '请选择用户']);
        $ceId = $this->request->ceId??null;
        if(!$ceId) error(['error' => '参数错误']);
        $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
        if(!$cert->ceId) error(['error' => '参数错误']);
        $endTime = TIME + $cert->cedays * 24 * 60 * 60;
        foreach($mids as $indexId => $passport)
        {
            $model = CertMember::findByPassportAndCeId($passport,$ceId);
            if(!$model->cemId)
            {
                $model = CertMember::fillWithInit([
                    'cemceid' => $ceId,
                    'cemexpirytime' => $endTime,
                    'cemtime' => TIME,
                    'cempassport' => $passport,
                    'cemstatus' => 0,
                    'cemsn' => date('YmdHis').rand(100,999).($indexId >= 1000?:str_pad($indexId,4,'0',STR_PAD_LEFT))
                ]);
                $result = $model->toValidate();
                if($result !== true)return $result;
                if(!$model->save()) error(['error' => '通行证ID：'.$passport.'用户添加失败']);
            }
            else
            {
                error(['error' => '通行证ID：'.$passport.'用户有未过期证件']);
            }
        }
        return ['msg' => '添加成功'];
    }

    public function Add():array
    {
        $ceId = $this->request->ceId??null;
        if(!$ceId) error(['error' => '参数错误']);
        $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
        if(!$cert->ceId) error(['error' => '参数错误']);
        $endTime = TIME + $cert->cedays * 24 * 60 * 60;
        $data = [
            'cemceid' => $ceId,
            'cemtime' => TIME,
            'cemexpirytime' => $endTime,
            'cempassport' => $this->request->cempassport??null,
        ];

        $model = CertMember::findByPassportAndCeId($data['cempassport'],$data['cemceid']);
        if(!$model->cemId)
        {
            $model = CertMember::fillWithInit($data);
            $result = $model->toValidate();
            if($result !== true)return $result;
            if(!$model->save()) error(['error' => '添加失败']);
        }
        else
        {
            error(['error' => '用户有未过期证件']);
        }
        return ['msg' => '添加成功'];
    }
    public function Index():array
    {
        return [];
    }
}