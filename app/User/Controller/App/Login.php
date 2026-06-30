<?php

namespace PHPEMS\App\User\Controller\App;

use PHPEMS\App\User\Service\Model\User;
use PHPEMS\App\User\Service\Model\UserGroup;
use PHPEMS\App\User\Service\Model\UserRandCode;
use PHPEMS\App\User\Service\UserService;
use PHPEMS\Lib\Auth\Auth;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Captcha;
use PHPEMS\Lib\Utils\Env;
use PHPEMS\Lib\Utils\Messenger\MessengerProvider;


class Login extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Json'];

    static public function getRoutes():array
    {
        return [
            'register' => 'Register',
            'login' => 'Login',
            'logout' => 'Logout',
            'setting' => 'Setting',
            'regcode' => 'SendRegisterCode',
            'findcode' => 'SendFindPasswordCode',
            'findpassword' => 'FindPassWord',

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

    public function Setting():array
    {
        return UserService::getUserAppConfig();
    }

    public function SendFindPasswordCode(): Error|array
    {
        $username = $this->request->username??null;
        $useremail = $this->request->useremail??null;
        $captchaId = $this->request->captchaId;
        $captcha = $this->request->captcha;
        if (empty($username) || empty($useremail) || empty($captchaId) || empty($captcha)) return error(['error' => '错误的参数']);
        if(!Captcha::verify($captcha, $captchaId))return error(['error' => '验证码错误']);
        $user = User::findByUserName($username);
        if(!$user->userId)return error(['error' => '用户名不存在']);
        if($user->userEmail != $useremail)return error(['error' => '用户名与邮箱不匹配']);
        $randCode = UserRandCode::findByTargetAndType($user->userEmail,'findpassword');
        if($randCode->urcId && ($randCode->urcSendTime + 120 > TIME))return error(['error' => '验证码发送过于频繁'.$randCode->urcId.'|'.($randCode->urcSendTime + 120).'|'.TIME]);
        $codeString = rand(100000,999999);
        $randCode = UserRandCode::fillWithInit([
            'urctarget' => $user->userEmail,
            'urctype' => 'findpassword',
            'urcsendtime' => TIME,
            'urcstring' => $codeString
        ]);
        $message = [
            'subject' => '找回密码验证码',
            'content' => "您的找回密码验证码是{$codeString},请在".date('Y-m-d H:i:s',TIME + 300)."前输入并验证！",
        ];
        $result = MessengerProvider::sendMessage($user->userEmail,$message);
        if($result === true)
        {
            $randCode->save();
            return [];
        }
        else return $result;
    }

    public function FindPassWord():array | Error
    {
        $username = $this->request->username??null;
        $useremail = $this->request->useremail??null;
        $codeString = $this->request->randcode??null;
        $userpassword = $this->request->userpassword??null;
        if (empty($username) || empty($useremail) || empty($userpassword) || empty($codeString)) return error(['error' => '错误的参数']);
        $user = User::findByUserName($username);
        if(!$user->userId)return error(['error' => '用户名不存在']);
        if($user->userEmail != $useremail)return error(['error' => '用户名与邮箱不匹配']);
        $randCode = UserRandCode::findByTargetAndType($useremail,'findpassword');
        if($randCode->urcSendTime + 300 < TIME)return error(['error' => '验证码已过期']);
        if($randCode->urcString == $codeString)
        {
            $userpassword = password_hash($userpassword, PASSWORD_DEFAULT);
            if($userpassword == $user->userpassword)return error(['error' => '密码不能与原密码一样']);
            $user->userpassword = $userpassword;
            if($user->save())
            {
                $randCode->delete();
                return ['msg' => '密码修改成功'];
            }
            else return error(['error' => '密码修改失败，请稍后尝试']);
        }
        else return error(['error' => '验证码对比失败']);
    }

    public function SendRegisterCode(): Error|array
    {
        $username = $this->request->username??null;
        $useremail = $this->request->useremail??null;
        $captchaId = $this->request->captchaId;
        $captcha = $this->request->captcha;
        if (empty($username) || empty($useremail) || empty($captchaId) || empty($captcha)) return error(['error' => '错误的参数']);
        if(!Captcha::verify($captcha, $captchaId))return error(['error' => '验证码错误']);
        $user = User::findByUserName($username);
        if($user->userId)return error(['error' => '用户名已存在']);
        $user = User::findByUserEmail($useremail);
        if($user->userId)return error(['error' => '邮箱已被注册']);
        $randCode = UserRandCode::findByTargetAndType($useremail,'register');
        if($randCode->urcId && ($randCode->urcSendTime + 120 > TIME))return error(['error' => '验证码发送过于频繁']);
        $codeString = rand(100000,999999);
        $randCode = UserRandCode::fillWithInit([
            'urctarget' => $useremail,
            'urctype' => 'register',
            'urcsendtime' => TIME,
            'urcstring' => $codeString
        ]);
        $message = [
            'subject' => '注册验证码',
            'content' => "您的注册验证码是{$codeString},请在".date('Y-m-d H:i:s',TIME + 300)."前输入并验证！",
        ];
        $result = MessengerProvider::sendMessage($useremail,$message);
        if($result === true)
        {
            $randCode->save();
            return [];
        }
        else return $result;
    }

    public function Register():array | Error
    {
        $username = $this->request->username??null;
        $useremail = $this->request->useremail??null;
        $userpassword = $this->request->userpassword??null;
        $codeString = $this->request->randcode??null;
        if (empty($username) || empty($useremail) || empty($userpassword) || empty($codeString)) return error(['error' => '错误的参数']);
        $user = User::findByUserName($username);
        if($user->userId)return error(['error' => '用户名已存在']);
        $user = User::findByUserEmail($useremail);
        if($user->userId)return error(['error' => '邮箱已被注册']);
        $randCode = UserRandCode::findByTargetAndType($useremail,'register');
        if($randCode->urcSendTime + 300 < TIME)return error(['error' => '验证码已过期']);
        if($randCode->urcString == $codeString)
        {
            $defaultGroup = UserGroup::findDefaultGroup();
            if(!$defaultGroup->groupId)return error(['error' => '错误的配置']);
            $user = User::fillWithInit([
                'username' => $username,
                'useremail' => $useremail,
                'userpassword' => password_hash($userpassword, PASSWORD_DEFAULT),
                'usergroupid' => $defaultGroup->groupId,
                'userregip' => Env::getClientIp(),
                'userregtime' => TIME,
                'userpassport' => User::generateUniqueId()
            ]);
            $result = $user->toValidate();
            if($result !== true)return $result;
            if($user->save()){
                $randCode->delete();
                return ['msg' => '注册成功'];
            }
            else return error(['error' => '注册失败，请稍后尝试']);
        }
        else return error(['error' => '验证码对比失败'.$randCode->urcString.'|'.$codeString]);
    }

    public function Logout(): array
    {
        Auth::logout();
        return ['message' => '退出成功'];
    }

    public function Login(): Error|array
    {
        $username = $this->request->username;
        $password = $this->request->password;
        $captchaId = $this->request->captchaId;
        $captcha = $this->request->captcha;
        if($username && $password && $captchaId && $captcha)
        {
            if(!Captcha::verify($captcha, $captchaId))return error(['error' => '验证码错误']);
            $user = User::findByUserName($username);
            if($user->validatePassword($password))
            {
                $token = Auth::login([
                    'userid' => $user->userid,
                ]);
                return ['token' => $token];
            }
            else return error(['error' => '用户名或密码错误']);
        }
        else return error(['error' => '缺少参数']);
    }

    public function index(): array
    {
        return [];
    }
}