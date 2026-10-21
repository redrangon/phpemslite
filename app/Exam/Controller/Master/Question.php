<?php

namespace PHPEMS\App\Exam\Controller\Master;

use Exception;
use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Relation;
use PHPEMS\App\Exam\Service\QuestionImporter;
use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

use PHPEMS\Lib\Utils\FileProvider;

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
            'setrelation' => 'SetRelation',
            'import' => 'Import',
            'point' => 'Point',
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

    public function Point():array
    {
        $ids = $this->request->ids??[];
        $points = $this->request->points??[];
        if(!empty($ids) && !empty($points)){
            $relations = Relation::getQuery()->select(['qkquestionid'])
                ->whereIn('qkid', $ids)
                ->get();
            foreach($relations as $relation)
            {
                $questionId = $relation['qkquestionid'];
                $exists = Relation::getQuery()->select(['qkpointid'])
                    ->where('qkquestionid', $questionId)
                    ->get();
                $exists = array_column($exists, 'qkpointid');
                $plusPoints = array_diff($points, $exists);
                foreach($plusPoints as $point)
                {
                    $model = Relation::fillWithInit([
                        'qkquestionid' => $questionId,
                        'qkpointid' => $point,
                        'qkstatus' => 1
                    ]);
                    $model->save();
                }
            }
            return ['msg' => '设置成功'];
        }
        else error(['error' => '缺少参数']);
    }

    public function Import():array
    {
        $service = new FileProvider();
        $file = $this->request->getFile('file');
        try{
            $result = $service->upload($file);
            if($result['success'])
            {
                $result = DI(QuestionImporter::class, ['filePath' => $result['path']])->importQuestion();
                return ['msg' => '导入成功'];
            }
            else error(['error' => $result['error']]);
        }catch (Exception $e){
            error(['error' => $e->getMessage()]);
        }
    }

    public function SetRelation():array
    {
        $points = $this->request->points;
        $questionId = $this->request->questionid;
        if($points && $questionId){
            $result = ExamService::setQuestionRelation($points, $questionId);
            if($result === true)return ['msg' => '操作成功'];
            else return $result;
        }
        else error(['error' => '缺少参数']);
    }

    /**
     * 添加操作
     */
    public function Add():array
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
            error(['error' => $e->getMessage()]);
        }
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array
    {
        // TODO: 实现修改逻辑
        // 示例：
        $questionid = $this->request->questionid;
        $model = \PHPEMS\App\Exam\Service\Model\Question::find($questionid);
        if(!$model->questionid) error(['error' => '记录不存在']);
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
        if(!$model->save()) error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function DeleteChildren():array
    {
        $ids = $this->request->ids;
        if(empty($ids)) error(['error' => '未选择要删除的记录']);
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
    public function Delete():array
    {
        // TODO: 实现删除逻辑
        // 示例：
        $ids = $this->request->ids;
        $search = $this->request->search;
        if(empty($ids) && empty($search)) error(['error' => '未选择要删除的记录']);
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
        $search = $this->request->search??null;
        if($search) {
            $query = $this->buildSearchQuery($query, $search);
        }
        $data = $query->paginate($page, $limit);
        $points = [];
        foreach($data['data'] as $item)
        {
            $points[] = $item['qkpointid'];
        }
        $points = array_unique($points);
        $points = \PHPEMS\App\Exam\Service\Model\Point::getQuery()->select(['pointid','point','pointsectionid'])
            ->whereIn('pointid',$points)
            ->get();
        $points = array_column($points,null,'pointid');
        $sections = [];
        foreach($points as $point)
        {
            $sections[] = $point['pointsectionid'];
        }
        $sections = \PHPEMS\App\Exam\Service\Model\Section::getQuery()->select(['sectionid','section','sectionsubjectid'])
            ->whereIn('sectionid',$sections)
            ->get();
        $sections = array_column($sections,null,'sectionid');
        $subjects = [];
        foreach($sections as $section)
        {
            $subjects[] = $section['sectionsubjectid'];
        }
        $subjects = \PHPEMS\App\Exam\Service\Model\Subject::getQuery()->select(['subjectid','subject'])
            ->whereIn('subjectid',$subjects)
            ->get();
        $subjects = array_column($subjects,null,'subjectid');
        array_walk($data['data'],function(&$item) use ($points,$sections,$subjects){
            $item['questionhtml'] = json_decode($item['questionhtml'],true)??[];
            $point = $points[$item['qkpointid']]['point']??'';
            $section = $sections[$points[$item['qkpointid']]['pointsectionid']]['section']??'';
            $subject = $subjects[$sections[$points[$item['qkpointid']]['pointsectionid']]['sectionsubjectid']]['subject']??'';
            $item['point'] = "{$subject} / {$section} / {$point}";
        });
        return $data;
    }

    public function Index(): array
    {
        $questionId = $this->request->questionid??null;
        if(!$questionId)error(['error' => '记录不存在']);
        $question = \PHPEMS\App\Exam\Service\Model\Question::find($questionId);
        if(!$question->questionId)error(['error' => '记录不存在']);
        if($question->questionIsParent)
        {
            $children = \PHPEMS\App\Exam\Service\Model\Question::getQuery()->orderBy('questionsequence','desc')->groupBy('questionid','ASC')
                ->select(['questionid','questiontype','question','questionselect','questionselectnumber','questionselecttype','questionanswer','questiondescribe','questionsequence','questionlevel','questionhtml'])
                ->where('questionparent', '=', $questionId)
                ->get();
            array_walk($children,function(&$item){
                $item['questionhtml'] = json_decode($item['questionhtml'],true)??[];
            });
            $question->data = $children;
        }
        return $question->getRaw();
    }
}
