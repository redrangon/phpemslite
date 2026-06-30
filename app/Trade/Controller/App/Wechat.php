<?php

namespace PHPEMS\App\Trade\Controller\App;

use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\PayProvider;
use Yansongda\Pay\Pay;

class Wechat extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = [];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'notify' => 'Index',
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

    /**
     * 获取单个订单详情
     */
    public function Index(): Error|array
    {
        return [];
    }
}
