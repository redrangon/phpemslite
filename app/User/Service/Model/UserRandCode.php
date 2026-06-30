<?php

namespace PHPEMS\App\User\Service\Model;

use PHPEMS\Lib\Rules\Model;

class UserRandCode extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'user_rand_code'; // 表名
    protected static $primaryKeys = ['urcid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'urctarget' => null,
        'urctype' => null,
        'urcsendtime' => null,
        'urcstring' => null,
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
        'urctarget' => 'string|required',
        'urctype' => 'string|required',
        'urcsendtime' => 'integer|required',
        'urcstring' => 'string|required',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'urctarget' => '请输入发送对象',
        'urctype' => '请输入类型',
        'urcsendtime' => '请输入发送时间',
        'urcstring' => '请输入验证码值',
    ];

    public static function findByTargetAndType(string $target, string $type): ?static
    {
        $result = static::getQuery()->orderBy('urcid','desc')
            ->where('urctarget',$target)
            ->where('urctype',$type)
            ->first();
        return new static($result??[]);
    }
}
