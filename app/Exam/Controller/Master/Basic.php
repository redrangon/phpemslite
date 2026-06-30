<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\ExamPaperSession;
use PHPEMS\App\Exam\Service\Model\History;
use PHPEMS\App\Exam\Service\Model\HistoryDetail;
use PHPEMS\Lib\AI\AIClient;

use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Basic extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'session' => 'Session',
            'force' => 'ForceSave',
            'decide' => 'Decide',
            'ai' => 'AI'
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

    public function AI():array|Error
    {
        $questionString = json_encode([
            'parent' => $this->request->parent??null,
            'question' => $this->request->question??null,
            'questionanswer' => $this->request->questionanswer??null,
            'answer' => $this->request->answer??null,
            'maxscore' => $this->request->maxscore??0,
        ], JSON_UNESCAPED_UNICODE);
        $aiClient = new AIClient();
        $prompt = <<<prompt
你是一个考题的解答专家，请根据考题的描述，给出考题的答案。
JSON格式考题描述：{$questionString}；
其中 parent 为当试题为材料题时的材料内容，没有材料时为null；
question为试题题干；
questionasnwer为标准答案内容；
answer为用户答案；
maxscore为试题总分；
请评判该题得分，得分范围为0-试题总分；
请以“综上所属，该题最后得分为：试题得分”结尾。
prompt;
;
        $result = $aiClient->ask($prompt);
        return [$result];
    }

    public function Decide():array|Error
    {
        $ehId = $this->request->ehid??null;
        $examScore = $this->request->examScore??[];
        if(!$ehId || empty($examScore))return error(['error' => '参数错误！']);
        $history = History::find($ehId);
        if(!$history->ehid)return error(['error' => '记录不存在']);
        $detail = HistoryDetail::findByEhId($ehId);
        if(!$detail->ehdid)return error(['error' => '记录错误']);
        $setting = json_decode($detail->ehdsetting,true)??[];
        $scores = json_decode($detail->ehdscores,true)??[];
        foreach($examScore as $questionid => $score)
        {
            if(isset($setting['questionScore'][$questionid]))
            {
                if($score > $setting['questionScore'][$questionid])$score = $setting['questionScore'][$questionid];
                $scores[$questionid] = $score;
            }
        }
        $detail->ehdscores = json_encode($scores, JSON_UNESCAPED_UNICODE);
        $history->ehscore = array_sum($scores);
        if($history->ehscore >= $setting['passMark'])
        {
            $history->ehispass = 1;
        }
        else $history->ehispass = 0;
        $history->ehteacher = $this->request->getUser()->username;
        $history->ehdecidetime = TIME;
        $history->ehstatus = 1;
        try{
            History::getDB()->transaction(function () use ($detail, $history) {
                $detail->save();
                $history->save();
            });
            return [];
        }catch (\Exception $e)
        {
            return error(['error' => '保存失败']);
        }
    }

    public function ForceSave():array|Error
    {
        $sessionId = $this->request->sessionid??null;
        if(!$sessionId)return error(['error' => '参数错误！']);
        $session = ExamPaperSession::findBySessionId($sessionId);
        $session->examSessionUserAnswer = json_decode($session->examSessionUserAnswer,true);
        $session->examSessionSetting = json_decode($session->examSessionSetting,true);
        $session->examSessionQuestion = json_decode($session->examSessionQuestion,true);
        $session->examSessionFaceList = json_decode($session->examSessionFaceList,true);
        $result = ExamService::finishExamSession($session);
        if($result instanceof Error)return $result;
        return ['ehid' => $result];
    }

    public function Session():array|Error
    {
        $basicId = $this->request->basicId??null;
        if(!$basicId)return error(['error' => '错误的参数']);
        $page = $this->request->page??1;
        $limit = $this->request->limit??20;
        $search = $this->request->search??null;
        $query = ExamPaperSession::getQuery()->select(['esid','examsessionid','examsession','examsessionpassport','examsessionstarttime','examsessionip','examsessionclient'])
            ->where('examsessionbasicid', $basicId)
            ->where('examsessiontype',2);
        if($search['passport']??false)$query->where('ehpassport',$search['passport']);
        $data = $query->paginate($page,$limit);
        array_walk($data['data'],function(&$item){
            $item['examsessionstarttime'] = date('Y-m-d H:i:s',$item['examsessionstarttime']);
        });
        return $data;
    }

    /**
     * 添加操作
     */
    public function Add(): Error | array
    {
        // 获取请求数据
        $data = [
            'basic' => $this->request->basic??null,
            'basicthumb' => $this->request->basicthumb??null,
            'basicsubjectid' => $this->request->basicsubjectid??null,
            'basicdescribe' => $this->request->basicdescribe??"",
            'basictext' => $this->request->basictext??"",
        ];
        $model = \PHPEMS\App\Exam\Service\Model\Basic::fillWithInit($data);
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
        $basicId = $this->request->basicid;
        $model = \PHPEMS\App\Exam\Service\Model\Basic::find($basicId);
        if(!$model->basicid) return error(['error' => '记录不存在']);
        $data = [
            'basic' => $this->request->basic??null,
            'basicsubjectid' => $this->request->basicsubjectid??null,
            'basicthumb' => $this->request->basicthumb??null,
            'basicdescribe' => $this->request->basicdescribe??null,
            'basicpoint' => $this->request->basicpoint??null,
            'basicexam' => $this->request->basicexam??null,
            'basictext' => $this->request->basictext??null,
        ];
        $data = array_filter($data,function($item){
            return $item !== null;
        });
        foreach ($data as $key => $value) {
            if(in_array($key, ['basicpoint','basicexam'])){
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
        \PHPEMS\App\Exam\Service\Model\Basic::getQuery()->whereIn('basicid', $ids)->delete();

        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = \PHPEMS\App\Exam\Service\Model\Basic::getQuery()->select(['basicid','basic','basicnumber','basicsubjectid','basicthumb','basicdescribe'])->orderBy('basicid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['basicid']??false)$query->where('basicid', $search['basicid']);
            if($search['keyword']??false)$query->where('basic', 'like', '%'.$search['keyword'].'%');
            if($search['basicsubjectid']??false)$query->where('basicsubjectid', $search['basicsubjectid']);
        }
        $data = $query->paginate($page, $limit);
        return $data;
    }

    public function Index(): Error|array
    {
        $basicid = $this->request->basicid;
        $basic = \PHPEMS\App\Exam\Service\Model\Basic::find($basicid)->getRaw();
        if($basic??false){
            $basic['basicpoint'] = json_decode($basic['basicpoint'])??(object)[];
            $basic['basicexam'] = json_decode($basic['basicexam'])??(object)[];
        }
        return $basic??[];
    }
}
