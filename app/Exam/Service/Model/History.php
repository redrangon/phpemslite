<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Examhistory 模型
 * 表名: x2_examhistory
 */
class History extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'exam_history'; // 表名
    protected static $primaryKeys = ['ehid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'ehplanid' => null,
        'ehpaperid' => 0,
        'ehexam' => null,
        'ehtype' => 0,
        'ehbasicid' => null,
        'ehtime' => 0,
        'ehscore' => 0,
        'ehpassport' => null,
        'ehstarttime' => 0,
        'ehendtime' => 0,
        'ehstatus' => 0,
        'ehdecide' => 0,
        'ehneedresit' => 0,
        'ehispass' => 0,
        'ehteacher' => "",
        'ehdecidetime' => 0,
        'ehstats' => "",
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
        'ehplanid' => 'required|integer',
        'ehpaperid' => 'required|integer',
        'ehexam' => 'required|string',
        'ehtype' => 'required|integer',
        'ehbasicid' => 'required|integer',
        'ehtime' => 'integer',
        'ehscore' => 'numeric',
        'ehpassport' => 'required|string',
        'ehstarttime' => 'integer',
        'ehendtime' => 'integer',
        'ehstatus' => 'integer',
        'ehdecide' => 'integer',
        'ehneedresit' => 'required|integer',
        'ehispass' => 'integer',
        'ehteacher' => 'string',
        'ehdecidetime' => 'integer',
        'ehstats' => 'string',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'ehplanid' => '请填写计划ID',
        'ehbasicid' => '请填写考场ID',
        'ehpassport' => '请填写身份证号',
    ];
}
