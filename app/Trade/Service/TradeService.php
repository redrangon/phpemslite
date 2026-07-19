<?php

namespace PHPEMS\App\Trade\Service;

use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\App\User\Service\Model\UserMoney;

class TradeService
{
    static public function finishOrder(string $orderSn, string $payType = 'offLine'):bool
    {
        $order = TradeOrder::findByOrderSn($orderSn);
        if ($order->orderStatus == 1){
            $payType = match ($payType){
                'alipay' => 'alipay',
                'wechat' => 'wechat',
                default => 'offLine'
            };
            try{
                TradeOrder::getDB()->transaction(function () use($payType, $order){
                    $passport = $order->orderPassport;
                    $coin = intval($order->orderPrice * 10);
                    UserMoney::getQuery()->where('umpassport',$passport)->update([
                        'umamount' => function() use($coin){
                            return 'umamount + '.$coin;
                        }
                    ]);
                    $order->orderStatus = 2;
                    $order->orderPayType = $payType;
                    $order->save();
                    return true;
                });
            }catch (\Exception $e){
                return false;
            }
        }
        else return false;
    }
}