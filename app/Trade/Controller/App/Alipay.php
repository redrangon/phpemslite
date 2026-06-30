<?php

namespace PHPEMS\App\Trade\Controller\App;

use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\App\Trade\Service\TradeService;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
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

    public function Notify()
    {
        $pay = new PayProvider('alipay');
        $data = $pay->notifyCallback(function ($data,$payType) {
            TradeService::finishOrder($payType);
        });
    }

    /**
     * 获取单个订单详情
     */
    public function Index(): Error|array
    {
        $pay = new PayProvider('alipay');
        $data = $pay->callBack();
        return [];
    }
}
