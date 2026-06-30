<?php

namespace PHPEMS\App\Course\Service\Model;

use PHPEMS\Lib\Rules\Model;

class CourseSession extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'course_session'; // 表名
    protected static $primaryKeys = ['csnid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'csnpassport' => "",
        'csncsid' => 0,
        'csnfadtime' => 0,
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
        'csnpassport' => 'string',
        'csncsid' => 'integer',
        'csnfadtime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
    ];

    public static function findByPassport(string $passport): static
    {
        $query = static::getQuery();
        $result = $query->where('csnpassport', $passport)->first();
        return new static($result ?? []);
    }
}
