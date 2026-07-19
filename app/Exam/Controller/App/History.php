<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\HistoryDetail;
use PHPEMS\App\Exam\Service\Model\Point;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class History extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Exam','Json'];
    protected ExamSession $session;
    protected Basic $basic;

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
            'detail' => 'Detail',
            'delete' => 'Delete',
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

    public function Delete(): array | Error
    {
        $ehId = $this->request->ehId;
        $history = \PHPEMS\App\Exam\Service\Model\History::find($ehId);
        if(($history->ehtype < 2) && ($history->ehpassport == $this->request->getUser()->userpassport))
        {
            $detail = HistoryDetail::findByEhId($ehId);
            $history->delete();
            $detail->delete();
            return [];
        }
        return error(['error' => '错误的参数']);
    }

    public function Detail(): array | Error
    {
        if($this->basic->basicexam['model'] == 2){
            return error(['error' => '错误的考场模式']);
        }
        $ehid = $this->request->ehid;
        $user = $this->request->getUser();
        $history = \PHPEMS\App\Exam\Service\Model\History::find($ehid);
        $detail = HistoryDetail::findByEhId($ehid);
        $detail->ehdscores = json_decode($detail->ehdscores,true);
        $detail->ehdsetting = json_decode($detail->ehdsetting,true);
        $detail->ehdquestion = json_decode($detail->ehdquestion,true);
        $detail->ehdanswer = json_decode($detail->ehdanswer,true);
        $formatedQuestions = [];
        $favorIds = [];
        foreach($detail->ehdquestion as $key => $sessionQuestions)
        {
            if(!empty($sessionQuestions['questions']) && is_array($sessionQuestions['questions']))
            {
                $tmp = $sessionQuestions['questions'];
                foreach($tmp as $question)
                {
                    $favorIds[] = $question['questionid'];
                }
                $formatedQuestions[$key] = [...$tmp];
            }
            if(!empty($sessionQuestions['rowsQuestions']) && is_array($sessionQuestions['rowsQuestions']) )
            {
                $tmp = $sessionQuestions['rowsQuestions'];
                foreach($tmp as $rows)
                {
                    $parent = $rows['question'];
                    foreach($rows['children'] as $index => $question)
                    {
                        $question['parent'] = $parent;
                        $question['childindex'] = $index;
                        $formatedQuestions[$key][] = $question;
                        $favorIds[] = $question['questionid'];
                    }
                }
            }
        }
        $detail->ehdquestion = $formatedQuestions;
        $favors = \PHPEMS\App\Exam\Service\Model\Favor::getQuery()->where("favorpassport",$user->userpassport)
            ->whereIn("favorquestionid",$favorIds)
            ->where('favorsubjectid',$this->basic->basicsubjectid)
            ->get();
        $favors = array_column($favors,null,'favorquestionid');
        array_walk($favors,function(&$item){
            $item = true;
        });
        $pointIds = array_merge(...$this->basic->basicpoint);
        $points = Point::getQuery()->select(['pointid','point'])->whereIn('pointid',$pointIds)->get();
        $points = array_column($points,'point','pointid');
        $history->points = $points;
        $history->detail = $detail->getRaw();
        $history->favors = $favors;
        return $history->getRaw();
    }

    public function Data(): array | Error
    {
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $examType = $this->request->examType ?? 1;
        $user = $this->request->getUser();
        $data = \PHPEMS\App\Exam\Service\Model\History::getQuery()->orderBy('ehid', 'desc')
            ->where('ehbasicid',$this->basic->basicid)
            ->where('ehpassport',$user->userpassport)
            ->where('ehtype',$examType)
            ->paginate($page,$limit);
        array_walk($data['data'], function (&$item) {
            $item['ehstarttime'] = date('Y-m-d H:i',$item['ehstarttime']);
            $item['ehendtime'] = date('Y-m-d H:i',$item['ehendtime']);
        });
        return $data;
    }

    public function index():array | Error
    {
        $ehid = $this->request->ehid;
        $history = \PHPEMS\App\Exam\Service\Model\History::find($ehid);
        if(!$history->ehstats)
        {
            $history = ExamService::cacheExamHistoryDetail($history);
        }
        $history->ehstats = json_decode($history->ehstats,true);
        $pointIds = array_keys($history->ehstats['pointAnalysis']['totalPointNumber']??[]);
        if(!empty($pointIds))
        {
            $points = Point::getQuery()->select(['pointid','point'])->whereIn('pointid',$pointIds)->get();
            $points = array_column($points,'point','pointid');
            $history->points = $points;
        }
        return $history->getRaw();
    }
}