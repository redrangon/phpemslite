<?php

namespace PHPEMS\App\User\Service;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\Member\Service\Model\Member;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\App\User\Service\Model\UserGroup;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Utils\Env;

class UserService
{
    public static function getUserWithGroupQuery():QueryBuilder
    {
        $groupTable = UserGroup::getTableName();
        $groupKey = UserGroup::getPrimaryKey();
        return User::getQuery()->join($groupTable, 'usergroupid', '=', $groupKey);
    }

    static public function getUserWithMoneyQuery():QueryBuilder
    {
        $moneyTable = UserMoney::getTableName();
        $moneyKey = 'umpassport';
        return User::getQuery()->join($moneyTable, 'userpassport', '=', $moneyKey);
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

    public static function importUser(array $data,int $defaultGroupId):void
    {
        foreach($data as $index => $item)
        {
            if(!$index)continue;
            $userName = $item[0];
            $userEmail = $item[1];
            $userPassword = $item[2];
            $userTrueName = $item[3];
            $data = [
                'username' => $userName,
                'useremail' => $userEmail,
                'userpassport' => User::generateUniqueId(),
                'usergroupid' => $defaultGroupId,
                'userpassword' => User::saltPassword($userPassword),
                'userregtime' => TIME,
                'userstatus' => 0,
                'userregip' => Env::getClientIp(),
                'usertruename' => $userTrueName
            ];
            $user = User::fillWithInit($data);
            if($user->toValidate() === false)throw new \Exception("第".($index+1)."行数据校验失败，请检查");
            User::getDB()->transaction(function() use ($user,$index){
                if($user->save() === false)throw new \Exception("第".($index+1)."行数据校验失败，请检查");
                $member = Member::findByPassport($user->userpassport);
                if(!$member->mId)
                {
                    $member = Member::fillWithInit([
                        'mpassport' => $user->userpassport,
                        'mname' => $user->usertruename,
                    ]);
                    if($member->save())throw new \Exception("第".($index+1)."行数据保存失败，请重试");
                }
            });
        }
    }
}