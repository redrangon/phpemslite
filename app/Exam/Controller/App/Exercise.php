<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\Favor;
use PHPEMS\App\Exam\Service\Model\Point;
use PHPEMS\App\Exam\Service\Model\Question;
use PHPEMS\App\Exam\Service\Model\Section;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Exercise extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Exam','Json'];
    protected ExamSession $session;
    protected Basic $basic;

    public function __construct(protected RequestInterface $request)
    {
        parent::__construct($this->request);
        $this->session = $this->request->getStore('session');
        $this->basic = $this->request->getStore('basic');
        if($this->basic->basicexam['model'] == 2)
        {
            throw new \Exception('正式考试期间不能进入练习');
        }
    }

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'progress' => 'Progress',
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

    public function Data(): array | Error
    {
        $user = $this->request->getUser();
        $sectionIds = array_keys($this->basic->basicpoint);
        $pointIds = array_merge(...$this->basic->basicpoint);
        $sectionPoints = $this->basic->basicpoint;
        $sections = Section::getQuery()->select(['sectionid','section'])->whereIn('sectionid', $sectionIds)->get();
        $points = Point::getQuery()->select(['pointid','point','pointnumber'])->whereIn('pointid', $pointIds)->get();
        $progress = \PHPEMS\App\Exam\Service\Model\Exercise::getQuery()->select(['exerpointid','exernumber'])
            ->whereIn('exerpointid', $pointIds)
            ->where('exerbasicid',$this->basic->basicid)
            ->where('exeruserid',$user->userid)
            ->get();
        $sections = array_column($sections, null, 'sectionid');
        $points = array_column($points, null, 'pointid');
        $progress = array_column($progress, 'exernumber', 'exerpointid');
        $data = [];
        foreach($sectionPoints as $key => $section)
        {
            $tmpData = [
                ...$sections[$key],
                'points' => []
            ];
            foreach($section as $point)
            {
                $tmpData['points'][] = [...$points[$point],'pointallnumber' => array_sum(json_decode($points[$point]['pointnumber'],true)??[]),'progress' => $progress[$point]??0];
            }
            $data[] = $tmpData;
        }
        return $data;
    }

    public function Progress(): array | Error
    {
        $user = $this->request->getUser();
        $pointId = $this->request->pointid??null;
        $pointIds = array_merge(...$this->basic->basicpoint);
        if(!$pointId || !in_array($pointId, $pointIds))return error(['error' => '不存在的知识点']);
        $progress = \PHPEMS\App\Exam\Service\Model\Exercise::getQuery()->select(['exerid','exerpointid','exernumber'])
            ->where('exerpointid', $pointId)
            ->where('exerbasicid',$this->basic->basicid)
            ->where('exeruserid',$user->userid)
            ->first();
        if(!$progress)
        {
            \PHPEMS\App\Exam\Service\Model\Exercise::getQuery()->insert([
                'exerpointid' => $pointId,
                'exerplanid' => 0,
                'exeruserid' => $user->userid,
                'exerbasicid' => $this->basic->basicid,
                'exernumber' => 1,
                'exerqutype' => 0
            ]);
        }
        if($progress['exernumber'])return ['number' => $progress['exernumber']];
        else return ['number' => 0];
    }

    public function index():array | Error
    {
        $pointId = $this->request->pointid??null;
        $pointIds = array_merge(...$this->basic->basicpoint);
        if(!$pointId || !in_array($pointId, $pointIds))return error(['error' => '不存在的知识点']);
        $user = $this->request->getUser();
        $page = max($this->request->page??1,1);
        if($page > 1)
        {
            \PHPEMS\App\Exam\Service\Model\Exercise::getQuery()
                ->where('exerpointid', $pointId)
                ->where('exerbasicid',$this->basic->basicid)
                ->where('exeruserid',$user->userid)
                ->update(['exernumber' => $page]);
        }
        $questions = ExamService::getQuestionQueryWithRelation()->select(['questionid'])->orderBy('questionid','ASC')
            ->where('qkpointid',$pointId)
            ->where('questionisparent',0)
            ->get();
        $rowsQuestions = ExamService::getQuestionQueryWithRelation()->select(['questionid'])->orderBy('questionid','ASC')
            ->where('qkpointid',$pointId)
            ->where('questionisparent',1)
            ->get();
        $rowsStart = count($questions);
        if($page > $rowsStart)
        {
            $ids = array_column($rowsQuestions, 'questionid');
            $childQuestions = Question::getQuery()->select(['questionid'])->whereIn('questionparent',$ids)
                ->orderBy('questionparent','ASC')
                ->orderBy('questionsequence','ASC')
                ->get();
            $questionId = $childQuestions[$page - $rowsStart - 1]['questionid']??0;
        }
        else $questionId = $questions[$page - 1]['questionid']??0;
        if(!$questionId)return error(['error' => '没有题目']);
        $isFavor = Favor::getQuery()->where('favorquestionid',$questionId)
            ->where('favorpassport',$user->userpassport)
            ->first();
        $question = Question::find($questionId);
        $question->isFavor = (bool)$isFavor;
        if($question->questionparent > 0)
        {
            $parent = Question::find($question->questionparent);
            $question->parent = $parent->question;
            $question->parentQuestionType = $parent->questiontype;
        }
        $point = Point::find($pointId);
        $total = array_sum(json_decode($point->pointnumber,true));
        $question->questionhtml = json_decode($question->questionhtml,true);
        return ['question' => $question->getRaw(),'total' => $total,'page' => $page];
    }
}