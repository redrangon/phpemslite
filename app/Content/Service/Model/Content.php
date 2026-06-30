<?php

namespace PHPEMS\App\Content\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Content 模型
 * 表名: content
 */
class Content extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'content';
    protected static $primaryKeys = ['contentid'];

    /**
     * 默认值（不含主键字段）
     */
    protected static $defaultValues = [
        'contentcatid' => 0,
        'contentmoduleid' => 0,
        'contentuserid' => 0,
        'contentusername' => '',
        'contentmodifier' => '',
        'contenttitle' => '',
        'contenttags' => '',
        'contentkeywords' => '',
        'contentthumb' => '',
        'contentlink' => '',
        'contentinputtime' => 0,
        'contentmodifytime' => 0,
        'contentsequence' => 0,
        'contentdescribe' => '',
        'contentinfo' => '',
        'contentstatus' => 0,
        'contenttemplate' => '',
        'contenttext' => '',
        'contentview' => null
    ];

    /**
     * 字段校验规则（不含主键字段）
     */
    protected static $rules = [
        'contentcatid' => 'integer',
        'contentmoduleid' => 'integer',
        'contentuserid' => 'integer',
        'contentusername' => 'string',
        'contentmodifier' => 'string',
        'contenttitle' => 'required|string',
        'contenttags' => 'string',
        'contentkeywords' => 'string',
        'contentthumb' => 'string',
        'contentlink' => 'string',
        'contentinputtime' => 'integer',
        'contentmodifytime' => 'integer',
        'contentsequence' => 'integer',
        'contentdescribe' => 'string',
        'contentinfo' => 'string',
        'contentstatus' => 'integer',
        'contenttemplate' => 'string',
        'contenttext' => 'string',
        'contentview' => 'integer'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'contenttitle' => '请填写内容标题'
    ];

    /**
     * 获取所有记录
     * @return array
     */
    public static function getAll(): array
    {
        return self::getQuery()->get();
    }

    /**
     * 根据分类ID获取内容列表
     * @param int $catid
     * @param int $status 状态
     * @return array
     */
    public static function getByCategory(int $catid, int $status = 0): array
    {
        $query = self::getQuery()->where('contentcatid', $catid);
        if ($status !== 0) {
            $query->where('contentstatus', $status);
        }
        return $query->orderBy('contentsequence', 'desc')->orderBy('contentid', 'desc')->get();
    }

    /**
     * 根据用户ID获取内容列表
     * @param int $userid
     * @return array
     */
    public static function getByUserId(int $userid): array
    {
        return self::getQuery()
            ->where('contentuserid', $userid)
            ->orderBy('contentinputtime', 'desc')
            ->get();
    }

    /**
     * 根据主键ID获取内容
     * @param int $contentid
     * @return array|null
     */
    public static function getById(int $contentid): ?array
    {
        return self::getQuery()->where('contentid', $contentid)->first();
    }

    /**
     * 根据主键列表查找
     * @param array $ids
     * @return array
     */
    public static function findByIds(array $ids): array
    {
        return self::getQuery()->whereIn('contentid', $ids)->get();
    }
}
