<?php

namespace PHPEMS\App\Trade\Service\Model;

use PHPEMS\Lib\Rules\Model;

class TradeOrder extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'trade_order'; // 表名
    protected static $primaryKeys = ['orderid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'ordersn' => null,
        'ordertitle' => "",
        'orderpassport' => "",
        'orderitems' => "",
        'orderprice' => 0,
        'orderstatus' => 0,
        'orderrawprice' => 0,
        'ordercreatetime' => 0,
        'orderpaytime' => 0,
        'orderpaytype' => "",
        'orderfinishtime' => 0,
        'orderfaq' => "",
        'orderdescribe' => "",
    ];

    /**
     * 字段校验规则（不含主键字段）
     * required: 必填（NOT NULL 且无默认值的字段）
     * integer: 整数类型
     * float: 浮点数类型
     * string: 字符串类型
     * date: 日期类型
     */
    protected static $rules = [
        'ordersn' => 'required|string',
        'ordertitle' => 'required|string',
        'orderpassport' => 'required|string',
        'orderitems' => 'required|string',
        'orderprice' => 'required|numeric',
        'orderstatus' => 'required|integer',
        'orderrawprice' => 'required|numeric',
        'ordercreatetime' => 'required|integer',
        'orderpaytime' => 'required|integer',
        'orderpaytype' => 'string',
        'orderfinishtime' => 'required|integer',
        'orderfaq' => 'string',
        'orderdescribe' => 'string',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'ordersn' => '请填写ordersn',
        'ordertitle' => '请填写ordertitle',
        'orderpassport' => '请填写orderpassport',
        'orderitems' => '请填写orderitems',
        'orderprice' => '请填写orderprice',
        'orderstatus' => '请填写orderstatus',
        'orderrawprice' => '请填写orderrawprice',
        'ordercreatetime' => '请填写ordercreatetime',
        'orderpaytime' => '请填写orderpaytime',
        'orderfinishtime' => '请填写orderfinishtime',
        'orderfaq' => '请填写orderfaq',
        'orderdescribe' => '请填写orderdescribe',
    ];

    static public function findByOrderSn(string $orderSn):static
    {
        $query = static::getQuery();
        $result = $query->where('ordersn', $orderSn)
            ->first();
        return new static($result??[]);
    }
}
