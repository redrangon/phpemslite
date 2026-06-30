<?php

namespace PHPEMS\App\Cert\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Cert 模型
 * 表名: cert
 */
class Cert extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'cert';
    protected static $primaryKeys = ['ceid'];

    /**
     * 默认值（不含主键字段）
     */
    protected static $defaultValues = [
        'cetitle' => '',
        'ceicon' => '',
        'cethumb' => '',
        'cedays' => 0,
        'cetime' => 0,
        'cetpl' => '',
        'cetags' => '',
        'cedescribe' => '',
        'cetext' => '',
    ];

    /**
     * 字段校验规则（不含主键字段）
     */
    protected static $rules = [
        'cetitle' => 'required|string',
        'ceicon' => 'string',
        'cethumb' => 'string',
        'cedays' => 'integer',
        'cetime' => 'integer',
        'cetpl' => 'string',
        'cetags' => 'string',
        'cedescribe' => 'string',
        'cetext' => 'string',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'cetitle' => '请填写证书标题'
    ];
}