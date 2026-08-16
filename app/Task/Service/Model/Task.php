<?php

namespace PHPEMS\App\Task\Service\Model;

use PHPEMS\Lib\Rules\Model;

class Task extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'task'; // 表名
    protected static $primaryKeys = ['taskid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'tasksid' => 0,
        'taskname' => "",
        'taskclass' => "",
        'taskpayload' => "",
        'tasktype' => "",
        'taskmaxattempts' => 0,
        'tasktimeout' => 0,
        'taskattempts' => 0,
        'taskstatus' => "",
        'taskscheduletime' => 0,
        'tasklocktime' => 0,
        'taskresult' => 0,
        'taskinfo' => "",
        'taskstarttime' => 0,
        'taskfinishtime' => 0,
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
        'tasksid' => 'integer',
        'taskname' => 'required|string',
        'taskclass' => 'required|string',
        'taskpayload' => 'required',
        'tasktype' => 'string',
        'taskmaxattempts' => 'integer',
        'tasktimeout' => 'integer',
        'taskattempts' => 'integer',
        'taskstatus' => 'string',
        'taskscheduletime' => 'integer',
        'tasklocktime' => 'integer',
        'taskresult' => 'integer',
        'taskinfo' => 'string',
        'taskstarttime' => 'integer',
        'taskfinishtime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'taskname' => '请填写taskname',
        'taskclass' => '请填写taskclass',
        'taskpayload' => '请填写taskpayload',
    ];
}
