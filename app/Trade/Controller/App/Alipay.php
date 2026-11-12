<?php

namespace PHPEMS\App\Trade\Controller\App;

use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\App\Trade\Service\TradeService;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

use PHPEMS\Lib\Utils\PayProvider;
use Yansongda\Pay\Pay;

class Alipay extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = [];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'notify' => 'Notify',
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        return [];
    }

    static public function withOutFlows($action = 'index'):array
    {
        return [];
    }

    public function Notify():void
    {
        $pay = new PayProvider('alipay');
        try{
            $pay->notifyCallback(function ($data,$payType) {
                TradeService::finishOrder($payType);
            });
            exit('success');
        }catch (\Exception $e){
            exit('fail');
        }

    }

    /**
     * 获取单个订单详情
     */
    public function Index(): array
    {
        $pay = new PayProvider('alipay');
        $data = $pay->callBack();
        return [];
    }
}
