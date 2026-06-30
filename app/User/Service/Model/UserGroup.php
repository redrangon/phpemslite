<?php

namespace PHPEMS\App\User\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * UserGroup 模型
 * 表名: x2_user_group
 */
class UserGroup extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'user_group';
    protected static $primaryKeys = ["groupid"];

    // ========== 常用查询方法 ==========

    /**
     * 根据ID查找记录
     * @param mixed $id
     * @return self|null
     */
    public static function findById($id): ?self
    {
        return self::find($id);
    }

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
        return self::getQuery()->whereIn('groupid', $ids)->get();
    }

    public static function findDefaultGroup(): ?static
    {
        $result = static::getQuery()->where('groupdefault',1)->first();
        return new static($result);
    }

}
