<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
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
            'all' => 'All',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'subject' => 'Subject',
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
        $points = ExamService::getPointQueryWithSection()->select(['pointid'])
            ->whereIn('sectionsubjectid',$ids)
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
    public function Add():array|Error
    {
        // 获取请求数据
        $data = [
            'subject' => $this->request->subject??null,
            'subjectsetting' => $this->request->subjectsetting??null,
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = \PHPEMS\App\Exam\Service\Model\Subject::fillWithInit($data);
        $result = $model->toValidate();
        if($result !== true)return $result;
        $model->subjectsetting = json_encode($model->subjectsetting, JSON_UNESCAPED_UNICODE);
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
        $subjectId = $this->request->subjectid;
        $model = \PHPEMS\App\Exam\Service\Model\Subject::find($subjectId);
        if(!$model->subjectid) return error(['error' => '记录不存在']);
        $data = [
            'subject' => $this->request->subject??null,
            'subjectsetting' => $this->request->subjectsetting??null,
        ];
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });
        foreach ($data as $key => $value) {
            if($key == 'subjectsetting')$model->$key = json_encode($value, JSON_UNESCAPED_UNICODE);
            else $model->$key = $value;
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
        \PHPEMS\App\Exam\Service\Model\Subject::getQuery()->whereIn('subjectid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function All():array
    {
        $query = \PHPEMS\App\Exam\Service\Model\Subject::getQuery()->select(['subjectid','subject'])
            ->orderBy('subjectid', 'DESC');
        $results = $query->get();
        $subjects = [];
        foreach($results as $result)
        {
            $subjects[$result['subjectid']] = $result['subject'];
        }
        return $subjects;
    }

    public function subject():array
    {
        $subjectId = $this->request->subjectid;
        $subject = \PHPEMS\App\Exam\Service\Model\Subject::find($subjectId)->getRaw();
        $subject['subjectsetting'] = json_decode($subject['subjectsetting'], true);
        return $subject;
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = \PHPEMS\App\Exam\Service\Model\Subject::getQuery()->orderBy('subjectid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['keyword'])$query->where('subject','like','%'.$search['keyword'].'%');
        }
        $data = $query->paginate($page, $limit);
        array_walk($data['data'], function (&$item) {
            $item['subjectsetting'] = json_decode($item['subjectsetting'],true)??[];
            $item['questionnumber'] = ExamService::getSubjectQuestionNumber($item['subjectid']);
            $item['papernumber'] = ExamService::getSubjectPaperNumber($item['subjectid']);
        });
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
