<?php

namespace PHPEMS\App\Exam\Controller\App;

use Exception;
use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamPaper;
use PHPEMS\App\Exam\Service\Model\ExamPaperSession;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\Lib\Auth\Auth;
use PHPEMS\Lib\Core\Request\RequestInterface;
use PHPEMS\Lib\Face\FaceProvider;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\App\Member\Service\Model\Member;
use PHPEMS\Lib\Utils\Env;

class Session extends Controller implements ControllerInterface
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
            'subject' => 'Subject',
            'verify' => 'FaceVerify',
            'draw' => 'Draw',
            'save' => 'Save',
            'recover' => 'Recover',
            'time' => 'Time',
            'finish' => 'Finish',
            'drop' => 'Drop',
            'retest' => 'ReTest'
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

    public function ReTest(): array | Error
    {
        $ehId = $this->request->ehId??null;
        if(!$ehId)return error(['error' => '错误的参数']);
        if($this->basic->basicexam['model'] == 2)return error(['error' => '错误的考场模式']);
        $history = ExamService::getExamHistoryWithDetailById($ehId);
        if($history instanceof Error)return $history;
        $session = [];
        $session['examsessionfacelist'] = [];
        $session['examsessionpaperid'] = $history['ehpaperid'];
        $session['examsessionpassport'] = $history['ehpassport'];
        $session['examsessionplanid'] = $history['ehplanid'];
        $session['examsessionbasicid'] = $history['ehbasicid'];
        $session['examsessiontype'] = 1;
        $session['examsession'] = $history['ehexam'];
        $session['examsessionstarttime'] = TIME;
        $session['examsessionid'] = bin2hex(random_bytes(32));
        $session['examsessiontoken'] = md5($session['examsessionid'].'-'.Auth::getSessionSalt());
        $session['examsessionsetting'] = json_encode($history['ehdsetting'],JSON_UNESCAPED_UNICODE);
        $session['examsessionquestion'] = json_encode($history['ehdquestion'],JSON_UNESCAPED_UNICODE);
        $session['examsessionip'] = Env::getClientIp();
        $session['examsessionclient'] = Env::getClientEnv();
        $session = ExamPaperSession::fillWithInit($session);
        $result = $session->toValidate();
        if($result !== true)return $result;
        if($session->save())return ['sessionid' => $session->examSessionId];
        else return error(['error' => '参数错误']);
    }

    public function Time()
    {
        $sessionId = $this->request->sessionId??null;
        if(!$sessionId)return error(['error' => '参数错误']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        $leftTime = TIME - $session->examSessionStartTime;
        if($leftTime < 0 )
        {
            $leftTime = 0;
        }
        return ['time' => $leftTime];
    }

    public function Drop(): array | Error
    {
        $sessionId = $this->request->sessionId??null;
        if(!$sessionId)return error(['error' => '参数错误']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        if($session->examSessionPassport == $this->request->getUser()->userpassport)
        {
            $session->delete();
            return [];
        }
        return error(['error' => '错误的参数请求']);
    }

    public function Recover(): array | Error
    {
        $sessionId = $this->request->sessionId??null;
        if(!$sessionId)return error(['error' => '参数错误']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        if($session->examSessionPassport == $this->request->getUser()->userpassport)
        {
            $token = md5($session->examSessionId.'-'.Auth::getSessionSalt());
            if($session->examSessionToken == $token)
            {
                return [];
            }
            else
            {
                $session->examSessionToken = $token;
                if($session->save())return [];
                else return error(['error' => '参数错误']);
            }
        }
        else return error(['error' => '参数错误']);
    }

    public function Save(): array | Error
    {
        $sessionId = $this->request->sessionId??null;
        if(!$sessionId)return error(['error' => '参数错误']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        $token = md5($session->examsessionid.'-'.Auth::getSessionSalt());
        if($token == $session->examSessionToken)
        {
            $session->examSessionUserAnswer = json_encode($this->request->userAnswer??[],JSON_UNESCAPED_UNICODE);
            $session->examSessionSign = json_encode($this->request->signs??[],JSON_UNESCAPED_UNICODE);
            $session->save();
        }
        if($this->basic->basicfacetime && $this->basic->basicexam['model'] == 2)
        {
            if(TIME - $this->session->esfadtime >= $this->basic->basicfacetime * 60)
            {
                return ['faceVerify' => 1];
            }
        }
        return [];
    }

    public function Finish(): array | Error
    {
        if($this->basic->basicexam['model'] == 2){
            if($this->basic->basicexam['examnumber'] > 0)
            {
                $user = $this->request->getUser();
                $historyNumber = \PHPEMS\App\Exam\Service\Model\History::getQuery()->where('ehtype',2)
                    ->where('ehpassport',$user->userpassport)
                    ->where('ehbasicid',$this->basic->basicid)
                    ->count();
                if($historyNumber >= $this->basic->basicexam['examnumber'])
                {
                    return error(['error' => '考试次数已用完']);
                }
            }
        }
        $sessionId = $this->request->sessionId??null;
        if(!$sessionId)return error(['error' => '参数错误，请联系监考老师！']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        $token = md5($session->examsessionid.'-'.Auth::getSessionSalt());
        if($token == $session->examSessionToken)
        {
            $session->examSessionUserAnswer = $this->request->userAnswer??[];
            $session->examSessionSetting = json_decode($session->examSessionSetting,true);
            $session->examSessionQuestion = json_decode($session->examSessionQuestion,true);
            $session->examSessionFaceList = json_decode($session->examSessionFaceList,true);
            $result = ExamService::finishExamSession($session);
            if($result instanceof Error)return $result;
            return ['ehid' => $result];
        }
        return error(['error' => '参数错误，请联系监考老师！']);
    }

    public function Delete(): array | Error
    {
        $sessionId = $this->request->sessionid??null;
        $token = $this->request->token??null;
        if(!$sessionId || !$token)return error(['error' => '参数错误']);
        $user = $this->request->getUser();
        $result = ExamPaperSession::getQuery()->where('sessionid', $sessionId)
            ->where('examsessiontoken', $token)
            ->where('examsessionpassport', $user->userpassport)
            ->where('examsessiontype',$this->basic->basicexam['model'] === 2?:1)
            ->delete();
        if($result)return ['msg' => '删除成功'];
        else return error(['error' => '操作失败']);
    }

    public function FaceVerify(): array | Error
    {
        if(!$this->basic->basicfacetime)return ['faceVerify' => 1];
        $user = $this->request->getUser();
        $face = $this->request->face??null;
        if(!$face)return error(['error' => '未上传照片']);
        $member = Member::findByPassport($user->userpassport);
        if(!file_exists($member->mphoto))return error(['error' => '用户未认证照片']);
        $source = base64_encode(file_get_contents($member->mphoto));
        if(!FaceProvider::FaceComparison($face,$source))error(['error' => '人脸识别校验失败']);
        else
        {
            try{
                $sessionId = $this->request->sessionid??null;
                $session = ExamPaperSession::findBySessionId($sessionId);
                $token = md5($session->examsessionid.'-'.Auth::getSessionSalt());
                if($token == $session->examSessionToken)
                {
                    $examSessionFaceList = json_decode($session->examSessionFaceList,true)??[];
                    $imagePath = FaceProvider::PreventCheatingAndSave($face,$examSessionFaceList);
                    $examSessionFaceList[] = $imagePath;
                    $session->examSessionFaceList = json_encode($examSessionFaceList,JSON_UNESCAPED_UNICODE);
                    $this->session->esfadtime = TIME;
                    $this->session->save();
                }
                else throw new Exception('人脸识别校验失败');
            }catch (Exception $e){
                return error(['error' => '人脸识别校验失败']);
            }
        }
        $this->session->psfadtime = TIME;
        $this->session->save();
        return [];
    }

    public function Data(): array | Error
    {
        $user = $this->request->getUser();
        $data = ExamPaperSession::getQuery()->orderBy('esid','DESC')
            ->select(['esid','examsessionid','examsession','examsessionstarttime','examsessiontoken','examsessionsetting'])
            ->where('examsessiontype',$this->basic->basicexam['model'] === 2?:1)
            ->where('examsessionplanid',$this->plan->planid)
            ->where('examsessionpassport',$user->userpassport)
            ->where('examsessionbasicid',$this->basic->basicid)
            ->first();
        return $data??[];
    }

    public function Draw(): array | Error
    {
        $paperId = $this->request->paperId??null;
        if(!$paperId)
        {
            if($this->basic->basicexam['model'] == 2 && $this->basic->basicexam['selectrule'] == 0)
            {
                $ids = $this->basic->basicexam['self'];
                if(empty($ids))return error(['error' => '后台配置错误']);
                $paperId = $ids[array_rand($ids)];
            }
            else return error(['error' => '参数错误']);
        }
        $user = $this->request->getUser();
        $examSessionFaceList = [];
        if($this->basic->basicexam['model'] == 2){
            if(!$this->basic->intime)return error(['error' => '不在考试时间段']);
            if($this->basic->basicexam['examnumber'] > 0)
            {
                $historyNumber = \PHPEMS\App\Exam\Service\Model\History::getQuery()->where('ehtype',2)
                    ->where('ehpassport',$user->userpassport)
                    ->where('ehbasicid',$this->basic->basicid)
                    ->count();
                if($historyNumber >= $this->basic->basicexam['examnumber'])
                {
                    return error(['error' => '考试次数已用完']);
                }
            }
            if($this->basic->basicfacetime > 0)
            {
                $face = $this->request->face??null;
                if(!$face)return error(['error' => '未上传照片']);
                $member = Member::findByPassport($user->userpassport);
                if(!file_exists($member->mphoto))return error(['error' => '用户未认证照片'.$member->mphoto]);
                $source = base64_encode(file_get_contents($member->mphoto));
                if(!FaceProvider::FaceComparison($face,$source))error(['error' => '人脸识别校验失败']);
                else
                {
                    try{
                        $face = explode('base64,',$face,2);
                        $face = $face[1];
                        $imagePath = FaceProvider::PreventCheatingAndSave($face,[]);
                        $examSessionFaceList[] = $imagePath;
                        $this->session->esfadtime = TIME;
                        $this->session->save();
                    }catch (Exception $e){
                        return error(['error' => '人脸识别校验失败']);
                    }
                }
            }
        }
        $paper = ExamPaper::find($paperId);
        if(!$paper->examid)return error(['error' => '试卷配置错误']);
        $paper->examSetting = json_decode($paper->examSetting,true);
        $paper->examQuestions = json_decode($paper->examQuestions,true);
        $paper->examScore = json_decode($paper->examScore,true);
        $session = [];
        $session['examsessionfacelist'] = $examSessionFaceList;
        $session['examsessionpaperid'] = $paperId;
        $session['examsessionpassport'] = $user->userpassport;
        $session['examsessionplanid'] = 0;
        $session['examsessionbasicid'] = $this->basic->basicid;
        $session['examsessiontype'] = match($this->basic->basicexam['model']){
            2 => 2,
            default => 1,
        };
        $session['examsession'] = $paper->exam;
        $session['examsessionstarttime'] = TIME;
        $session['examsessionid'] = bin2hex(random_bytes(32));
        $session['examsessiontoken'] = md5($session['examsessionid'].'-'.Auth::getSessionSalt());
        $session['examsessionsetting'] = json_encode([
            'totalScore' => $paper->examTotalScore,
            'totalTime' => $paper->examTotalTime,
            'passMark' => $paper->examPassMark,
            'questionTypeSort' => $paper->examSetting['lite'],
            'scaleModel' => $paper->examScaleModel,
            'type' => $paper->examType,
            'questionTypes' => $paper->examSetting['questionTypes'],
            'questionScore' => $paper->examScore??[],
        ],JSON_UNESCAPED_UNICODE);
        $pointIds = array_merge(...$this->basic->basicpoint)??[];
        if($paper->examType == 2)
        {
            $session['examsessionquestion'] = ExamService::drawSelfPaper($paper,$pointIds);
        }
        else
        {
            $session['examsessionquestion'] = ExamService::drawPaper($paper,$pointIds);
        }
        $session['examsessionquestion'] = json_encode($session['examsessionquestion'],JSON_UNESCAPED_UNICODE);
        $session['examsessionip'] = Env::getClientIp();
        $session['examsessionclient'] = Env::getClientEnv();
        $session = ExamPaperSession::fillWithInit($session);
        $result = $session->toValidate();
        if($result !== true)return $result;
        if($session->save())return ['sessionid' => $session->examSessionId];
        else return error(['error' => '参数错误']);
    }

    public function index():array | Error
    {
        $sessionId = $this->request->sessionId??null;
        if(!$sessionId)return error(['error' => '参数错误']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        $token = md5($session->examsessionid.'-'.Auth::getSessionSalt());
        if($token == $session->examSessionToken)
        {
            $session->examSessionSetting = json_decode($session->examSessionSetting,true);
            $session->examSessionSign = json_decode($session->examSessionSign,true);
            $session->examSessionQuestion = json_decode($session->examSessionQuestion,true);
            $session->examSessionUserAnswer = json_decode($session->examSessionUserAnswer,true);
            $session->examSessionTimeList = json_decode($session->examSessionTimeList,true);
            $basicExam = $this->basic->basicexam;
            if($basicExam['model'] == 2)$basicExam['template'] = $basicExam['selftemplate'];
            else $basicExam['template'] = $basicExam['autotemplate'];
            $session->basicExam = $basicExam;
            $formatedQuestions = [];
            foreach($session->examSessionQuestion as $key => $sessionQuestions)
            {
                if(!empty($sessionQuestions['questions']) && is_array($sessionQuestions['questions']))
                {
                    $tmp = $sessionQuestions['questions'];
                    if($basicExam['changesequence'])shuffle($tmp);
                    $formatedQuestions[$key] = [...$tmp];
                }
                if(!empty($sessionQuestions['rowsQuestions']) && is_array($sessionQuestions['rowsQuestions']) )
                {
                    $tmp = $sessionQuestions['rowsQuestions'];
                    if($basicExam['changesequence'])shuffle($tmp);
                    foreach($tmp as $rows)
                    {
                        $parent = $rows['question'];
                        foreach($rows['children'] as $index => $question)
                        {
                            $question['parent'] = $parent;
                            $question['childindex'] = $index;
                            $formatedQuestions[$key][] = $question;
                        }
                    }
                }
            }
            $session->examSessionQuestion = $formatedQuestions;
            return $session->getRaw();
        }
        else return error(['error' => '参数错误']);
    }
}