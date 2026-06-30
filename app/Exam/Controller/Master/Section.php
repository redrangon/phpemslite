<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Point;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Section extends Controller implements ControllerInterface
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
        $points = Point::getQuery()->select(['pointid'])
            ->whereIn('pointsectionid',$ids)
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
            'section' => $this->request->section??null,
            'sectionsubjectid' => $this->request->sectionsubjectid??null
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = \PHPEMS\App\Exam\Service\Model\Section::fillWithInit($data);
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
        $sectionId = $this->request->sectionid;
        $model = \PHPEMS\App\Exam\Service\Model\Section::find($sectionId);
        if(!$model->sectionid) return error(['error' => '记录不存在']);
        $data = [
            'section' => $this->request->section??null,
            'sectiondescribe' => $this->request->sectiondescribe??null,
            'sectionsequence' => $this->request->sectionsequence??null,
        ];
        $data = array_filter($data, function ($value) {
            return $value !== null;
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
        \PHPEMS\App\Exam\Service\Model\Section::getQuery()->whereIn('id', $ids)->delete();

        return ['msg' => '删除成功'];
    }

    public function All():array|Error
    {
        $subjectId = $this->request->subjectid??null;
        if(!$subjectId)return error(['error' => '参数错误']);
        $query = \PHPEMS\App\Exam\Service\Model\Section::getQuery();
        $items = $query->select(['sectionid','section'])->where('sectionsubjectid', $subjectId)->get();
        $sections = [];
        foreach ($items as $item) {
            $sections[$item['sectionid']] = $item['section'];
        }
        return $sections;
    }

    /**
     * 获取数据列表
     */
    public function Data(): array|Error
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = \PHPEMS\App\Exam\Service\Model\Section::getQuery()->orderBy('sectionid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $subjectid = $this->request->subjectid??null;
        if($subjectid)$query->where('sectionsubjectid', $subjectid);
        $search = $this->request->search;
        if($search) {
            if($search['keyword']??false)$query->where('section', 'like', '%'.$search['keyword'].'%');
        }
        return $query->paginate($page, $limit);
    }

    public function Index(): Error|array
    {
        $sectionId = $this->request->sectionid??null;
        if(!$sectionId)return error(['error' => '记录不存在']);
        $section = \PHPEMS\App\Exam\Service\Model\Section::find($sectionId);
        if(!$section->sectionid)return error(['error' => '记录不存在']);
        return $section->getRaw();
    }
}
