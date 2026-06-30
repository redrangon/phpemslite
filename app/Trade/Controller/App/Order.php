<?php

namespace PHPEMS\App\Trade\Controller\App;

use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\Lib\Config\Pay\Wechat;
use PHPEMS\Lib\Config\Pay\Alipay;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Env;
use PHPEMS\Lib\Utils\PayProvider;

class Order extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'cancel' => 'Cancel',
            'pay' => 'Pay',
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

    public function Pay():array|Error
    {
        $orderSn = $this->request->orderSn??null;
        $type = $this->request->type??null;
        if(!in_array($type,['wechatpay','alipay'])){
            return error(['error' => '选择支付方式']);
        }
        $order = TradeOrder::findByOrderSn($orderSn);
        if ($order->orderSn) {
            $user = $this->request->getUser();
            if ($order->orderPassport == $user->userpassport) {
                $pay = new PayProvider($type);
                $ua = Env::getPaymentEnv();
                if($ua == 'pc')
                {
                    return $pay->web($order);
                }
                elseif($ua == 'wechat')
                {
                    return $pay->app($order);
                }
                elseif($ua == 'mobile')
                {
                    return $pay->mobile($order);
                }
                else return error(['error' => '错误的支付环境']);
            }
            else return error(['error' => '订单记录不存在']);
        }
        else return error(['error' => '订单记录不存在']);
    }

    /**
     * 删除订单
     */
    public function Cancel():array|Error
    {
        $orderSn = $this->request->orderSn??null;
        if (!$orderSn) {
            return error(['error' => '未选择要取消的记录']);
        }
        $order = TradeOrder::findByOrderSn($orderSn);
        $user = $this->request->getUser();
        if($order->orderPassport == $user->userpassport)
        {
            $order->orderStatus = 99;
            $order->save();
        }
        return ['msg' => '取消成功'];
    }

    /**
     * 获取订单列表
     */
    public function Data(): array
    {
        $query = TradeOrder::getQuery()->orderBy('ordercreatetime', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        $user = $this->request->getUser();
        $query->where("orderpassport",$user->userpassport);
        if ($search) {
            // 关键词搜索（订单号、订单标题）
            if ($search['keyword'] ?? false) {
                $query->where(function($q) use ($search) {
                    $q->where('ordersn', 'like', '%' . $search['keyword'] . '%')
                      ->orWhere('ordertitle', 'like', '%' . $search['keyword'] . '%');
                });
            }
            
            // 订单状态筛选
            if (isset($search['orderstatus']) && $search['orderstatus'] !== '') {
                $query->where('orderstatus', $search['orderstatus']);
            }
            
            // 支付方式筛选
            if ($search['orderpaytype'] ?? false) {
                $query->where('orderpaytype', $search['orderpaytype']);
            }
            
            // 应用来源筛选
            if ($search['orderapp'] ?? false) {
                $query->where('orderapp', $search['orderapp']);
            }

            if ($search['range'] ?? false)
            {
                // 时间范围筛选
                if ($search['range'][0] ?? false) {
                    $query->where('ordercreatetime', '>=', strtotime($search['range'][0]));
                }
                if ($search['range'][1] ?? false) {
                    $query->where('ordercreatetime', '<=', strtotime($search['range'][1]));
                }
            }

        }
        
        $data = $query->paginate($page, $limit);
        
        // 格式化时间字段
        array_walk($data['data'], function (&$item) {
            $item['ordercreatetime'] = $item['ordercreatetime'] ? date('Y-m-d H:i:s', $item['ordercreatetime']) : '';
            $item['orderpaytime'] = $item['orderpaytime'] ? date('Y-m-d H:i:s', $item['orderpaytime']) : '';
            $item['orderfinishtime'] = $item['orderfinishtime'] ? date('Y-m-d H:i:s', $item['orderfinishtime']) : '';
        });
        
        return $data;
    }

    /**
     * 获取单个订单详情
     */
    public function Index(): Error|array
    {
        $orderSn = $this->request->orderSn??null;
        if ($orderSn) {
            $order = TradeOrder::findByOrderSn($orderSn);
            if ($order->orderSn) {
                $user = $this->request->getUser();
                if($order->orderPassport == $user->userpassport) {
                    $order->orderCreateTime = date('Y-m-d H:i:s', $order->orderCreateTime);
                    return [
                        'order' => $order->getRaw(),
                        'info' => [
                            'wechatPay' => (new Wechat())->enable,
                            'aliPay' => (new Alipay())->enable
                        ]
                    ];
                }
                else return error(['error' => '记录不存在']);
            }
            else return error(['error' => '记录不存在']);
        }
        return [];
    }
}
