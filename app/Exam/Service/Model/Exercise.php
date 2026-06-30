<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Exercise 模型
 * 表名: x2_exercise
 */
class Exercise extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_exercise';
    protected static $primaryKeys = ["exerid"];

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
        return self::getQuery()->whereIn('exerid', $ids)->get();
    }

}
