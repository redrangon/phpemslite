<?php

namespace PHPEMS\App\Exam\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * FeedBack 模型
 * 表名: x2_exam_feedback
 * 
 * 字段说明:
 * - fbid: 反馈ID（主键，自增）
 * - fbquestionid: 问题ID
 * - fbtype: 反馈类型
 * - fbcontent: 反馈内容
 * - fbuserid: 反馈用户ID
 * - fbtime: 反馈时间
 * - fbstatus: 反馈状态
 * - fbdoneuserid: 处理用户ID
 * - fbdonetime: 处理时间
 */
class FeedBack extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'exam_feedback';
    protected static $primaryKeys = ["fbid"];

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     */
    protected static $defaultValues = [
        'fbquestionid' => 0,
        'fbtype' => '',
        'fbcontent' => '',
        'fbuserid' => 0,
        'fbtime' => 0,
        'fbstatus' => 0,
        'fbdoneuserid' => 0,
        'fbdonetime' => 0
    ];

    /**
     * 字段校验规则（不含主键字段）
     * required: 必填字段
     * integer: 整数类型
     * string: 字符串类型
     */
    protected static $rules = [
        'fbquestionid' => 'required|integer',
        'fbtype' => 'required|string',
        'fbcontent' => 'required|string',
        'fbuserid' => 'required|integer',
        'fbtime' => 'integer',
        'fbstatus' => 'integer',
        'fbdoneuserid' => 'integer',
        'fbdonetime' => 'integer'
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'fbquestionid.required' => '请选择问题',
        'fbtype.required' => '请选择反馈类型',
        'fbcontent.required' => '请填写反馈内容',
        'fbuserid.required' => '用户ID不能为空'
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
        return self::getQuery()->whereIn('fbid', $ids)->get();
    }

    /**
     * 根据问题ID查找反馈列表
     * @param int $questionId
     * @return array
     */
    public static function findByQuestionId(int $questionId): array
    {
        return self::getQuery()->where('fbquestionid', $questionId)->orderBy('fbtime', 'DESC')->get();
    }

    /**
     * 根据用户ID查找反馈列表
     * @param int $userId
     * @return array
     */
    public static function findByUserId(int $userId): array
    {
        return self::getQuery()->where('fbuserid', $userId)->orderBy('fbtime', 'DESC')->get();
    }

    /**
     * 根据反馈类型查找列表
     * @param string $type
     * @return array
     */
    public static function findByType(string $type): array
    {
        return self::getQuery()->where('fbtype', $type)->orderBy('fbtime', 'DESC')->get();
    }

    /**
     * 根据状态查找反馈列表
     * @param int $status
     * @return array
     */
    public static function findByStatus(int $status): array
    {
        return self::getQuery()->where('fbstatus', $status)->orderBy('fbtime', 'DESC')->get();
    }

    /**
     * 获取未处理的反馈列表
     * @return array
     */
    public static function getUnprocessed(): array
    {
        return self::getQuery()->where('fbstatus', 0)->orderBy('fbtime', 'ASC')->get();
    }

    /**
     * 获取已处理的反馈列表
     * @return array
     */
    public static function getProcessed(): array
    {
        return self::getQuery()->where('fbstatus', 1)->orderBy('fbdonetime', 'DESC')->get();
    }

    /**
     * 分页获取反馈列表
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @param array $conditions 查询条件
     * @return array
     */
    public static function getListByPage(int $page = 1, int $pageSize = 20, array $conditions = []): array
    {
        $query = self::getQuery();
        
        if (!empty($conditions['fbuserid'])) {
            $query->where('fbuserid', $conditions['fbuserid']);
        }
        
        if (!empty($conditions['fbquestionid'])) {
            $query->where('fbquestionid', $conditions['fbquestionid']);
        }
        
        if (!empty($conditions['fbtype'])) {
            $query->where('fbtype', $conditions['fbtype']);
        }
        
        if (isset($conditions['fbstatus']) && $conditions['fbstatus'] !== '') {
            $query->where('fbstatus', $conditions['fbstatus']);
        }
        
        $query->orderBy('fbid', 'DESC');
        
        return $query->paginate($page, $pageSize);
    }

    /**
     * 统计反馈数量
     * @param array $conditions 查询条件
     * @return int
     */
    public static function countByConditions(array $conditions = []): int
    {
        $query = self::getQuery();
        
        if (!empty($conditions['fbuserid'])) {
            $query->where('fbuserid', $conditions['fbuserid']);
        }
        
        if (!empty($conditions['fbquestionid'])) {
            $query->where('fbquestionid', $conditions['fbquestionid']);
        }
        
        if (!empty($conditions['fbtype'])) {
            $query->where('fbtype', $conditions['fbtype']);
        }
        
        if (isset($conditions['fbstatus']) && $conditions['fbstatus'] !== '') {
            $query->where('fbstatus', $conditions['fbstatus']);
        }
        
        return $query->count();
    }
}
