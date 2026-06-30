<?php

namespace PHPEMS\App\Course\Service\Model;

use PHPEMS\Lib\Rules\Model;

class CourseLog extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'course_log'; // 表名
    protected static $primaryKeys = ['logid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'logpassport' => null,
        'logplanid' => 0,
        'logcourseid' => 0,
        'logtime' => 0,
        'logstatus' => 0,
        'logendtime' => 0,
        'logprogress' => 0,
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
        'logpassport' => 'required|string',
        'logplanid' => 'required|int',
        'logcourseid' => 'integer',
        'logtime' => 'integer',
        'logstatus' => 'integer',
        'logendtime' => 'integer',
        'logprogress' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'logpassport' => '请填写logpassport',
        'logplanid' => '请填写logplanid',
        'logcourseid' => '请填写logcourseid'
    ];

    public static function findByUserCourse(string $passport, int $courseid): static
    {
        $result = static::getQuery()->where('logpassport', '=', $passport)
            ->where('logcourseid', '=', $courseid)
            ->first();
        return new static($result??[]);
    }

    public static function findByUserCourses(string $passport, array $courses): array
    {
        $query = static::getQuery()->where('logpassport', '=', $passport);
        $query->whereIn('logcourseid', $courses);
        return $query->get();
    }
}
