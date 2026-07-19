<?php

namespace PHPEMS\App\User\Controller\Master;

use PHPEMS\App\User\Service\Model\UserExpense;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\App\User\Service\UserService;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Coin extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'save' => 'Save'
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

    public function Save(): Error|array
    {
        $passport = $this->request->passport??null;
        $coin = $this->request->coin??0;
        if($coin <= 0)return error(['error' => '积分数不能小于0']);
        $user = $this->request->getUser();
        $userMoney = UserMoney::findByPassport($passport);
        if(!$userMoney->uepassport)$userMoney->umpassport = $passport;
        $userMoney->umamount = $userMoney->umamount + $coin;
        $userMoney->save();
        $data = [
            'uepassport' => $passport,
            'ueamount' => -1 * $coin,
            'uetime' => TIME,
            'ueuserid' => $user->userid,
            'uedescribe' => '管理员'.$user->username.'积分充值'
        ];
        $expense = UserExpense::fillWithInit($data);
        $expense->save();
        return [];
    }

    public function Data(): array | Error
    {
        $search = $this->request->search??null;
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $passport = $this->request->passport??null;
        if(!$passport)return error(['error' => '请选择用户']);
        $query = UserExpense::getQuery()
            ->where('uepassport' , $passport)
            ->orderBy('ueid', 'DESC');
        if($search['range']??false)
        {
            $query->where('uetime', '>=', strtotime($search['range'][0])??0);
            $query->where('uetime', '<=', strtotime($search['range'][1])??TIME);
        }
        $data = $query->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['uetime'] = date('Y-m-d H:i:s',$item['uetime']);
        });
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}