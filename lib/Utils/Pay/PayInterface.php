<?php

namespace PHPEMS\Lib\Utils\Pay;

use PHPEMS\App\Trade\Service\Model\TradeOrder;

interface PayInterface
{
    public function app(TradeOrder $order);
    public function mobile(TradeOrder $order);
    public function web(TradeOrder $order);
    public function notifyCallback(callable $callback);
}