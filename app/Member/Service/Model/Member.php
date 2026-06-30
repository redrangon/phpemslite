<?php

namespace PHPEMS\App\Member\Service\Model;

use PHPEMS\Lib\Rules\Model;

class Member extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'member'; // 表名
    protected static $primaryKeys = ['mid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'mname' => "",
        'mphoto' => "",
        'mpassport' => null,
        'mphone' => '',
        'maddress' => '',
        'mpassportimg' => "",
        'msex' => "",
        'mbirthday' => "",
        'mpolitic' => "",
        'medu' => "",
        'munit' => "",
        'mcompany' => "",
        'mjobtime' => "",
        'mjob' => "",
        'mjobtitle' => "",
        'mteam' => "",
        'mtext' => "",
        'mresume' => "",
        'mtime' => 0,
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
        'mname' => 'string',
        'mphoto' => 'string',
        'mpassport' => 'required|string',
        'mphone' => 'string',
        'maddress' => 'string',
        'mpassportimg' => 'string',
        'msex' => 'string',
        'mbirthday' => 'string',
        'mpolitic' => 'string',
        'medu' => 'string',
        'munit' => 'string',
        'mcompany' => 'string',
        'mjobtime' => 'string',
        'mjob' => 'string',
        'mjobtitle' => 'string',
        'mteam' => 'string',
        'mtext' => 'string',
        'mresume' => 'string',
        'mtime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'mpassport' => '缺少身份证号'
    ];

    public static function findByPassport(string $passport): Member
    {
        $query = static::getQuery();
        $result = $query->where('mpassport',$passport)->first();
        return new static($result??[]);
    }
}
