<?php

namespace PHPEMS\App\User\Controller\Master;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\User\Service\UserService;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

use PHPEMS\Lib\Utils\FileProvider;

class Index extends Controller implements ControllerInterface
{

    static protected array $publicFlows = ['Auth@admin','Json'];
    static public function getRoutes():array
    {
        return [
            'getconfig' => 'getConfig',
            'setconfig' => 'setConfig',
            'index' => 'Index'
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

    public function getConfig():array
    {
        return UserService::getUserAppConfig();
    }

    public function setConfig():array
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
        if(!$app->save())error(['error' => '修改失败']);
        return ['msg'=>'设置成功'];
    }

    public function index(): array
    {
        $user = $this->request->getUser();
        if(!$user)error(['code' => 310]);
        return [
            'userid' => $user->userid,
            'username' => $user->username,
            'usergroupid' => $user->usergroupid,
            'userphoto' => $user->userphoto,
            'usergender' => $user->usergender
        ];
    }
}