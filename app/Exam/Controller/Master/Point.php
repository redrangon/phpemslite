<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Point extends Controller implements ControllerInterface
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
            'add' => 'Add',
            'refresh' => 'RefreshCache',
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

    public function RefreshCache():array|Error
    {
        $ids = $this->request->ids??null;
        if(!$ids) return error(['error' => '记录不存在']);
        $points = \PHPEMS\App\Exam\Service\Model\Point::getQuery()->select(['pointid'])
            ->whereIn('pointid',$ids)
            ->get();
        foreach($points as $point)
        {
            ExamService::refreshPointCache($point['pointid']);
        }
        return ['msg' => '刷新成功'];
    }

    /**
     * 添加操作
     */
    public function Add():array | Error
    {
        // 获取请求数据
        $data = [
            'point' => $this->request->point??null,
            'pointsectionid' => $this->request->pointsectionid??null,
            'pointsequence' => $this->request->pointsequence??0
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = \PHPEMS\App\Exam\Service\Model\Point::fillWithInit($data);
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
        $pointId = $this->request->pointid;
        $model = \PHPEMS\App\Exam\Service\Model\Point::find($pointId);
        if(!$model->pointid) return error(['error' => '记录不存在']);
        $data = [
            'point' => $this->request->point??null,
            'pointsectionid' => $this->request->pointsectionid??null,
            'pointsequence' => $this->request->pointsequence??null,
            'pointnumber' => $this->request->pointnumber??null,
            'pointquestions' => $this->request->pointquestions??null,
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
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        \PHPEMS\App\Exam\Service\Model\Point::getQuery()->whereIn('pointid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function All():array|Error
    {
        $query = ExamService::getPointQueryWithSection()->select(['pointid','point','pointsectionid']);
        $subjectId = $this->request->subjectid??null;
        $sectionId = $this->request->sectionid??null;
        $singleType = $this->request->singletype??false;
        if($subjectId || $sectionId){
            if($subjectId){
                $query->where("sectionsubjectid", $subjectId);
            }
            if($sectionId){
                if(is_array($sectionId)){
                    $query->whereIn("sectionid", $sectionId);
                }
                else $query->where("sectionid", $sectionId);
            }
            $results = $query->get();
            $points = [];
            foreach ($results as $result){
                if($singleType)$points[$result['pointid']] = $result['point'];
                else $points[$result['pointsectionid']][$result['pointid']] = $result['point'];
            }
            return $points;
        }
        else return error(['error' => '缺少参数']);
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = \PHPEMS\App\Exam\Service\Model\Point::getQuery()->orderBy('pointsequence', 'DESC')->orderBy('pointid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $sectionid = $this->request->sectionid??null;
        if($sectionid)$query->where("pointsectionid", $sectionid);
        $search = $this->request->search;
        if($search) {
        // 处理搜索条件
        }
        $data = $query->paginate($page, $limit);
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
