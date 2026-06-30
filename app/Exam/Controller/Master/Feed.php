<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\Model\FeedBack;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Feed extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'sign' => 'Sign',
            'delete' => 'Delete'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??self::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    /**
     * 修改操作
     */
    public function Sign():array|Error
    {
        // TODO: 实现修改逻辑
        // 示例：
        $ids = $this->request->ids??null;
        if(!$ids)return error(['error' => '参数错误']);
        FeedBack::getQuery()->whereIn('fbid', $ids)->update([
            'fbstatus' => 1,
            'fbdonetime' => TIME,
            'fbdoneuserid' => $this->request->getUser()->userid,
        ]);
        return ['msg' => '标记成功'];
    }

    /**
     * 删除操作
     */
    public function delete():array|Error
    {
        // TODO: 实现删除逻辑
        // 示例：
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        FeedBack::getQuery()->whereIn('fbid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array|Error
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = FeedBack::getQuery()->orderBy('fbid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search??null;
        if($search) {
            if($search['fbquestionid']??false)$query->where('fbquestionid', $search['fbquestionid']);
        }
        $data = $query->paginate($page, $limit);
        array_walk($data['data'], function (&$item) {
            $item['fbtime'] = $item['fbtime']?date('Y-m-d H:i:s', $item['fbtime']):'';
            $item['fbdonetime'] = $item['fbdonetime']?date('Y-m-d H:i:s', $item['fbdonetime']):'';
        });
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
