<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Basic 模型
 * 表名: x2_basic
 */
class Basic extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_basic';
    protected static $primaryKeys = ["basicid"];

    protected static $defaultValues = [
        'basic' => null,
        'basicsubjectid' => null,
        'basicnumber' => 0,
        'basicpoint' => "",
        'basicexam' => "",
        'basicthumb' => "",
        'basicdescribe' => "",
        'basictext' => ''
    ];

    protected static $rules = [
        'basic' => 'required|string',
        'basicsubjectid' => 'required|integer',
        'basicnumber' => 'integer',
        'basicpoint' => 'string',
        'basicexam' => 'string',
        'basicthumb' => 'string',
        'basicdescribe' => 'string',
        'basictext' => 'string'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'basic' => '请填写考场名',
        'basicsubjectid' => '请填写科目名',
    ];

    // ========== 常用查询方法 ==========
    /**
     * 获取所有记录
     * @return array
     */
    public static function getAll(): array
    {
        return self::getQuery()->get();
    }

    /**
     * 根据主键列表查找
     * @param array $ids
     * @return array
     */
    public static function findByIds(array $ids): array
    {
        return self::getQuery()->whereIn('basicid', $ids)->get();
    }

}
