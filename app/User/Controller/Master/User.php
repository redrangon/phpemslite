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
            'verify' => 'Verify',
            'import' => 'Import',
            'sync' => 'Sync'
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

    public function Sync():array
    {
        $ids = $this->request->ids??null;
        if(!$ids)error(['error' => '用户ID不能为空']);
        $users = \PHPEMS\App\User\Service\Model\User::getQuery()->whereIn('userid', $ids)->get();
        if(!$users)error(['error' => '用户不存在']);
        foreach ($users as $user)
        {
            $memberModel = Member::findByPassport($user['userpassport']);
            if(!$memberModel->mId)
            {
                $memberModel = Member::fillWithInit([
                    'mpassport' => $user['userpassport'],
                    'mname' => $user['usertruename'],
                    'msex' => $user['usergender']
                ]);
            }
            else
            {
                $memberModel->mname = $user['usertruename'];
                $memberModel->msex = $user['usergender'];
            }
            $memberModel->save();
        }
        return ['msg' => '导入完成'];
    }

    public function Log():array
    {
        $search = $this->request->search??null;
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $userid = $this->request->userid??null;
        if(!$userid)error(['error' => '用户ID不能为空']);
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



    public function Add():array
    {
        $username = $this->request->username??null;
        $useremail = $this->request->useremail??null;
        $userpassword = $this->request->userpassword??null;
        $usergroupid = $this->request->usergroupid??null;
        if(!$username)error(['error' => '用户名不能为空']);
        if(!$useremail)error(['error' => '邮箱不能为空']);
        if(strlen($userpassword) < 6)error(['error' => '密码不能少于6位']);
        if(!$usergroupid)error(['error' => '用户组不能为空']);
        $user = \PHPEMS\App\User\Service\Model\User::findByUserName($username);
        if($user->userid)error(['error' => '用户名已存在']);
        $user = \PHPEMS\App\User\Service\Model\User::findByUserEmail($useremail);
        if($user->userid)error(['error' => '邮箱已被注册']);
        $group = UserGroup::find($usergroupid);
        if(!$group->groupid)error(['error' => '用户组不存在']);
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
        if(!$user->save())error(['error' => '添加用户失败']);
        return [];
    }

    public function Modify():array
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
        if(!$user->save())error(['error' => '修改资料失败']);
        return ['msg'=>'修改成功'];
    }

    public function  Delete():array
    {
        $ids = $this->request->ids??[];
        if(empty($ids))error(['error' => '未选择要删除的用户']);
        \PHPEMS\App\User\Service\Model\User::getQuery()->whereIN('userid', $ids)->delete();
        return ['msg'=>'删除成功'];
    }

    public function modifyPassword():array
    {
        $password = $this->request->password;
        $userid = $this->request->userid;
        $user = \PHPEMS\App\User\Service\Model\User::find($userid);
        if(!$user->userid)error(['error' => '修改密码失败']);
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

    public function Verify():array
    {
        $ids = $this->request->ids??[];
        $status = $this->request->status??0;
        if(empty($ids))error(['error' => '参数错误']);
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

    public function Import(): array
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
            else error($result['error']);
        }catch (Throwable $e){
            error($e);
        }
    }

    public function Index(): array
    {
        return [];
    }
}