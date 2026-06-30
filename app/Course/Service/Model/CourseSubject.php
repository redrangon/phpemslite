<?php

namespace PHPEMS\App\Course\Service\Model;

use PHPEMS\Lib\Rules\Model;

class CourseSubject extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'course_subject'; // 表名
    protected static $primaryKeys = ['csid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'cstitle' => "",
        'cscatid' => 0,
        'csuserid' => 0,
        'cstime' => 0,
        'csthumb' => "",
        'cssequence' => 0,
        'csdescribe' => "",
        'cstype' => 0,
        'csprogress' => 0,
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
        'cstitle' => 'required|string',
        'cscatid' => 'integer',
        'csuserid' => 'integer',
        'cstime' => 'integer',
        'csthumb' => 'string',
        'cssequence' => 'integer',
        'csdescribe' => 'string',
        'cstype' => 'integer',
        'csprogress' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'cstitle' => '课程名称必须填写'
    ];
}
