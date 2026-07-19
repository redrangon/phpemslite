<?php

namespace PHPEMS\App\Course\Controller\Master;

use PHPEMS\App\Course\Service\Model\CourseCategory;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Subject extends Controller implements ControllerInterface
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
        $user = $this->request->getUser();
        $data = [
            'cstitle' => $this->request->cstitle??null,
            'cscatid' => $this->request->cscatid??null,
            'csuserid' => $user->userid,
            'cstime' => TIME,
            'csthumb' => $this->request->csthumb??null,
            'cssequence' => $this->request->cssequence??0,
            'csdescribe' => $this->request->csdescribe??"",
            'csprogress' => $this->request->csprogress??0,
            'csfacetime' => $this->request->csfacetime??0,
            'cstext' => $this->request->cstext??"",
        ];
        // TODO: 实现添加逻辑
        // 示例：
        $model = CourseSubject::fillWithInit($data);
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
        $csId = $this->request->csid;
        $model = CourseSubject::find($csId);
        if(!$model->csid) return error(['error' => '记录不存在']);
        $data = [
            'cstitle' => $this->request->cstitle??null,
            'cscatid' => $this->request->cscatid??null,
            'csthumb' => $this->request->csthumb??null,
            'cssequence' => $this->request->cssequence??null,
            'csdescribe' => $this->request->csdescribe??null,
            'csprogress' => $this->request->csprogress??null,
            'csfacetime' => $this->request->csfacetime??0,
            'cstext' => $this->request->cstext??"",
        ];
        $data = array_filter($data,function ($item){
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
        CourseSubject::getQuery()->whereIn('csid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = CourseSubject::getQuery()->orderBy('cssequence','desc')->orderBy('csid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search??null;
        if($search) {
            if($search['keyword']??false)$query->where('cstitle', 'like', '%'.$search['keyword'].'%');
            if($search['cscatid']??false)$query->where('cscatid', $search['cscatid']);
        }
        $data = $query->paginate($page, $limit);
        if($data['total'] > 0)
        {
            $cats = [];
            foreach ($data['data'] as $item) {
                $cats[] = $item['cscatid'];
            }
            $cats = array_unique($cats);
            $categories = CourseCategory::getQuery()->whereIn('catid', $cats)->get();
            $cats = [];
            foreach ($categories as $category) {
                $cats[$category['catid']] = $category['catname'];
            }
            array_walk($data['data'], function (&$item) use ($cats) {
                $item['catname'] = $cats[$item['cscatid']];
                $item['cstime'] = date('Y-m-d', $item['cstime']);
            });
        }
        return $data;
    }

    public function Index(): Error|array
    {
        $csId = $this->request->csid??null;
        if($csId)$data = CourseSubject::find($csId)->getRaw();
        return $data??[];
    }
}
