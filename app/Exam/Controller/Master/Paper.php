<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamPaper;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Paper extends Controller implements ControllerInterface
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
            'paper' => 'Paper',
            'question' => 'Question',
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
        $user = $this->request->getUser();
        $data = [
            'exam' => $this->request->exam??null,
            'exampassmark' => $this->request->exampassmark??null,
            'examquestions' => $this->request->examquestions??'',
            'examscalemodel' => $this->request->examscalemodel??0,
            'examsetting' => $this->request->examsetting??null,
            'examsubject' => $this->request->examsubject??null,
            'examtotalscore' => $this->request->examtotalscore??100,
            'examtotaltime' => $this->request->examtotaltime??60,
            'examtype' => $this->request->examtype??null,
            'examtime' => TIME,
            'examauthor' => $user->username??null
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = ExamPaper::fillWithInit($data);
        $model->examsetting = is_array($data['examsetting'])?json_encode($data['examsetting'],JSON_UNESCAPED_UNICODE):'';
        $model->examquestions = is_array($data['examquestions'])?json_encode($data['examquestions'],JSON_UNESCAPED_UNICODE):'';
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
        $id = $this->request->examid;
        $model = ExamPaper::find($id);
        if(!$model->examid) return error(['error' => '记录不存在']);
        $data = [
            'exam' => $this->request->exam??null,
            'exampassmark' => $this->request->exampassmark??null,
            'examquestions' => $this->request->examquestions??null,
            'examscalemodel' => $this->request->examscalemodel??null,
            'examsetting' => $this->request->examsetting??null,
            'examsubject' => $this->request->examsubject??null,
            'examtotalscore' => $this->request->examtotalscore??null,
            'examtotaltime' => $this->request->examtotaltime??null,
            'examscore' => $this->request->examscore??null,
        ];
        $data = array_filter($data, function($item){
            return !is_null($item);
        });
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $model->$key = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
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
        ExamPaper::getQuery()->whereIn('examid', $ids)->delete();

        return ['msg' => '删除成功'];
    }

    public function Question():array | Error
    {
        $paperId = $this->request->paperid;
        $paper = ExamPaper::find($paperId);
        if(!$paper->examid || ($paper->examtype !== 2))
        {
            return error(['error' => '错误的试卷']);
        }
        $examquestions = json_decode($paper->examquestions,true);
        if(!$examquestions)return error(['error' => '错误的试卷']);
        $examscore = json_decode($paper->examscore,true);
        $examsetting = json_decode($paper->examsetting,true);
        $questions = array();
        foreach($examquestions as $key => $p)
        {
            if($p['questions'])
            {
                $questions[$key]['questions'] = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->select(['questionid','question','questiontype'])
                    ->whereIn('questionid', $p['questions'])->get();
                foreach($p['questions'] as $id)
                {
                    $examscore[$id] = $examscore[$id]??$examsetting['questionTypes'][$key]['score'];
                }
            }
            if($p['rowsquestions'])
            {
                $questions[$key]['rowsquestions'] = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->select(['questionid','question','questiontype'])
                    ->where('questionisparent',1)
                    ->whereIn('questionid', $p['rowsquestions'])->get();
                foreach ($questions[$key]['rowsquestions'] as $id => $row)
                {
                    $children = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->select(['questionid','question','questiontype'])
                        ->orderBy('questionsequence','ASC')->orderBy('questionid','DESC')
                        ->where('questionparent',$row['questionid'])
                        ->get();
                    $questions[$key]['rowsquestions'][$id]['data'] = $children;
                    foreach ($children as $child)
                    {
                        $examscore[$child['questionid']] = $examscore[$child['questionid']]??$examsetting['questionTypes'][$key]['score'];
                    }
                }
            }
        }
        return [
            'examscore' => $examscore,
            'questions' => $questions,
        ];
    }

    public function All():array|Error
    {
        $subjectId = $this->request->subjectid;
        $results = ExamPaper::getQuery()->select(['examid', 'exam'])
            ->where('examsubject', $subjectId)
            ->get();
        $papers = [];
        foreach ($results as $result) {
            $papers[$result['examid']] = $result['exam'];
        }
        return $papers;
    }

    public function Paper():array
    {
        $paperId = $this->request->paperid;
        $paper = ExamPaper::find($paperId);
        $paper->examsetting = json_decode($paper->examsetting,true);
        $paper->examquestions = json_decode($paper->examquestions,true);
        return $paper->getRaw();
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = ExamService::getPaperQueryWithSubject()->select(['examid', 'exam','examtotalscore','examsubject','examtype','examtime','subject']);
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['keyword']??false)$query->where("exam", "like", "%{$search['keyword']}%");
            if($search['subjectid']??false)$query->where("examsubject", $search['subjectid']);
        }
        $data = $query->paginate($page, $limit);
        array_walk($data['data'], function (&$item) {
            $item['examtime'] = date('Y-m-d',$item['examtime']);
        });
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
