<?php

namespace PHPEMS\App\Trade\Controller\Master;

use PHPEMS\App\Trade\Service\Model\TradeOrder;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\PayProvider;

class Order extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
            'pay' => 'Pay'
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
        $ids = $this->request->ids??null;
        if (empty($ids)) {
            return error(['error' => '未选择要支付的记录']);
        }
        foreach ($ids as $id) {
            $order = TradeOrder::findByOrderSn($id);
            if ($order->orderSn && $order->orderStatus == 1) {
                $order->orderPayType = 'offLine';
                $order->orderStatus = 2;
                $order->save();
            }
        }
        return ['msg' => '设置成功'];
    }

    public function Cancel():array|Error
    {
        $ids = $this->request->ids??null;
        if (empty($ids)) {
            return error(['error' => '未选择要取消的记录']);
        }
        foreach ($ids as $id) {
            $order = TradeOrder::findByOrderSn($id);
            if ($order->orderSn && $order->orderStatus != 2) {
                $order->orderStatus = 99;
                $order->save();
            }
        }
        return ['msg' => '取消成功'];
    }

    /**
     * 删除订单
     */
    public function Delete():array|Error
    {
        $ids = $this->request->ids??null;
        if (empty($ids)) {
            return error(['error' => '未选择要取消的记录']);
        }
        foreach ($ids as $id) {
            $order = TradeOrder::findByOrderSn($id);
            if ($order->orderSn && $order->orderStatus != 2) {
                $order->delete();
            }
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

            if ($search['ordersn'] ?? false) {
                $query->where("ordersn",$search['ordersn']);
            }

            if ($search['orderpassport'] ?? false) {
                $query->where("orderpassport",$search['orderpassport']);
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
        $ordersn = $this->request->ordersn;
        if ($ordersn) {
            $order = TradeOrder::find($ordersn);
            if (!$order || !$order->ordersn) {
                return error(['error' => '记录不存在']);
            }
            return $order->getRaw();
        }
        return [];
    }
}
