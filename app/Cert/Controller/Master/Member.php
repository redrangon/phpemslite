<?php

namespace PHPEMS\App\Cert\Controller\Master;

use PHPEMS\App\Cert\Service\Model\CertMember;
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
            'verify' => 'Verify',
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
        $ceId = $this->request->ceId??null;
        if($ceId)
        {
            $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
            if(!$cert->ceId) return error(['error' => '证书不存在']);
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

    public function Data():array | Error
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
        return error(['error' => '参数错误']);
    }

    public function Verify():array | Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        CertMember::getQuery()->whereIn('cemid', $ids)->update(['cemstatus' => 1]);
        return ['msg' => '删除成功'];
    }

    public function Delete():array | Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        CertMember::getQuery()->whereIn('cemid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array | Error
    {
        $cemId = $this->request->cemId??null;
        if(!$cemId) return error(['error' => '参数错误']);
        $model = CertMember::find($cemId);
        if(!$model->cemId) return error(['error' => '记录不存在']);
        $cemexpirytime = $this->request->cemexpirytime??null;
        if(!$cemexpirytime) return error(['error' => '请输入到期时间']);
        $cemexpirytime = strtotime($cemexpirytime);
        if($cemexpirytime < TIME) return error(['error' => '请输入正确的到期时间']);
        $model->cemexpirytime = $cemexpirytime;
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function AddByPassport():array | Error
    {
        $mids = $this->request->mids??[];
        if(empty($mids)) return error(['error' => '请选择用户']);
        $ceId = $this->request->ceId??null;
        if(!$ceId) return error(['error' => '参数错误']);
        $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
        if(!$cert->ceId) return error(['error' => '参数错误']);
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
                if(!$model->save()) return error(['error' => '通行证ID：'.$passport.'用户添加失败']);
            }
            else
            {
                return error(['error' => '通行证ID：'.$passport.'用户有未过期证件']);
            }
        }
        return ['msg' => '添加成功'];
    }

    public function Add():array | Error
    {
        $ceId = $this->request->ceId??null;
        if(!$ceId) return error(['error' => '参数错误']);
        $cert = \PHPEMS\App\Cert\Service\Model\Cert::find($ceId);
        if(!$cert->ceId) return error(['error' => '参数错误']);
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
            if(!$model->save()) return error(['error' => '添加失败']);
        }
        else
        {
            return error(['error' => '用户有未过期证件']);
        }
        return ['msg' => '添加成功'];
    }
    public function Index():array | Error
    {
        return [];
    }
}