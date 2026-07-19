<?php

namespace PHPEMS\App\User\Controller\App;

use PHPEMS\App\Cert\Service\CertService;
use PHPEMS\App\Course\Service\CourseService;
use PHPEMS\App\Course\Service\Model\CourseSession;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\App\User\Service\Model\UserExpense;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\Lib\Auth\Auth;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\FileProvider;

class Index extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Json'];

    static public function getRoutes():array
    {
        return [
            'data' => 'Data',
            'profile' => 'Profile',
            'password' => 'Password',
            'verify' => 'Verify',
            'cancel' => 'cancelVerify',
            'index' => 'Index',
            'laststudy' => 'LastStudy',
            'basic' => 'Basic',
            'course' => 'Course',
            'cert' => 'Cert',
            'expense' => 'Expense',
            'recharge' => 'Recharge'
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

    public function Recharge():array|Error
    {
        $amount = $this->request->amount??0;
        if($amount < 10)return error(['error' => '最少充值10积分']);
        if($amount % 10 > 0)return error(['error' => '参数错误']);
        $user = $this->request->getUser();
        $order = TradeOrder::fillWithInit([
            'ordersn' => date('YmdHis').rand(100,999),
            'ordertitle' => "充值积分".$amount,
            'orderpassport' => $user->userpassport,
            'orderitems' => json_encode([
                'itemtype' => 'recharge',
                'items' => [
                    'rate' => 10
                ]
            ],true),
            'ordercreatetime' => TIME,
            'orderstatus' => 1,
            'orderprice' => intval($amount / 10),
            'orderrawprice' => intval($amount / 10),
        ]);
        $result = $order->toValidate();
        if($result !== true)return $result;
        $order->save();
        return $order->getRaw();
    }

    public function LastStudy():array|Error
    {
        $user = $this->request->getUser();
        $data = [];
        $courseSession = CourseSession::findByPassport($user->userpassport);
        if($courseSession->csnid) {
            $data['course'] = CourseSubject::find($courseSession->csncsid)->getRaw();
        }
        $examSession = ExamSession::findByPassport($user->userpassport);
        if($examSession->esid) {
            $data['exam'] = Basic::find($examSession->esbasicid)->getRaw();
        }
        return $data;
    }

    public function Cert():array|Error
    {
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $user = $this->request->getUser();
        $data = CertService::getCertQueryWithMember()->select(['ceid','cetitle','cethumb','cemsn','cemexpirytime'])
            ->where('empassport' , $user->userpassport)
            ->where('emendtime',">",TIME)
            ->orderBy('emid', 'DESC')
            ->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['cemexpirytime'] = date('Y-m-d',$item['cemexpirytime']);
        });
        return $data;
    }

    public function Basic():array|Error
    {
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $user = $this->request->getUser();
        $data = ExamService::getBasicQueryWithMember()->select(['basicid','basic','basicthumb','basicdescribe','emendtime'])
            ->where('empassport' , $user->userpassport)
            ->where('emendtime',">",TIME)
            ->orderBy('emid', 'DESC')
            ->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['emendtime'] = date('Y-m-d',$item['emendtime']);
        });
        return $data;
    }

    public function Course():array|Error
    {
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $user = $this->request->getUser();
        $data = CourseService::getCourseSubjectQueryWithMember()->select(['csid','cstitle','csthumb','csdescribe','cmendtime'])
            ->where('cmpassport' , $user->userpassport)
            ->where('cmendtime',">",TIME)
            ->orderBy('cmid', 'DESC')
            ->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['cmendtime'] = date('Y-m-d',$item['cmendtime']);
        });
        return $data;
    }

    public function Expense():array|Error
    {
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $user = $this->request->getUser();
        $query = UserExpense::getQuery()
            ->where('uepassport' , $user->userpassport)
            ->orderBy('ueid', 'DESC');
        $data = $query->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['uetime'] = date('Y-m-d H:i:s',$item['uetime']);
        });
        return $data;
    }

    public function Profile():array|Error
    {
        $user = $this->request->getUser();
        $data = [
            'usergender' => $this->request->usergender??null
        ];
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });
        if(empty($data)){
            return error(['error' => '未提交任何修改']);
        }

        // 更新数据
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }

        if (!$user->save()) {
            return error(['error' => '修改失败']);
        }

        return ['msg' => '修改成功'];
    }

    public function cancelVerify(): array|Error
    {
        $user = $this->request->getUser();
        if($user->userstatus === 1)
        {
            $user->userstatus = 0;
            $result = $user->save();
            if($result)return ['msg' => '撤回成功'];
        }
        return error(['error' => '撤回失败']);
    }

    public function Password():array|Error
    {
        $user = $this->request->getUser();
        $oldpassword = $this->request->oldpassword??null;
        $newpassword = $this->request->newpassword??null;
        $newpassword2 = $this->request->newpassword2??null;
        if($oldpassword && $newpassword && $newpassword2)
        {
            if($newpassword != $newpassword2)return error(['error' => '两次密码输入不一致，请重新输入！']);
            if(!$newpassword || strlen($newpassword) < 6)return error(['error' => '密码不符合规范']);
            if(!$user->validatePassword($oldpassword))return error(['error' => '原密码校验失败，请重新输入！']);
            if(!$newpassword || strlen($newpassword) < 6)return error(['error' => '密码不符合规范']);
            $user->userpassword = User::saltPassword($newpassword);
            $result = $user->save();
            if($result)
            {
                Auth::logout();
                return ['msg' => '修改密码成功'];
            }
            else return error(['error' => '修改密码失败']);
        }
        else return error(['error' => '密码不符合规范']);
    }

    public function Verify():array|Error
    {
        $user = $this->request->getUser();
        $userphoto = $this->request->userphoto??null;
        $userpassport = $this->request->userpassport??null;
        $usertruename = $this->request->usertruename??null;
        if($userphoto && $userpassport && $usertruename)
        {
            if($user->userpassport != $userpassport)return error('通行证ID校验失败');
            if($user->userstatus >= 1)return error(['error' => '您已提交过实名信息，请等待审核']);
            $user->userphoto = $userphoto;
            $user->usertruename = $usertruename;
            $user->userstatus = 1;
            $result = $user->save();
            if($result)return ['msg' => '提交实名信息成功'];
            else error(['error' => '提交失败']);
        }
        return error(['error' => '缺少认证信息']);
    }

    public function Data():array|Error
    {
        $user = $this->request->getUser();
        $userMoney = UserMoney::findByPassport($user->userpassport);
        return [
            'username' => $user->username,
            'usertruename' => $user->usertruename,
            'userpassport' => $user->userpassport,
            'userphoto' => $user->userphoto,
            'useremail' => $user->useremail,
            'usergender' => $user->usergender,
            'userphone' => substr($user->userphone,0,3).'****'.substr($user->userphone,-4),
            'isadmin' => $user->isAdmin(),
            'userstatus' => $user->userstatus,
            'usercoin' => $userMoney->umamount??0,
        ];
    }

    public function Index(): Error|array
    {
        return [];
    }
}