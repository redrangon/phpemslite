<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

class Point extends Model
{
    protected static $dbTable = 'exam_point'; // 表名
    protected static $primaryKeys = ['pointid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'point' => "",
        'pointsectionid' => 0,
        'pointdescribe' => "",
        'pointstatus' => 0,
        'pointsequence' => 0,
        'pointnumber' => "",
        'pointquestions' => "",
    ];

    /**
     * 字段校验规则（不含主键字段）
     * required: 必填（NOT NULL 且无默认值的字段）
     * int: 整数类型
     * float: 浮点数类型
     * string: 字符串类型
     * date: 日期类型
     */
    protected static $rules = [
        'point' => 'required|string',
        'pointsectionid' => 'integer',
        'pointdescribe' => 'string',
        'pointstatus' => 'integer',
        'pointsequence' => 'integer',
        'pointnumber' => 'string',
        'pointquestions' => 'string',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'pointdescribe' => '请填写pointdescribe',
        'pointsequence' => '请填写pointsequence',
        'pointnumber' => '请填写pointnumber',
        'pointquestions' => '请填写pointquestions',
    ];
}
