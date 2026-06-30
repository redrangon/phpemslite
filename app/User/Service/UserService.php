<?php

namespace PHPEMS\App\User\Service;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\App\User\Service\Model\UserGroup;
use PHPEMS\Lib\DataBase\QueryBuilder;

class UserService
{
    public static function getUserWithGroupQuery():QueryBuilder
    {
        $groupTable = UserGroup::getTableName();
        $groupKey = UserGroup::getPrimaryKey();
        return User::getQuery()->join($groupTable, 'usergroupid', '=', $groupKey);
    }

    public static function getUserAppConfig():array
    {
        $app = App::getAppByCode('user');
        if(!$app->appid){
            $config = [
                'closeregist' => 0,
                'loginmodel' => 0,
                'userverify' => 0,
                'emailverify' => 0,
                'emailaccount' => '',
                'emailpassword' => ''
            ];
            $data = [
                'appcode' => 'user',
                'appname' => '用户',
                'appsetting' => json_encode($config, JSON_UNESCAPED_UNICODE)
            ];
            $data = App::fill($data);
            App::getQuery()->insert($data);
        }
        else
        {
            $config = json_decode($app->appsetting, true)??[];
        }
        return $config;
    }
}