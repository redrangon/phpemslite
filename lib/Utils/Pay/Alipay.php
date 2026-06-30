<?php

namespace PHPEMS\Lib\Utils\Pay;


use Exception;
use GuzzleHttp\Psr7\Response;
use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\Lib\Config\Site\Site;
use Throwable;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Pay\Pay;

class Alipay implements PayInterface
{
    protected array $config = [];

    public function __construct($configName = 'default')
    {
        $config = new \PHPEMS\Lib\Config\Pay\Alipay($configName);
        $config->account['return_url'] = Site::getConfig('baseUri') . '/index.php/trade/app/alipay';
        $config->account['notify_url'] = Site::getConfig('baseUri') . '/index.php/trade/app/alipay/notify';
        $this->config = [
            'alipay' => [
                'default' => $config->account
            ],
            'logger' => $config->logger,
            'http' => $config->http
        ];
    }

    public function app(TradeOrder $order)
    {
        Pay::config($this->config);
        $result = Pay::alipay()->app([
            'out_trade_no' => $order->ordersn,
            'total_amount' => $order->orderprice,
            'subject' => $order->ordertitle,
        ]);
        return $result;
    }

    public function mini(TradeOrder $order)
    {
        Pay::config($this->config);
        $result = Pay::alipay()->mini([
            'out_trade_no' => $order->ordersn,
            'total_amount' => $order->orderprice,
            'subject' => $order->ordertitle,
        ]);
        return $result;
    }

    public function mobile(TradeOrder $order) : array
    {
        Pay::config($this->config);
        $result = Pay::alipay()->h5([
            'out_trade_no' => $order->ordersn,
            'total_amount' => $order->orderprice,
            'subject' => $order->ordertitle
        ]);
        return ['data' => $result->getBody()->getContents()];
    }

    public function web(TradeOrder $order) : array
    {
        Pay::config($this->config);
        $result = Pay::alipay()->web([
            'out_trade_no' => $order->ordersn,
            'total_amount' => $order->orderprice,
            'subject' => $order->ordertitle,
            '_method' => 'get',
        ]);
        return ['data' => $result->getBody()->getContents()];
    }

    public function callBack():array
    {
        Pay::config($this->config);
        $data = Pay::alipay()->callback(); // 是的，验签就这么简单！
        return $data->toArray();
    }

    /**
     * @throws ContainerException
     */
    public function notifyCallback(callable $callback):Response
    {
        Pay::config($this->config);
        try{
            $data = Pay::alipay()->callback(); // 是的，验签就这么简单！
            if($data->trade_status == 'TRADE_FINISHED' || $data->trade_status == 'TRADE_SUCCESS'){
                $callback($data->out_trade_no,'alipay');
            }
            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
        return Pay::alipay()->success();
    }
}