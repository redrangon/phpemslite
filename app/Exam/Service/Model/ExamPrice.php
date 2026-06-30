<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

class ExamPrice extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'exam_price'; // 表名
    protected static $primaryKeys = ['epid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'epbasicid' => 0,
        'epdays' => 0,
        'epamount' => 0,
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
        'epbasicid' => 'integer',
        'epdays' => 'integer',
        'epamount' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
    ];

    public static function findByBasicId($basicId): array
    {
        return self::getQuery()->where('epbasicid', $basicId)->get();
    }
}
