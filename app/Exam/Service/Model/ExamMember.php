<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

class ExamMember extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'exam_member'; // 表名
    protected static $primaryKeys = ['emid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'empassport' => "",
        'embasicid' => 0,
        'emstarttime' => 0,
        'emendtime' => 0,
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
        'empassport' => 'string',
        'embasicid' => 'integer',
        'emstarttime' => 'integer',
        'emendtime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
    ];

    static public function findByPassportAndSubjectIdWithTime(string $passport, int $basicId):static
    {
        $query = static::getQuery();
        $result = $query->where('empassport', $passport)
            ->where('embasicid',$basicId)
            ->where('emendtime','>',TIME)
            ->first();
        return new static($result ?? []);
    }

    static public function findByPassportAndSubjectId(string $passport, int $basicId):static
    {
        $query = static::getQuery();
        $result = $query->where('empassport', $passport)
            ->where('embasicid',$basicId)
            ->first();
        return new static($result ?? []);
    }
}
