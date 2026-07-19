<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\Question;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Favor extends Controller implements ControllerInterface
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
            'favor' => 'Favor',
            'unfavor' => 'UnFavor'
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

    public function UnFavor(): array | Error
    {
        if($this->basic->basicexam['model'] == 2)
        {
            return error(['error' => '错误的模式']);
        }
        $questionId = $this->request->questionId??null;
        if($questionId)
        {
            $user = $this->request->getUser();
            \PHPEMS\App\Exam\Service\Model\Favor::getQuery()->where('favorquestionid', $questionId)
                ->where('favorpassport',$user->userpassport)
                ->delete();
            return [];
        }
        return error(['error' => '错误的试题ID']);
    }

    public function Favor(): array | Error
    {
        if($this->basic->basicexam['model'] == 2)
        {
            return error(['error' => '错误的模式']);
        }
        $questionId = $this->request->questionId??null;
        if($questionId)
        {
            $user = $this->request->getUser();
            $favor = \PHPEMS\App\Exam\Service\Model\Favor::getQuery()->where('favorquestionid', $questionId)
                ->where('favorpassport',$user->userpassport)
                ->first();
            if($favor)return [];
            $favor = New \PHPEMS\App\Exam\Service\Model\Favor([
                'favorpassport' => $user->userpassport,
                'favorsubjectid' => $this->basic->basicSubjectId,
                'favorquestionid' => $questionId,
                'favortime' => TIME
            ]);
            $favor->save();
            return [];
        }
        return error(['error' => '错误的试题ID']);
    }

    public function index():array | Error
    {
        if($this->basic->basicexam['model'] == 2)
        {
            return error(['error' => '错误的模式']);
        }
        $page = $this->request->page??1;
        $query = ExamService::getFavorQueryWithQuestion()->select(['questionid','question','questiontype','questionhtml','questionparent','questionanswer','questiondescribe'])
            ->where('favorsubjectid',$this->basic->basicsubjectid)
            ->where('favorpassport',$this->request->getUser()->userpassport);
        $data = $query->paginate($page,1);
        array_walk($data['data'], function (&$item) {
            if($item['questionparent'])
            {
                $parent = Question::find($item['questionparent']);
                $item['parent'] = $parent->question;
            }
            $item['questionhtml'] = json_decode($item['questionhtml'],true);
        });
        return $data;
    }
}