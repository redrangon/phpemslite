<?php

namespace PHPEMS\App\Course\Service\Model;

use PHPEMS\Lib\Rules\Model;

class CourseProgress extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'course_progress'; // 表名
    protected static $primaryKeys = ['cpid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'cppassport' => "",
        'cpcsid' => 0,
        'cpstatus' => 0,
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
        'cppassport' => 'string',
        'cpcsid' => 'integer',
        'cpstatus' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
    ];

    public static function findByPassportWithCourseId(string $passport, int $courseId): static
    {
        $query = static::getQuery();
        $result = $query->where('cppassport', $passport)
            ->where('cpcsid', $courseId)
            ->first();
        return new static($result??[]);
    }
}
