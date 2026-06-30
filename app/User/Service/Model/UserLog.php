<?php

namespace PHPEMS\App\User\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * UserLog 模型
 * 表名: x2_user_log
 */
class UserLog extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'user_log';
    protected static $primaryKeys = ["ulid"];

    // ========== 表字段 ==========
    /**
     * ulid
     * @var int
     */
    public $ulid;

    /**
     * uluserid
     * @var int
     */
    public $uluserid;

    /**
     * ulip
     * @var string
     */
    public $ulip;

    /**
     * ulcliect
     * @var string
     */
    public $ulcliect;

    /**
     * ultime
     * @var int
     */
    public $ultime;

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
        return self::getQuery()->whereIn('ulid', $ids)->get();
    }

}
