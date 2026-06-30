<?php

namespace PHPEMS\App\Course\Controller\Master;

use PHPEMS\App\Course\Service\Model\CourseCategory;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Category extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'tree' => 'Tree',
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??static::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    /**
     * 添加操作
     */
    public function Add():array|Error
    {
        // 获取请求数据
        $data = [
            'catlite' => $this->request->catlite??0,
            'catname' => $this->request->catname??null,
            'catthumb' => $this->request->catthumb??"",
            'caturl' => $this->request->caturl??"",
            'catuseurl' => $this->request->catuseurl??"",
            'catparent' => $this->request->catparent??0,
            'catdes' => $this->request->catdes??""
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = CourseCategory::fillWithInit($data);
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
        $catid = $this->request->catid;
        $model = CourseCategory::find($catid);
        if(!$model->catid) return error(['error' => '记录不存在']);
        $data = [
            'catlite' => $this->request->catlite??null,
            'catname' => $this->request->catname??null,
            'catthumb' => $this->request->catthumb??null,
            'caturl' => $this->request->caturl??null,
            'catuseurl' => $this->request->catuseurl??null,
            'catparent' => $this->request->catparent??null,
            'catdes' => $this->request->catdes??null
        ];
        $data = array_filter($data,function($value){
            return !is_null($value);
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
        CourseCategory::getQuery()->whereIn('catid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Tree():array
    {
        return CourseCategory::getCategoryTree();
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = CourseCategory::getQuery()->orderBy('catlite', 'DESC')->orderBy('catid', 'DESC');
        $parentId = intval($this->request->parentid);
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['keyword']??false)$query->where('catname', 'like', '%'.$search['keyword'].'%');
        }
        $query->where('catparent', $parentId);
        $data = $query->paginate($page, $limit);
        return $data;
    }

    public function Index(): Error|array
    {
        $catId = $this->request->catid;
        return CourseCategory::find($catId)->getRaw();
    }
}
