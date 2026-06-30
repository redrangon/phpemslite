<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Examhistory 模型
 * 表名: x2_examhistory
 */
class HistoryDetail extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_history_detail';
    protected static $primaryKeys = ["ehdid"];

    public static function findByEhId($ehId): self
    {
        return new static(static::getQuery()->where('ehdehid', $ehId)->first()??[]);
    }

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'ehdscores' => "",
        'ehdsetting' => "",
        'ehdquestion' => "",
        'ehdanswer' => "",
        'ehdtimes' => "",
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
        'ehdehid' => 'integer',
        'ehdscores' => 'string',
        'ehdsetting' => 'string',
        'ehdquestion' => 'string',
        'ehdanswer' => 'string',
        'ehdtimes' => 'string',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
    ];

}
