<?php

namespace PHPEMS\App\Task\Service\Model;

use PHPEMS\Lib\Rules\Model;

class TaskSchedules extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'task_schedules'; // 表名
    protected static $primaryKeys = ['tsid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'tsname' => "",
        'tsdescribe' => "",
        'tsuserid' => 0,
        'tsclass' => "",
        'tspayload' => "",
        'tstype' => "",
        'tspriority' => 0,
        'tsattempts' => 0,
        'tsloop' => "",
        'tstimes' => 0,
        'tsenabled' => 0,
        'tslastrun' => "",
        'tsnextrun' => "",
        'tstime' => "",
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
        'tsname' => 'required|string',
        'tsdescribe' => 'string',
        'tsuserid' => 'integer',
        'tsclass' => 'required|string',
        'tspayload' => 'string',
        'tstype' => 'string',
        'tspriority' => 'integer',
        'tsattempts' => 'integer',
        'tsloop' => 'required|string',
        'tstimes' => 'integer',
        'tsenabled' => 'integer',
        'tslastrun' => 'integer',
        'tsnextrun' => 'integer',
        'tstime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'tsname' => '请填写tsname',
        'tsclass' => '请填写tsclass',
        'tspayload' => '请填写tspayload',
        'tsloop' => '请填写tsloop',
    ];
}
