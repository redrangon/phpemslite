<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamMember;
use PHPEMS\App\Exam\Service\Model\ExamPrice;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\Point;
use PHPEMS\App\Exam\Service\Model\Question;
use PHPEMS\App\Exam\Service\Model\QuestionType;
use PHPEMS\App\Exam\Service\Model\Section;
use PHPEMS\App\Exam\Service\Model\Subject;
use PHPEMS\App\User\Service\Model\UserExpense;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Basic extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Exam','Json'];

    protected ExamSession $session;
    protected \PHPEMS\App\Exam\Service\Model\Basic $basic;

    public function __construct(protected RequestInterface $request)
    {
        parent::__construct($this->request);
        $this->session = $this->request->getStore('session');
        $this->basic = $this->request->getStore('basic');
    }

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'current' => 'CurrentBasic',
            'detail' => 'QuestionDetail',
            'point' => 'SectionPoint',
            'subject' => 'Subject'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [
            'current' => ['Auth','Exam','Json']
        ];
        return $flows[$action]??self::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }



    public function Subject(): array | Error
    {
        $subject = Subject::find($this->basic->basicsubject);
        return $subject->getRaw();
    }

    public function SectionPoint(): array | Error
    {
        $sectionIds = array_keys($this->basic->basicpoint);
        $pointIds = array_merge(...$this->basic->basicpoint);
        $sections = Section::getQuery()->select(['sectionid','section'])->whereIn('sectionid', $sectionIds)->get();
        $tmpPoints = Point::getQuery()->select(['pointid','point','pointsectionid'])->whereIn('pointid', $pointIds)->get();
        $points = [];
        foreach($tmpPoints as $point)
        {
            $points[$point['pointsectionid']][] = [
                'pointid' => $point['pointid'],
                'point' => $point['point'],
            ];
        }
        return ['sections' => $sections,'points' => $points];
    }


    public function CurrentBasic(): Error|array
    {
        $basic = $this->basic->getRaw();
        return $basic??[];
    }

    public function Data(): array | Error
    {
        if($this->basic->basicexam['model'])
        {
            return error(['error' => '错误的模式']);
        }
        $points = array_merge(...$this->basic->basicpoint);
        $query = ExamService::getQuestionQueryWithRelation()->select(['DISTINCT questionid','question','questiontype','questionlevel','questionisparent'])
            ->whereIn('qkpointid',$points)
            ->where('questionparent',0)
            ->orderBy('questionid','ASC');
        $search = $this->request->search;
        $page = intval($this->request->page)??1;
        $limit = intval($this->request->limit)??20;
        if($search['questionid']??false)$query->where('questionid',$search['questionid']);
        if($search['keyword']??false)$query->where('question','like','%'.$search['keyword'].'%');
        if($search['questiontype']??false)$query->where('questiontype',$search['questiontype']);
        if($search['questionlevel']??false)$query->where('questionlevel',$search['questionlevel']);
        if($search['questionisparent']??false)$query->where('questionisparent',$search['questionisparent'] - 1);
        if($search['points']??false){
            $query->whereIn('qkpointid',$search['points']);
        }
        elseif($search['sections']??false){
            $points = [];
            foreach($search['sections'] as $section)
            {
                $points = array_merge($points,$this->basic->basicpoint[$section]??[]);
            }
            $query->whereIn('qkpointid',$points);
        }
        return $query->paginate($page,$limit);
    }

    public function QuestionDetail(): array | Error
    {
        if($this->basic->basicexam['model'])
        {
            return error(['error' => '错误的模式']);
        }
        $questionId = $this->request->questionid??null;
        if(!$questionId)return error(['error' => '记录不存在']);
        $question = Question::find($questionId)->getRaw();
        if(!$question['questionid'])return error(['error' => '记录不存在']);
        if($question['questionisparent'])
        {
            $children = Question::getQuery()->orderBy('questionsequence','desc')->groupBy('questionid','ASC')
                ->select(['questionid','questiontype','question','questionselect','questionselectnumber','questionselecttype','questionanswer','questiondescribe','questionsequence','questionlevel','questionhtml'])
                ->where('questionparent', '=', $questionId)
                ->get();
            array_walk($children,function(&$item){
                $item['questionhtml'] = json_decode($item['questionhtml'],true)??[];
            });
            $question['data'] = $children;
        }
        else $question['questionhtml'] = json_decode($question['questionhtml'],true)??[];
        return $question;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
