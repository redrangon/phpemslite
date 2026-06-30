<?php

namespace PHPEMS\App\Content\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * ContentCategory 模型
 * 表名: content_category
 */
class ContentCategory extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'content_category';
    protected static $primaryKeys = ['catid'];

    protected static $defaultValues = [
        'catlite' => 0,
        'catname' => "",
        'catthumb' => "",
        'caturl' => "",
        'catuseurl' => 0,
        'catparent' => 0,
        'catdes' => ""
    ];

    /**
     * 字段校验规则（不含主键字段）
     * required: 必填（NOT NULL 且无默认值的字段）
     * int: 整数类型
     * float: 浮点数类型
     * string: 字符串类型
     * date: 日期类型
     */
    protected static $rules = [
        'catlite' => 'integer',
        'catname' => 'required|string',
        'catthumb' => 'string',
        'caturl' => 'string',
        'catuseurl' => 'integer',
        'catparent' => 'integer',
        'catdes' => 'string'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'catname' => '请填写分类名称'
    ];

    private static function buildTree(array $data,int $parentid = 0):array
    {
        $tree = [];
        foreach ($data as $item) {
            if($item['catparent'] == $parentid){
                $tmp = [
                    'id' => $item['catid'],
                    'title' => $item['catname'],
                ];
                $children = self::buildTree($data,$item['catid']);
                if(!empty($children))
                {
                    $tmp['children'] = $children;
                }
                $tree[] = $tmp;
            }
        }
        return $tree;
    }

    public static function getCategoryTree():array
    {
        $categories = static::getQuery()->select(['catid','catname','catparent'])->orderBy('catlite','desc')->orderBy('catid','desc')->get();
        return self::buildTree($categories,0);
    }

    /**
     * 1. 获取目标节点下的【直接子节点】ID 数组
     * @param array $tree 树形数据
     * @param int $targetId 目标节点 ID
     * @return array
     */
    static public function getChildIds(array $tree, int $targetId): array
    {
        foreach ($tree as $node) {
            // 找到目标节点
            if ($node['id'] == $targetId) {
                // 判断是否存在 children 且不为空
                if (!empty($node['children']) && is_array($node['children'])) {
                    // 使用 array_column 快速提取子节点的 id (若字段为 catid，改为 'catid')
                    return array_column($node['children'], 'id');
                }
                return []; // 目标节点没有子节点
            }

            // 如果当前节点有 children，继续向下递归查找
            if (!empty($node['children']) && is_array($node['children'])) {
                $result = self::getChildIds($node['children'], $targetId);
                if (!empty($result)) {
                    return $result;
                }
            }
        }
        return []; // 树中不存在该 ID
    }

    /**
     * 2. 获取目标节点下的【所有子孙节点】ID 数组（包含多层嵌套）
     * @param array $tree 树形数据
     * @param int $targetId 目标节点 ID
     * @return array
     */
    static public function getAllDescendantIds(array $tree, int $targetId): array
    {
        foreach ($tree as $node) {
            if ($node['id'] == $targetId) {
                $result = [];
                // 定义内部递归函数，收集所有子孙节点
                $collect = function($children) use (&$collect, &$result) {
                    foreach ($children as $child) {
                        $result[] = $child['id']; // 若字段为 catid，改为 $child['catid']
                        if (!empty($child['children']) && is_array($child['children'])) {
                            $collect($child['children']);
                        }
                    }
                };

                if (!empty($node['children']) && is_array($node['children'])) {
                    $collect($node['children']);
                }
                return $result;
            }

            if (!empty($node['children']) && is_array($node['children'])) {
                $result = self::getAllDescendantIds($node['children'], $targetId);
                if (!empty($result)) {
                    return $result;
                }
            }
        }
        return [];
    }
}