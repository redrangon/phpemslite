<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamPaperSession;
use PHPEMS\App\Exam\Service\Model\HistoryDetail;
use PHPEMS\App\Exam\Service\Model\HistoryLog;
use PHPEMS\App\Member\Service\Model\Member;
use PHPEMS\Lib\AI\AIClient;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Office\Xlsx\Writer;

class History extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'delete' => 'Delete',
            'detail' => 'Detail',
            'face' => 'Face',
            'log' => 'Log',
            'export' => 'Export',
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

    private function buildSearchQuery(QueryBuilder $query ,array $search):QueryBuilder
    {
        if($search['ehpassport']??false) {
            $query->where('ehpassport', $search['ehpassport']);
        }
        if($search['range']??false) {
            if($search['range'][0]??false)$query->where('ehstarttime','>=', strtotime($search['range'][0]));
            if($search['range'][1]??false)$query->where('ehstarttime','<=', strtotime($search['range'][1]));
        }
        if($search['minscore']??false) {
            $query->where('ehscore','>=', $search['minscore']);
        }
        if($search['maxscore']??false) {
            $query->where('ehscore','<=', $search['maxscore']);
        }
        if(isset($search['ehstatus'])) {
            $query->where('ehstatus', $search['ehstatus']);
        }

        if($search['ehtype']??false) {
            $query->where('ehtype', $search['ehtype']);
        }
        return $query;
    }

    public function Log():array|Error
    {
        $ehId = $this->request->ehId;
        if(!$ehId)return error(['error' => '错误的参数']);
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $data = HistoryLog::getQuery()->orderBy('ehlid','DESC')
            ->where('ehlehid', $ehId)
            ->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['ehltime'] = date('Y-m-d H:i:s',$item['ehltime']);
        });
        return $data;
    }

    public function Face():array|Error
    {
        $ehId = $this->request->ehId;
        if(!$ehId)return error(['error' => '错误的参数']);
        $history = \PHPEMS\App\Exam\Service\Model\History::find($ehId);
        if(!$history->ehid)return error(['error' => '错误的参数']);
        $face = HistoryDetail::findByEhId($ehId);
        $face = $face->ehdFaceList?json_decode($face->ehdFaceList,true):[];
        $planId = $history->ehPlanId;
        $passport = $history->ehPassport;
        $member = Member::getQuery()->select(['mname','mphoto'])
            ->where('mpassport',$passport)
            ->first();
        if(!$member)return error(['error' => '未找到相关用户信息']);
        return [
            'pmpassport' => $passport,
            'pmname' => $member['mname'],
            'pmphoto' => $member['mphoto'],
            'ehstartip' => $history->ehStartIp,
            'ehendip' => $history->ehEndIp,
            'ehstartclient' => $history->ehStartClient,
            'ehendclient' => $history->ehEndClient,
            'ehfaces' => $face
        ];
    }

    public function Export():void
    {
        $query = \PHPEMS\App\Exam\Service\Model\History::getQuery()->select(['ehpassport','ehscore','ehstarttime','ehtime','ehneedresit'])->orderBy('ehid', 'DESC');
        $search = $this->request->search;
        if($search) {
            $query = $this->buildSearchQuery($query,$search);
        }
        $basicId = $this->request->basicId??null;
        $query->where('ehbasicid', $basicId);
        $histories = $query->get();
        array_walk($histories,function(&$item){
            $item['ehstarttime'] = date('Y-m-d H:i:s',$item['ehstarttime']);
            $item['ehtime'] = intval($item['ehtime'] / 60) .'分'. intval($item['ehtime'] % 60) .'秒';
            $item['ehneedresit'] = $item['ehneedresit'] ? '作废' : '正常';
        });
        $histories = [
            ['身份证号','考试得分','考试开始时间','考试用时','状态'],
            ...$histories
        ];
        $writer = new Writer();
        $writer->writeSheet($histories,'考试记录');
        $writer->writeToStdOut();
        exit();
    }

    public function Delete():array|Error
    {
        $ehId = $this->request->ehId;
        $reason = $this->request->reason;
        if($ehId && $reason)
        {
            $user = $this->request->getUser();
            try{
                \PHPEMS\App\Exam\Service\Model\History::getDB()->transaction(function() use($ehId,$reason,$user){
                    \PHPEMS\App\Exam\Service\Model\History::getQuery()->where('ehid', $ehId)->update(['ehneedresit' => 1]);
                    $log = [
                        'ehlehid' => $ehId,
                        'ehlusername' => $user->userName,
                        'ehltype' => 1,
                        'ehlinfo' => $reason,
                        'ehltime' => TIME
                    ];
                    $log = HistoryLog::fillWithInit($log);
                    $log->save();
                });
            }catch (\Exception $e){
                return error(['error' => $e->getMessage()]);
            }
            return ['msg' => '撤销成功'];
        }
        return error(['error' => '未选择要撤销的记录']);
    }

    public function Data():array|Error
    {
        $query = \PHPEMS\App\Exam\Service\Model\History::getQuery()->orderBy('ehid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $basicId = $this->request->basicId??null;
        $query->where('ehbasicid', $basicId);
        $search = $this->request->search;
        if($search) {
            $query = $this->buildSearchQuery($query,$search);
        }
        $histories = $query->paginate($page, $limit);
        $passports = [];
        foreach($histories['data'] as $item) {
            $passports[] = $item['ehpassport'];
        }
        $passports = array_unique($passports);
        $data = Member::getQuery()->select(['mpassport','mname'])
            ->whereIn('mpassport', $passports)->get();
        $members = [];
        foreach($data as $item) {
            $members[$item['mpassport']] = $item;
        }
        if($histories['total'] > 0) {
            array_walk($histories['data'], function (&$item) use ($members){
                $item['ehendtime'] = $item['ehendtime'] > 0 ? date('Y-m-d H:i:s', $item['ehendtime']) : '';
                $item['ehstarttime'] = $item['ehstarttime'] > 0 ? date('Y-m-d H:i:s', $item['ehstarttime']) : '';
                $item['ehdecidetime'] = $item['ehdecidetime'] > 0 ? date('Y-m-d H:i:s', $item['ehdecidetime']) : '';
                $item['mname'] = $members[$item['ehpassport']]['mname']??'';
            });
        }
        return $histories;
    }

    public function Detail():array|Error
    {
        $ehId = $this->request->ehId;
        $history = \PHPEMS\App\Exam\Service\Model\History::find($ehId);
        $detail = HistoryDetail::findByEhId($ehId);
        $detail->ehdscores = json_decode($detail->ehdscores,true);
        $detail->ehdsetting = json_decode($detail->ehdsetting,true);
        $detail->ehdquestion = json_decode($detail->ehdquestion,true);
        $detail->ehdanswer = json_decode($detail->ehdanswer,true);
        $formatedQuestions = [];
        foreach($detail->ehdquestion as $key => $sessionQuestions)
        {
            if(!empty($sessionQuestions['questions']) && is_array($sessionQuestions['questions']))
            {
                $formatedQuestions[$key] = [...$sessionQuestions['questions']];
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
                    }
                }
            }
        }
        $detail->ehdquestion = $formatedQuestions;
        $history->detail = $detail->getRaw();
        return $history->getRaw();
    }

    public function Index(): Error|array
    {
        return $basic??[];
    }
}
