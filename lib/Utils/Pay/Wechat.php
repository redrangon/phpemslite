<?php

namespace PHPEMS\Lib\Utils\Pay;


use Exception;
use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\Lib\Config\Site\Site;
use Throwable;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Pay\Pay;

class Wechat implements PayInterface
{
    protected array $config = [];
    protected string $openId;

    public function __construct($configName = 'default')
    {
        $config = new \PHPEMS\Lib\Config\Pay\Wechat($configName);
        $config->account['notify_url'] = Site::getConfig('baseUri') . '/index.php/trade/app/wechat/notify';
        $this->config = [
            'wechat' => ['default' => $config->account],
            'logger' => $config->logger,
            'http' => $config->http
        ];
    }

    public function setOpenId(string $openId):void
    {
        $this->openId = $openId;
    }

    public function app(TradeOrder $order)
    {
        Pay::config($this->config);
        $order = [
            'out_trade_no' => $order->ordersn,
            'description' => $order->ordertitle,
            'amount' => [
                'total' => 100 * $order->orderprice,
            ],
            'payer' => [
                'openid' => 'onkVf1FjWS5SBxxxxxxxx',
            ],
        ];
        $pay = Pay::wechat()->app($order);
        return $pay->toArray();
        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function mobile(TradeOrder $order)
    {
        Pay::config($this->config);
        $order = [
            'out_trade_no' => $order->ordersn,
            'description' => $order->ordertitle,
            'amount' => [
                'total' => 100 * $order->orderprice,
            ],
            'payer' => [
                'openid' => 'onkVf1FjWS5SBxxxxxxxx',
            ],
        ];
        $pay = Pay::wechat()->h5($order);
        return $pay->toArray();
        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function native(TradeOrder $order)
    {
        Pay::config($this->config);
        $order = [
            'out_trade_no' => $order->ordersn,
            'description' => $order->ordertitle,
            'amount' => [
                'total' => 100 * $order->orderprice,
            ],
            'payer' => [
                'openid' => 'onkVf1FjWS5SBxxxxxxxx',
            ],
        ];
        $pay = Pay::wechat()->mp($order);
        return $pay->toArray();
        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function web(TradeOrder $order)
    {
        Pay::config($this->config);
        $order = [
            'out_trade_no' => $order->ordersn,
            'description' => $order->ordertitle,
            'amount' => [
                'total' => 100 * $order->orderprice,
            ],
            'payer' => [
                'openid' => 'onkVf1FjWS5SBxxxxxxxx',
            ],
        ];
        $pay = Pay::wechat()->scan($order);
        return $pay->toArray();
        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function mini(TradeOrder $order)
    {
        Pay::config($this->config);
        $order = [
            'out_trade_no' => $order->ordersn,
            'description' => $order->ordertitle,
            'amount' => [
                'total' => 100 * $order->orderprice,
            ],
            'payer' => [
                'openid' => 'onkVf1FjWS5SBxxxxxxxx',
            ],
        ];
        $pay = Pay::wechat()->mini($order);
        return $pay->toArray();
        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    /**
     * @throws ContainerException
     */
    public function notifyCallback(callable $callback)
    {
        Pay::config($this->config);
        try{
            $data = Pay::wechat()->callback(); // 是的，验签就这么简单！
            if($data->trade_state == 'SUCCESS'){
                $callback($data->out_trade_no,'wechat');
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return Pay::wechat()->success();
    }
}