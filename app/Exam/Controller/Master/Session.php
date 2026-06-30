<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Session extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
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
    public function Add():array
    {
        // 获取请求数据
        $data = [];

        // TODO: 实现添加逻辑
        // 示例：
        // $model = Session::fill($data);
        // if(!$model->save()) return error(['error' => '添加失败']);

        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        // TODO: 实现修改逻辑
        // 示例：
        // $id = $this->request->id;
        // $model = Session::find($id);
        // if(!$model) return error(['error' => '记录不存在']);
        // $data = [];
        // foreach ($data as $key => $value) {
        //     $model->$key = $value;
        // }
        // if(!$model->save()) return error(['error' => '修改失败']);

        return ['msg' => '修改成功'];
    }

    /**
     * 删除操作
     */
    public function delete():array|Error
    {
        // TODO: 实现删除逻辑
        // 示例：
        // $ids = $this->request->ids;
        // if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        // Session::getQuery()->whereIn('id', $ids)->delete();

        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        // $query = Session::getQuery()->orderBy('id', 'DESC');
        // $page = $this->request->page ?? 1;
        // $limit = $this->request->limit ?? 20;
        // $search = $this->request->search;
        // if($search) {
        //     // 处理搜索条件
        // }
        // $data = $query->paginate($page, $limit);
        // return $data;

        return [];
    }

    public function Index(): Error|array
    {
        return [];
    }
}
