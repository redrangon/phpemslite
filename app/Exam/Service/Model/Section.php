<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Sections 模型
 * 表名: x2_sections
 */
class Section extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_section';
    protected static $primaryKeys = ["sectionid"];

    protected static $defaultValues = [
        'section' => null,
        'sectionsubjectid' => null,
        'sectiondescribe' => '',
        'sectionsequence' => 0,
    ];

    protected static $rules = [
        'section' => 'required|string',
        'sectionsubjectid' => 'required|integer'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'section' => '请填写章节名',
        'sectionsubjectid' => '请设置科目ID',
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
        return self::getQuery()->whereIn('sectionid', $ids)->get();
    }

}
