<?php

namespace PHPEMS\App\User\Controller\Master;

use PHPEMS\App\User\Service\Model\UserGroup;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;


class Group extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'add' => 'Add',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'default' => 'setDefault'
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

    public function Add():array
    {
        $data = [
            'groupname' => $this->request->groupname??null,
            'groupdescribe' => $this->request->groupdescribe??null
        ];
        $group = UserGroup::fillWithInit($data);
        $result = $group->toValidate();
        if($result !== true)return $result;
        if(!$group->save())error(['error' => '添加用户组失败']);
        return [];
    }

    public function Modify():array
    {
        $groupId = $this->request->groupid??null;
        $group = UserGroup::find($groupId);
        if(!$group->groupId)error('参数错误');
        $data = [
            'groupname' => $this->request->groupname??null,
            'groupdescribe' => $this->request->groupdescribe??null
        ];
        $data = array_filter($data,fn($value) => $value !== null);
        foreach ($data as $key => $value)
        {
            $group->$key = $value;
        }
        if(!$group->save())error(['error' => '修改资料失败']);
        return ['msg'=>'修改成功'];
    }

    public function  Delete():array
    {
        $ids = $this->request->ids??[];
        if(empty($ids))error(['error' => '未选择要删除的用户']);
        $number = \PHPEMS\App\User\Service\Model\User::getQuery()->whereIn('usergroupid',$ids)->count();
        if($number)error('当前用户组下还有用户，不能删除');
        UserGroup::getQuery()->whereIn('groupid', $ids)->delete();
        return ['msg'=>'删除成功'];
    }

    public function setDefault():array
    {
        $groupId = $this->request->groupId??null;
        if(!$groupId)error(['error' => '修改失败']);
        $group = UserGroup::find($groupId);
        if($group->groupId)
        {
            if($group->groupModuleId == 1)error(['error' => '修改失败,管理员不能被默认注册']);
            if(!$group->groupDefault)
            {
                UserGroup::getQuery()->update(['groupDefault'=>0]);
                $group->groupDefault = 1;
                $group->save();
            }
            return ['msg' => '操作成功'];
        }
        else error(['error' => '修改失败']);
    }

    public function Data(): array
    {
        return UserGroup::getQuery()->orderBy('groupid', 'desc')->get();
    }

    public function Index(): array
    {
        $groupId = $this->request->groupid??null;
        if(!$groupId)error('参数错误');
        return UserGroup::find($groupId)->getRaw()??[];
    }
}