<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * ExamhistoryLog 模型
 * 表名: x2_examhistory_log
 */
class HistoryLog extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_history_log';
    protected static $primaryKeys = ["ehlid"];

    protected static $defaultValues = [
        'ehlehid' => null,
        'ehlusername' => null,
        'ehltype' => null,
        'ehlinfo' => "",
        'ehltime' => 0
    ];

    protected static $rules = [
        'ehlehid' => 'required|integer',
        'ehlusername' => 'required|string',
        'ehltype' => 'integer',
        'ehlinfo' => 'string',
        'ehltime' => 'integer'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'ehlehid' => '请填写记录ID',
        'ehlusername' => '请填写用户名',
    ];
}
