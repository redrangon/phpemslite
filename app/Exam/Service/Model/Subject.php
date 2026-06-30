<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Subject 模型
 * 表名: x2_subject
 */
class Subject extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_subject';
    protected static $primaryKeys = ["subjectid"];

    protected static $defaultValues = [
        'subject' => null,
        'subjectsetting' => ''
    ];

    protected static $rules = [
        'subject' => 'required|string',
        'subjectsetting' => 'string'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'subject' => '请填写科目名',
        'subjectsetting' => '请设置题型',
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
        return self::getQuery()->whereIn('subjectid', $ids)->get();
    }

}
