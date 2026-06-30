<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamPaper;
use PHPEMS\App\Exam\Service\Model\Relation;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Question extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'delchildren' => 'DeleteChildren',
            'add' => 'Add',
            'setrelation' => 'SetRelation'
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

    private function buildSearchQuery(QueryBuilder $query, array $search = []):QueryBuilder
    {
        if(empty($search))return $query;
        if($search['ids']??false)$query->whereIn('questionid', $search['ids']);
        if($search['questionid']??false)$query->where('questionid', $search['questionid']);
        if($search['keyword']??false)$query->where('question', 'like', '%'.$search['keyword'].'%');
        if($search['points']??false)$query->whereIn('qkpointid', $search['points']);
        elseif($search['sections']??false)
        {
            if(!is_array($search['sections']))$search['sections'] = [$search['sections']];
            $records = ExamService::getPointQueryWithSection()->select(['pointid'])->whereIn('sectionid', $search['sections'])->get();
            $points = [];
            foreach($records as $record)
            {
                $points[] = $record['pointid'];
            }
            $query->whereIn('qkpointid', $points);
        }
        elseif($search['subjectid']??false){
            $records = ExamService::getPointQueryWithSection()->select(['pointid'])->where('sectionsubjectid', $search['subjectid'])->get();
            $points = [];
            foreach($records as $record)
            {
                $points[] = $record['pointid'];
            }
            $query->whereIn('qkpointid', $points);
        }
        if($search['questiontype']??false)$query->where('questiontype', $search['questiontype']);
        if($search['level']??false)$query->where('questionlevel', $search['level']);
        if($search['range']??false)
        {
            if($search['range'][0]??false)$query->where('questioncreatetime', '>=',strtotime($search['range'][0]));
            if($search['range'][1]??false)$query->where('questioncreatetime', '<=',strtotime($search['range'][1]));
        }
        return $query;
    }

    public function SetRelation():array | Error
    {
        $points = $this->request->points;
        $questionId = $this->request->questionid;
        if($points && $questionId){
            $result = ExamService::setQuestionRelation($points, $questionId);
            if($result === true)return ['msg' => '操作成功'];
            else return $result;
        }
        else return error(['error' => '缺少参数']);
    }

    /**
     * 添加操作
     */
    public function Add():array | Error
    {
        // 获取请求数据
        $user = $this->request->getUser();
        $points = $this->request->points;
        $data = [
            'questiontype' => $this->request->questiontype??null,
            'question' => $this->request->question??null,
            'questionuserid' => $user->userid??null,
            'questionusername' => $user->username??null,
            'questionselect' => $this->request->questionselect??"",
            'questionselecttype' => $this->request->questionselecttype??0,
            'questionanswer' => $this->request->questionanswer??"",
            'questiondescribe' => $this->request->questiondescribe??"",
            'questioncreatetime' => TIME,
            'questionparent' => $this->request->questionparent??0,
            'questionisparent' => $this->request->questionisparent??0,
            'questionchildnumber' => $this->request->questionchildnumber??1,
            'questionsequence' => $this->request->questionsequence??0,
            'questionlevel' => $this->request->questionlevel??1,
        ];
        if($data['questionisparent'])$data['questionchildnumber'] = 0;
        // TODO: 实现添加逻辑
        // 示例：
        $data = ExamService::formatQuestion($data);
        $model = \PHPEMS\App\Exam\Service\Model\Question::fillWithInit($data);
        $result = $model->toValidate();
        if($result !== true)return $result;
        try{
            \PHPEMS\App\Exam\Service\Model\Question::getDB()->transaction(function() use($model, $points){
                $model->save();
                if(!empty($points) && (!$model->questionparent))
                {
                    foreach($points as $point){
                        $item = [
                            'qkquestionid' => $model->questionid,
                            'qkpointid' => $point,
                            'qkstatus' => 1
                        ];
                        Relation::getQuery()->insert($item);
                    }
                }
                elseif($model->questionparent)
                {
                    $childNumber = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->where("questionparent",$model->questionparent)->count('1');
                    \PHPEMS\App\Exam\Service\Model\Question::getQuery()->where("questionid",$model->questionparent)->update(['questionchildnumber' => $childNumber]);
                }
            });
        }catch (\Exception $e){
            return error(['error' => $e->getMessage()]);
        }
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        // TODO: 实现修改逻辑
        // 示例：
        $questionid = $this->request->questionid;
        $model = \PHPEMS\App\Exam\Service\Model\Question::find($questionid);
        if(!$model->questionid) return error(['error' => '记录不存在']);
        $user = $this->request->getUser();
        $data = [
            'questiontype' => $this->request->questiontype??null,
            'question' => $this->request->question??null,
            'questionlastmodifyuser' => $user->username,
            'questionselect' => $this->request->questionselect??null,
            'questionselectnumber' => $this->request->questionselectnumber??null,
            'questionselecttype' => $this->request->questionselecttype??null,
            'questionanswer' => $this->request->questionanswer??null,
            'questiondescribe' => $this->request->questiondescribe??null,
            'questionparent' => $this->request->questionparent??null,
            'questionisparent' => $this->request->questionisparent??null,
            'questionchildnumber' => $this->request->questionchildnumber??null,
            'questionsequence' => $this->request->questionsequence??null,
            'questionlevel' => $this->request->questionlevel??null,
        ];
        $data = ExamService::formatQuestion($data,$model->getRaw());
        $data = array_filter($data,function($item){
            return !is_null($item);
        });
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function DeleteChildren():array|Error
    {
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        $parents = [];
        foreach($ids as $id){
            $model = \PHPEMS\App\Exam\Service\Model\Question::find($id);
            if($model->questionparent)
            {
                if(!($parents[$model->questionparent]??false))$parents[$model->questionparent] = $model->questionparent;
                $model->delete();
            }
        }
        foreach($parents as $parent){
            $model = \PHPEMS\App\Exam\Service\Model\Question::find($parent);
            $childNumber = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->where("questionparent",$model->questionid)->count('1');
            $model->questionchildnumber = $childNumber;
            $model->save();
        }
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
        $search = $this->request->search;
        if(empty($ids) && empty($search)) return error(['error' => '未选择要删除的记录']);
        if($search)
        {
            $query = ExamService::getQuestionQueryWithRelation()->select(['qkid']);
            $query = $this->buildSearchQuery($query, $search);
            $records = $query->get();
            $ids = [];
            foreach($records as $record)
            {
                $ids[] = $record['qkid'];
            }
        }
        Relation::getQuery()->whereIn('qkid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = ExamService::getQuestionQueryWithRelation()->orderBy('qkid', 'DESC')->where('questionparent','0');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $hasChildren = $this->request->haschildren ?? false;
        if($hasChildren)$query->where('questionisparent',"1");
        else $query->where('questionisparent',"0");
        $search = $this->request->search;
        if($search) {
            $query = $this->buildSearchQuery($query, $search);
        }
        $data = $query->paginate($page, $limit);
        array_walk($data['data'],function(&$item){
            $item['questionhtml'] = json_decode($item['questionhtml'],true)??[];
        });
        return $data;
    }

    public function Index(): Error|array
    {
        $questionId = $this->request->questionid??null;
        if(!$questionId)return error(['error' => '记录不存在']);
        $question = \PHPEMS\App\Exam\Service\Model\Question::find($questionId)->getRaw();
        if(!$question['questionid'])return error(['error' => '记录不存在']);
        if($question['questionisparent'])
        {
            $children = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->orderBy('questionsequence','desc')->groupBy('questionid','ASC')
                ->select(['questionid','questiontype','question','questionselect','questionselectnumber','questionselecttype','questionanswer','questiondescribe','questionsequence','questionlevel','questionhtml'])
                ->where('questionparent', '=', $questionId)
                ->get();
            array_walk($children,function(&$item){
                $item['questionhtml'] = json_decode($item['questionhtml'],true)??[];
            });
            $question['data'] = $children;
        }
        return $question;
    }
}
