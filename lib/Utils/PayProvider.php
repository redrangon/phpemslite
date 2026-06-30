<?php

namespace PHPEMS\Lib\Utils;

use PHPEMS\Lib\Utils\Pay\Alipay;
use PHPEMS\Lib\Utils\Pay\PayInterface;
use PHPEMS\Lib\Utils\Pay\Wechat;

class PayProvider
{
    public PayInterface $pay;
    public function __construct(string $payType,string $configName = 'default')
    {
        if($payType == 'wechatpay')$this->pay = new Wechat($configName);
        else $this->pay = new Alipay($configName);
    }

    public function __call(string $name, array $arguments)
    {
        if(method_exists($this->pay,$name))return $this->pay->$name(...$arguments);
        else return error(['错误的方法']);
    }
}