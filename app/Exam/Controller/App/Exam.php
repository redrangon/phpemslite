<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamPaper;
use PHPEMS\App\Exam\Service\Model\ExamPaperSession;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\History;
use PHPEMS\App\Exam\Service\Model\Subject;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Exam extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Exam','Json'];
    protected Basic $basic;
    protected ExamSession $session;

    public function __construct()
    {
        parent::__construct();
        $this->session = $this->request->getStore('session');
        $this->basic = $this->request->getStore('basic');
        $this->basic->basicexam = json_decode($this->basic->basicexam, true);
        $this->basic->basicpoint = json_decode($this->basic->basicpoint, true);
        $intime = 1;
        if(($this->basic->basicexam['opentime']??false) && strtotime($this->basic->basicexam['opentime']) > TIME)
        {
            $intime = 0;
        }
        if(($this->basic->basicexam['closetime']??false) && strtotime($this->basic->basicexam['closetime']) < TIME)
        {
            $intime = 0;
        }
        $this->basic->intime = $intime;
    }

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'subject' => 'Subject',
            'verify' => 'FaceVerify',
            'history' => 'ExamHistory',
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

    public function ExamHistory(): array | Error
    {
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $query = History::getQuery()->orderBy('ehid', 'desc')
            ->where('ehbasicid',$this->basic->basicid)
            ->where('ehplanid',0)
            ->where('ehpassport',$this->request->getUser()->userpassport)
            ->where('ehtype',2);
        $data = $query->paginate($page,$limit);
        array_walk($data['data'], function (&$item) {
            $item['ehstarttime'] = date('Y-m-d H:i:s',$item['ehstarttime']);
            $item['ehendtime'] = date('Y-m-d H:i:s',$item['ehendtime']);
        });
        return $data;
    }

    public function FaceVerify(): array | Error
    {
        $user = $this->request->getUser();
        $face = $this->request->face??null;
        if(!$face)return error(['error' => '人脸不存在']);
        $this->session->esfadtime = TIME;
        $this->session->save();
        return ['faceVerify' => 1];
    }
    public function Data(): array | Error
    {
        $user = $this->request->getUser();
        $hasExamNumber = true;
        if($this->basic->basicexam['model'] == 2)
        {
            $paperIds = $this->basic->basicexam['self'];
            if($this->basic->basicexam['examnumber'] > 0)
            {
                $historyNumber = History::getQuery()->where('ehtype', 2)
                    ->where('ehpassport', $user->userpassport)
                    ->where('ehbasicid', $this->basic->basicid)
                    ->where('ehplanid', $this->plan->planid)
                    ->count();
                if($historyNumber >= $this->basic->basicexam['examnumber'])$hasExamNumber = false;
            }
        }
        else $paperIds = $this->basic->basicexam['auto'];
        if(!empty($paperIds))
        {
            $session = ExamPaperSession::getQuery()->where('examsessiontype',$this->basic->basicexam['model'] == 2?2:1)
                ->where('examsessionpassport',$this->request->getUser()->userpassport)
                ->where('examsessionbasicid',$this->basic->basicid)
                ->first();
            if($session)$session['examsessionstarttime'] = date('Y-m-d H:i:s',$session['examsessionstarttime']);
            $papers = ExamPaper::getQuery()->whereIn('examid', $paperIds)->get();
            array_walk($papers,function(&$item){
                $item['examsetting'] = json_decode($item['examsetting'],true);
                $item['examquestions'] = json_decode($item['examquestions'],true);
                $item['examscore'] = json_decode($item['examscore'],true);
            });
            return ['papers' => $papers,'session' => $session,'hasnumber' => $hasExamNumber];
        }
        return error(['error' => '当前考场未配置试卷']);
    }

    public function Subject(): array | Error
    {
        $subject = Subject::find($this->basic->basicsubject);
        return $subject->getRaw();
    }

    public function index():array | Error
    {
        return $this->basic->getRaw();
    }
}