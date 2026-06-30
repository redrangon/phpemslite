<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Rules\Model;

class ExamPaper extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'exam_paper'; // 表名
    protected static $primaryKeys = ['examid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'examsubject' => 0,
        'exam' => "",
        'examtotalscore' => 0,
        'examtotaltime' => 0,
        'exampassmark' => 0,
        'examscalemodel' => 0,
        'examsetting' => "",
        'examquestions' => "",
        'examscore' => "",
        'examstatus' => 0,
        'examtype' => 0,
        'examauthorid' => 0,
        'examauthor' => "",
        'examtime' => 0,
        'examkeyword' => "",
        'examdecide' => 0,
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
        'examsubject' => 'required|integer',
        'exam' => 'required|string',
        'examtotalscore' => 'required|integer',
        'examtotaltime' => 'required|integer',
        'exampassmark' => 'required|numeric',
        'examscalemodel' => 'integer',
        'examsetting' => 'required|string',
        'examquestions' => 'string',
        'examscore' => 'string',
        'examstatus' => 'integer',
        'examtype' => 'integer',
        'examauthorid' => 'integer',
        'examauthor' => 'string',
        'examtime' => 'integer',
        'examkeyword' => 'string',
        'examdecide' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'examsetting' => '请填写examsetting',
        'examquestions' => '请填写examquestions',
        'examscore' => '请填写examscore',
    ];
}
