<?php

namespace PHPEMS\App\Cert\Service\Model;

use PHPEMS\Lib\Rules\Model;

class CertMember extends Model
{
    protected static $dbName = 'mysql.default'; // 连接名
    protected static $dbTable = 'cert_member'; // 表名
    protected static $primaryKeys = ['cemid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'cemceid' => 0,
        'cempassport' => "",
        'cemtime' => 0,
        'cemstatus' => 0,
        'cemsn' => "",
        'cemexpirytime' => 0,
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
        'cemceid' => 'required|integer',
        'cempassport' => 'required|string',
        'cemtime' => 'required|integer',
        'cemstatus' => 'required|integer',
        'cemsn' => 'string',
        'cemexpirytime' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'cemceid' => '请填写cemceid',
        'cempassport' => '请填写cempassport',
        'cemtime' => '请填写cemtime',
        'cemstatus' => '请填写cemstatus',
    ];
}
