<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\Model\QuestionType;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Questype extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'all' => 'All',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add'
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
     * 添加操作
     */
    public function Add():array | Error
    {
        // 获取请求数据
        $data = [
            'questchoice' => $this->request->questchoice??null,
            'questsort' => $this->request->questsort??0,
            'questype' => $this->request->questype??null,
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = QuestionType::fillWithInit($data);
        $result = $model->toValidate();
        if($result !== true)return $result;
        if(!$model->save()) return error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        // TODO: 实现修改逻辑
        // 示例：
        $questid = $this->request->questid;
        $model = QuestionType::find($questid);
        if(!$model->questid) return error(['error' => '记录不存在']);
        $data = [
            'questchoice' => $this->request->questchoice??null,
            'questsort' => $this->request->questsort??0,
            'questype' => $this->request->questype??null,
        ];
        $data = array_filter($data,function($item){
            return !is_null($item);
        });
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
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
        QuestionType::getQuery()->whereIn('questid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function All():array
    {
        $data = QuestionType::getQuery()->get();
        $items = [];
        foreach ($data as $key => $value) {
            $items[$value['questid']] = $value;
        }
        return $items;
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = QuestionType::getQuery()->orderBy('questid', 'ASC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        return $query->paginate($page, $limit);
    }

    public function Index(): Error|array
    {
        return [];
    }
}
