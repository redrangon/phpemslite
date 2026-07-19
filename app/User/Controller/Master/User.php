<?php

namespace PHPEMS\App\User\Controller\Master;

use Exception;
use PDOException;
use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\Member\Service\Model\Member;
use PHPEMS\App\User\Service\Model\UserGroup;
use PHPEMS\App\User\Service\Model\UserLog;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\App\User\Service\UserService;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Env;
use PHPEMS\Lib\Utils\ExcelProvider;
use PHPEMS\Lib\Utils\FileProvider;
use Throwable;

class User extends Controller implements ControllerInterface
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
            'log' => 'Log',
            'password' => 'modifyPassword',
            'getconfig' => 'getConfig',
            'setconfig' => 'setConfig',
            'groups' => 'getGroups',
            'group' => 'getGroup',
            'modifygroup' => 'modifyGroup',
            'verify' => 'Verify',
            'defaultgroup' => 'setDefaultGroup',
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

    public function Log():array|Error
    {
        $search = $this->request->search??null;
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $userid = $this->request->userid??null;
        if(!$userid)return error(['error' => '用户ID不能为空']);
        $query = UserLog::getQuery()->where('uluserid', $userid)->orderBy('ultime', 'desc');
        if($search['range']??false)
        {
            $query->where('ultime', '>=', strtotime($search['range'][0])??0);
            $query->where('ultime', '<=', strtotime($search['range'][1])??TIME);
        }
        $data = $query->paginate($page, $limit);
        array_walk($data['data'],function(&$item){
            $item['ultime'] = date('Y-m-d H:i:s', $item['ultime']);
        });
        return $data;
    }

    public function setDefaultGroup():array|Error
    {
        $groupId = $this->request->groupId??null;
        if(!$groupId)return error(['error' => '修改失败']);
        $group = UserGroup::find($groupId);
        if($group->groupId)
        {
            if($group->groupModuleId == 1)return error(['error' => '修改失败,管理员不能被默认注册']);
            if(!$group->groupDefault)
            {
                UserGroup::getQuery()->update(['groupDefault'=>0]);
                $group->groupDefault = 1;
                $group->save();
            }
            return ['msg' => '操作成功'];
        }
        else return error(['error' => '修改失败']);
    }

    public function getConfig():array
    {
        return UserService::getUserAppConfig();
    }

    public function setConfig():array|Error
    {
        $config = [
            'closeregist' => $this->request->closeregist??0,
            'loginmodel' => $this->request->loginmodel??0,
            'userverify' => $this->request->userverify??0,
            'emailverify' => $this->request->emailverify??0,
            'emailaccount' => $this->request->emailaccount??'',
            'emailpassword' => $this->request->emailpassword??''
        ];
        $app = App::getAppByCode('user');
        $app->appsetting = json_encode($config, JSON_UNESCAPED_UNICODE);
        if(!$app->save())return error(['error' => '修改失败']);
        return ['msg'=>'设置成功'];
    }

    public function getGroups():array
    {
        return UserGroup::getQuery()->orderBy('groupid', 'desc')->get();
    }

    public function getGroup()
    {
        $groupId = $this->request->groupid;
        return UserGroup::find($groupId)->getRaw();
    }

    public function Add():array|Error
    {
        $username = $this->request->username??null;
        $useremail = $this->request->useremail??null;
        $userpassword = $this->request->userpassword??null;
        $usergroupid = $this->request->usergroupid??null;
        if(!$username)return error(['error' => '用户名不能为空']);
        if(!$useremail)return error(['error' => '邮箱不能为空']);
        if(strlen($userpassword) < 6)return error(['error' => '密码不能少于6位']);
        if(!$usergroupid)return error(['error' => '用户组不能为空']);
        $user = \PHPEMS\App\User\Service\Model\User::findByUserName($username);
        if($user->userid)return error(['error' => '用户名已存在']);
        $user = \PHPEMS\App\User\Service\Model\User::findByUserEmail($useremail);
        if($user->userid)return error(['error' => '邮箱已被注册']);
        $group = UserGroup::find($usergroupid);
        if(!$group->groupid)return error(['error' => '用户组不存在']);
        $data = [
            'username' => $username,
            'useremail' => $useremail,
            'userpassword' => password_hash($userpassword, PASSWORD_DEFAULT),
            'userpassport' => \PHPEMS\App\User\Service\Model\User::generateUniqueId(),
            'userregtime' => TIME,
            'userregip' => Env::getClientIp(),
            'usergroupid' => $this->request->usergroupid??1,
            'userphoto' => $this->request->userphoto??'',
            'usertruename' => $this->request->usertruename??'',
            'usergender' => $this->request->usergender??'男',
        ];
        $user = \PHPEMS\App\User\Service\Model\User::fillWithInit($data);
        $result = $user->toValidate();
        if($result !== true)return $result;
        if(!$user->save())return error(['error' => '添加用户失败']);
        return [];
    }

    public function Modify():array|Error
    {
        $userid = $this->request->userid;
        $user = \PHPEMS\App\User\Service\Model\User::find($userid);
        $data = [
            'usergender' => $this->request->usergender,
            'userphoto' => $this->request->userphoto,
            'usertruename' => $this->request->usertruename,
            'usergroupid' => $this->request->usergroupid,
            'userpassport' => $this->request->userpassport,
        ];
        $data = array_filter($data,fn($value) => $value !== null);
        foreach ($data as $key => $value)
        {
            $user->$key = $value;
        }
        if(!$user->save())return error(['error' => '修改资料失败']);
        return ['msg'=>'修改成功'];
    }

    public function  Delete():array|Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids))return error(['error' => '未选择要删除的用户']);
        \PHPEMS\App\User\Service\Model\User::getQuery()->whereIN('userid', $ids)->delete();
        return ['msg'=>'删除成功'];
    }

    public function modifyPassword():array|Error
    {
        $password = $this->request->password;
        $userid = $this->request->userid;
        $user = \PHPEMS\App\User\Service\Model\User::find($userid);
        if(!$user->userid)return error(['error' => '修改密码失败']);
        $data = [
            'userpassword' => password_hash($password, PASSWORD_DEFAULT)
        ];
        foreach ($data as $key => $value)
        {
            $user->$key = $value;
        }
        $user->save();
        return ['msg' => '修改密码成功'];
    }

    public function Data(): array
    {
        $query = UserService::getUserWithGroupQuery()->orderBy('userid', 'DESC');
        $page = $this->request->page??1;
        $limit = $this->request->limit??20;
        $search = $this->request->search;
        if($search)
        {
            if($search['userid']??false)$query->where('userid', $search['userid']);
            if($search['username']??false)$query->where('username', $search['username']);
            if($search['range']??false)
            {
                $query->where('userregtime', '>=', strtotime($search['range'][0]));
                $query->where('userregtime', '<=', strtotime($search['range'][1]));
            }
            if($search['useremail']??false)$query->where('useremail', $search['useremail']);
            if($search['groupid']??false)$query->where('usergroupid', $search['groupid']);
            if($search['userstatus']??false)$query->where('userstatus', $search['userstatus']);
        }
        $data = $query->paginate($page, $limit);
        $coins = [];
        $passports = [];
        foreach($data['data'] as $item)
        {
            $passports[] = $item['userpassport'];
        }
        $coins = UserMoney::getQuery()->whereIN('umpassport', $passports)->get();
        $coins = array_column($coins, 'umamount', 'umpassport');
        array_walk($data['data'], function (&$item) use ($coins){
            $item['userregtime'] = date('Y-m-d', $item['userregtime']);
            $item['usercoin'] = $coins[$item['userpassport']]??0;
            unset($item['userpassword']);
            return $item;
        });
        return $data;
    }

    public function Verify():array|Error
    {
        $ids = $this->request->ids??[];
        $status = $this->request->status??0;
        if(empty($ids))return error(['error' => '参数错误']);
        foreach($ids as $id)
        {
            $user = \PHPEMS\App\User\Service\Model\User::find($id);
            if($status == 3)
            {
                if($user->userpassport) {
                    $member = Member::findByPassport($user->userpassport);
                    if(!$member->mid)
                    {
                        $member = Member::fillWithInit([
                            'mpassport' => $user->userpassport,
                            'mname' => $user->usertruename,
                            'mphoto' => $user->userphoto
                        ]);
                        $member->save();
                    }
                }

            }
            $user->userverifytime = TIME;
            $user->userStatus = $status;
            $user->save();
        }
        return [];
    }

    public function Import(): array|Error
    {
        $service = new FileProvider();
        $file = $this->request->getFile('file');
        try{
            $result = $service->upload($file);
            if($result['success'])
            {
                $group = UserGroup::findDefaultGroup();
                $data = ExcelProvider::importExcel($result['path'])??[];
                if(empty($data))throw new Exception('导入文件数据为空');
                UserService::importUser($data, $group->groupId);
                return ['msg' => '导入成功'];
            }
            else return error($result['error']);
        }catch (Throwable $e){
            return error($e);
        }
    }

    public function Index(): Error|array
    {
        return [];
    }
}