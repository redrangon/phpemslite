<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Favor 模型
 * 表名: x2_favor
 */
class Favor extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_favor';
    protected static $primaryKeys = ["favorid"];

    // ========== 常用查询方法 ==========

    /**
     * 根据主键列表查找
     * @param array $ids
     * @return array
     */
    public static function getByUserAndQuestionId(int $userId, int $questionId): array|null
    {
        return self::getQuery()->where('favoruserid', $userId)->where('favorquestionid', $questionId)->first();
    }

}
