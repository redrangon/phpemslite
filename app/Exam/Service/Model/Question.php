<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

class Question extends Model
{
    protected static $dbTable = 'exam_question'; // 表名
    protected static $primaryKeys = ['questionid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'questiontype' => 0,
        'question' => "",
        'questionuserid' => 0,
        'questionusername' => "",
        'questionlastmodifyuser' => "",
        'questionselect' => "",
        'questionselectnumber' => 0,
        'questionanswer' => "",
        'questiondescribe' => "",
        'questioncreatetime' => 0,
        'questionhtml' => "",
        'questionparent' => 0,
        'questionisparent' => 0,
        'questionchildnumber' => 1,
        'questionchildren' => "",
        'questionsequence' => 0,
        'questionlevel' => 1
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
        'questiontype' => 'required|integer',
        'question' => 'required|string',
        'questionuserid' => 'integer',
        'questionusername' => 'string',
        'questionlastmodifyuser' => 'string',
        'questionselect' => 'string',
        'questionselectnumber' => 'integer',
        'questionanswer' => 'string',
        'questiondescribe' => 'string',
        'questioncreatetime' => 'integer',
        'questionstatus' => 'integer',
        'questionhtml' => 'string',
        'questionparent' => 'integer',
        'questionisparent' => 'integer',
        'questionchildnumber' => 'integer',
        'questionchildren' => 'string',
        'questionsequence' => 'integer',
        'questionlevel' => 'required|integer',
        'questiondeler' => 'string',
        'questiondeltime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'question' => '请填写题干',
        'questiontype' => '请填写题型'
    ];
}
