<?php

namespace PHPEMS\App\User\Controller\Master;

use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\FileProvider;

class Index extends Controller implements ControllerInterface
{

    static protected array $publicFlows = ['Auth@admin','Json'];
    static public function getRoutes():array
    {
        return [];
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

    public function index(): Error|array
    {
        $user = $this->request->getUser();
        if(!$user)return error(['code' => 310]);
        return [
            'userid' => $user->userid,
            'username' => $user->username,
            'usergroupid' => $user->usergroupid,
            'userphoto' => $user->userphoto,
            'usergender' => $user->usergender
        ];
    }
}