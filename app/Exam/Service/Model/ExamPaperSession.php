<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

class ExamPaperSession extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'exam_paper_session'; // 表名
    protected static $primaryKeys = ['esid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'examsessionid' => "",
        'examsessionplanid' => 0,
        'examsessionpassport' => "",
        'examsession' => "",
        'examsessionsetting' => "",
        'examsessionsign' => "",
        'examsessionbasicid' => 0,
        'examsessiontype' => 0,
        'examsessionpaperid' => 0,
        'examsessionquestion' => "",
        'examsessionuseranswer' => "",
        'examsessionstarttime' => 0,
        'examsessiontime' => 0,
        'examsessiontimelist' => "",
        'examsessiontoken' => "",
        'examsessionfacelist' => "",
        'examsessionip' => "",
        'examsessionclient' => "",
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
        'examsessionid' => 'required|string',
        'examsessionplanid' => 'required|integer',
        'examsessionpassport' => 'string',
        'examsession' => 'string',
        'examsessionsetting' => 'required|string',
        'examsessionsign' => 'string',
        'examsessionbasicid' => 'integer',
        'examsessiontype' => 'required|integer',
        'examsessionpaperid' => 'required|integer',
        'examsessionquestion' => 'required|string',
        'examsessionuseranswer' => 'string',
        'examsessionstarttime' => 'integer',
        'examsessiontime' => 'integer',
        'examsessiontimelist' => 'string',
        'examsessiontoken' => 'string',
        'examsessionfacelist' => "string",
        'examsessionip' => "string",
        'examsessionclient' => "string",
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'examsessionid' => '请填写examsessionid',
        'examsessionplanid' => '请填写examsessionplanid',
        'examsessionsetting' => '请填写examsessionsetting',
        'examsessionsign' => '请填写examsessionsign',
        'examsessiontype' => '请填写examsessiontype',
        'examsessionpaperid' => '请填写examsessionpaperid',
        'examsessionquestion' => '请填写examsessionquestion',
        'examsessionuseranswer' => '请填写examsessionuseranswer',
        'examsessiontimelist' => '请填写examsessiontimelist',
    ];

    static public function findBySessionId($sessionId)
    {
        $query = static::getQuery();
        $result = $query->where('examsessionid', $sessionId)->first();
        return new static($result??[]);
    }
}
