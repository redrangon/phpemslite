<?php

namespace PHPEMS\App\Exam\Service;

use PHPEMS\App\Exam\Controller\Master\Paper;
use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamMember;
use PHPEMS\App\Exam\Service\Model\ExamPaper;
use PHPEMS\App\Exam\Service\Model\ExamPaperSession;
use PHPEMS\App\Exam\Service\Model\Favor;
use PHPEMS\App\Exam\Service\Model\History;
use PHPEMS\App\Exam\Service\Model\HistoryDetail;
use PHPEMS\App\Exam\Service\Model\Point;
use PHPEMS\App\Exam\Service\Model\Question;
use PHPEMS\App\Exam\Service\Model\QuestionType;
use PHPEMS\App\Exam\Service\Model\Relation;
use PHPEMS\App\Exam\Service\Model\Section;
use PHPEMS\App\Exam\Service\Model\Subject;
use PHPEMS\App\Plan\Controller\App\Member;
use PHPEMS\App\Plan\Service\Model\PlanMember;
use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Env;

class ExamService
{
    public static function getBasicQueryWithMember():QueryBuilder
    {
        $memberTable = ExamMember::getTableName();
        $basicKey = Basic::getPrimaryKey();
        return Basic::getQuery()->join($memberTable, 'embasicid', '=', $basicKey);
    }
    public static function getExamHistoryQueryWithDetail():QueryBuilder
    {
        $detailTable = HistoryDetail::getTableName();
        $detailKey = HistoryDetail::getPrimaryKey();
        return History::getQuery()->join($detailTable, 'ehid', '=', $detailKey);
    }

    public static function getFavorQueryWithQuestion():QueryBuilder
    {
        $questionTable = Question::getTableName();
        $questionKey = Question::getPrimaryKey();
        return Favor::getQuery()->join($questionTable, 'favorquestionid', '=', $questionKey);
    }

    public static function getSubjectPaperNumber(int $subjectId):int
    {
        return ExamPaper::getQuery()->where('examsubject', '=', $subjectId)->count('1');
    }

    public static function getPaperQueryWithSubject():QueryBuilder
    {
        $subjectTable = Subject::getTableName();
        $subjectKey = Subject::getPrimaryKey();
        return ExamPaper::getQuery()->join($subjectTable, 'examsubject', '=', $subjectKey)->orderBy('examid', 'DESC');
    }

    public static function getSubjectPointIds(int $subjectId):array
    {
        $points = self::getPointQueryWithSection()->select(['pointid'])->where('sectionsubjectid', '=', $subjectId)->get();
        $ids = [];
        foreach ($points as $point) {
            $ids[] = $point['pointid'];
        }
        return $ids;
    }

    public static function getPointQueryWithSection():QueryBuilder
    {
        $sectionTable = Section::getTableName();
        $sectionKey = Section::getPrimaryKey();
        return Point::getQuery()->join($sectionTable, 'pointsectionid', '=', $sectionKey);
    }

    public static function getSubjectQuestionNumber(int $subjectId):int
    {
        $numbers = self::getPointQueryWithSection()->select(['pointnumber'])->where('sectionsubjectid', '=', $subjectId)->get();
        $questionNumber = 0;
        foreach ($numbers as $number) {
            $number = json_decode($number['pointnumber'],true)??[];
            $number = array_values($number);
            $questionNumber += array_sum($number);
        }
        return $questionNumber;
    }

    public static function setQuestionRelation(array $points = [], int $questionId = 0):bool|Error
    {
        $relations = Relation::getQuery()->select(['qkid','qkpointid'])->where('qkquestionid', '=', $questionId)->get();
        $relationPoints = [];
        foreach ($relations as $relation) {
            $relationPoints[] = $relation['qkpointid'];
        }
        $points = array_values(array_diff($points, $relationPoints));
        try{
            Relation::getDB()->transaction(function() use($points, $questionId) {
                foreach ($points as $point) {
                    $item = [
                        'qkpointid' => $point,
                        'qkquestionid' => $questionId,
                        'qkstatus' => 1
                    ];
                    Relation::getQuery()->insert($item);
                }
            });
        }catch (\Exception $e){
            return error(['error' => $e->getMessage()]);
        }
        return true;
    }

    public static function formatQuestion(array $data, array $raw = []):array
    {
        if(empty($raw))
        {
            if($data['questionselect'])
            {
                $selector = explode("\n",trim($data['questionselect']));
                $data['questionselectnumber'] = count($selector);
                $data['questionhtml'] = json_encode($selector,JSON_UNESCAPED_UNICODE);
            }
        }
        elseif($data['questionselect']??false)
        {
            if($data['questionselect'] !== $raw['questionselect'])
            {
                $selector = explode("\n",trim($data['questionselect']));
                $data['questionselectnumber'] = count($selector);
                $data['questionhtml'] = json_encode($selector,JSON_UNESCAPED_UNICODE);
            }
            else
            {
                $data['questionselectnumber'] = null;
                $data['questionhtml'] = null;
            }
        }
        if(is_array($data['questionanswer']))
        {
            sort($data['questionanswer']);
            $data['questionanswer'] = implode('',$data['questionanswer']);
        }
        return $data;
    }

    public static function getExamHistoryWithDetailById(int $ehId):array | Error
    {
        $data = History::find($ehId)->getRaw();
        if($data) {
            $data['ehstarttime'] = $data['ehstarttime'] > 0 ? date('Y-m-d H:i:s', $data['ehstarttime']) : '';
            $data['ehendtime'] = $data['ehendtime'] > 0 ? date('Y-m-d H:i:s', $data['ehendtime']) : '';
            $data['ehdecidetime'] = $data['ehdecidetime'] > 0 ? date('Y-m-d H:i:s', $data['ehdecidetime']) : '';
            $detail = HistoryDetail::findByEhid($ehId)->getRaw();
            $detail['ehdscores'] = json_decode($detail['ehdscores'], true);
            $detail['ehdsetting'] = json_decode($detail['ehdsetting'], true);
            $detail['ehdquestion'] = json_decode($detail['ehdquestion'], true);
            $detail['ehdanswer'] = json_decode($detail['ehdanswer'], true);
            $detail['ehdtimes'] = json_decode($detail['ehdtimes'], true);
            $data = [...$data, ...$detail];
        }
        return empty($data)?error(['error' => '不存在的记录']):$data;
    }

    static public function refreshPointCache(int $pointId):bool
    {
        $point = Point::find($pointId);
        $data = self::getQuestionQueryWithRelation()->select(['questionid','questiontype','questionchildnumber','questionlevel'])
            ->where('qkpointid',$pointId)
            ->where('questionparent',0)
            ->get();
        $number = [];
        $ids = [];
        foreach ($data as $item) {
            if(!isset($number[$item['questiontype']]))$number[$item['questiontype']] = 0;
            $number[$item['questiontype']] += $item['questionchildnumber'];
            $ids[$item['questiontype']][$item['questionlevel']][] = $item['questionid'];
        }
        $point->pointnumber = json_encode($number,JSON_UNESCAPED_UNICODE);
        $point->pointquestions = json_encode($ids,JSON_UNESCAPED_UNICODE);
        return (bool)$point->save();
    }

    public static function getQuestionQueryWithRelation():QueryBuilder
    {
        $questionTable = Question::getTableName();
        $questionKey = Question::getPrimaryKey();
        return Relation::getQuery()->join($questionTable, 'qkquestionid', '=', $questionKey);
    }

    static public function drawSelfPaper(ExamPaper $paper, array $pointRange):array|Error
    {
        $data = [];
        $questionIds = array_column($paper->examQuestions,'questions');
        $rowIds = array_column($paper->examQuestions,'rowsquestions');
        $questionIds = array_merge(...$questionIds);
        $rowIds = array_merge(...$rowIds);
        $totalIds = array_merge($questionIds,$rowIds);
        $relations = Relation::getQuery()->select(['qkquestionid','qkpointid'])
            ->whereIn('qkquestionid',$totalIds)
            ->whereIn('qkpointid',$pointRange)
            ->get();
        $questionRelations = [];
        foreach($relations as $relation)
        {
            $questionRelations[$relation['qkquestionid']][] = $relation['qkpointid'];
        }
        $questions = Question::getQuery()->select(['questionid','questiontype','question','questionhtml','questionlevel','questionisparent','questionanswer'])
            ->whereIn('questionid',array_merge($questionIds,$rowIds))
            ->where('questionparent',0)
            ->get();
        $children = Question::getQuery()->select(['questionid','questiontype','question','questionhtml','questionlevel','questionparent','questionanswer'])
            ->whereIn('questionparent',$rowIds)
            ->orderBy('questionparent','ASC')
            ->orderBy('questionsequence','ASC')
            ->get();
        $tmpChildren = [];
        foreach($children as $child)
        {
            $child['questionhtml'] = json_decode($child['questionhtml'],true);
            $tmpChildren[$child['questionparent']][] = $child;
        }
        foreach($questions as $question)
        {
            $question['points'] = $questionRelations[$question['questionid']]??[];
            if($question['questionisparent'])
            {
                $question['children'] = $tmpChildren[$question['questionid']]??null;
                $data[$question['questiontype']]['rowsQuestions'][] = $question;
            }
            else
            {
                $question['questionhtml'] = json_decode($question['questionhtml'],true);
                $data[$question['questiontype']]['questions'][] = $question;
            }
        }
        return $data;
    }

    static public function drawPaper(ExamPaper $paper, array $pointRange):array|Error
    {
        if($paper->examScaleModel)return self::drawScalePaper($paper);
        $points = Point::getQuery()->select(['pointid','pointquestions'])
            ->whereIn('pointid',$pointRange)
            ->get();
        array_walk($points,function(&$item){
            $item['pointquestions'] = json_decode($item['pointquestions'],true);
        });
        $ids = [];
        foreach($points as $point){
            foreach($point['pointquestions'] as $key => $questions){
                foreach($questions as $level => $question){
                    if(!isset($ids[$key][$level]))$ids[$key][$level] = [];
                    $ids[$key][$level] = [...$ids[$key][$level],...$question];
                }
            }
        }
        $questions = [];
        $existsIds = [];
        foreach($paper->examSetting['questionTypes'] as $key => $questionType){
            if($questionType['number'])
            {
                $questions[$key] = [
                    'questions' => [],
                    'rowsQuestions' => [],
                ];
                $numbers = [1 => $questionType['easyNumber'],$questionType['middleNumber'],$questionType['hardNumber']];
                foreach($numbers as $level => $number)
                {
                    if($number < 1)continue;
                    $ids[$key][$level] = array_diff($ids[$key][$level],$existsIds);
                    if(count($ids[$key][$level]) <= $number)
                    {
                        $tmpIds = $ids[$key][$level];
                    }
                    else
                    {
                        $tmpIds = array_rand($ids[$key][$level],$number);
                        if(!is_array($tmpIds))$tmpIds = [$tmpIds];
                    }
                    $relations = Relation::getQuery()->select(['qkquestionid','qkpointid'])
                        ->whereIn('qkquestionid',$tmpIds)
                        ->whereIn('qkpointid',$pointRange)
                        ->get();
                    $questionRelations = [];
                    foreach($relations as $relation)
                    {
                        $questionRelations[$relation['qkquestionid']][] = $relation['qkpointid'];
                    }
                    $tmpQuestions = Question::getQuery()->select(['questionid','questiontype','question','questionhtml','questionisparent','questionanswer'])
                        ->whereIn('questionid',$tmpIds)
                        ->get();
                    $levelNumber = 0;
                    foreach($tmpQuestions as $question){
                        $question['points'] = $questionRelations[$question['questionid']]??[];
                        if($question['questionisparent'])
                        {
                            $existsIds[] = $question['questionid'];
                            $children = Question::getQuery()->select(['questionid','questiontype','question','questionhtml','questionlevel','questionparent','questionanswer'])
                                ->where('questionparent',$question['questionid'])
                                ->orderBy('questionsequence','ASC')
                                ->get();
                            if(($levelNumber + count($children)) < $number)
                            {
                                array_walk($children,function(&$item){
                                    $item['questionhtml'] = json_decode($item['questionhtml'],true);
                                });
                                $question['children'] = $children;
                            }
                            else
                            {
                                $question['children'] = [];
                                foreach($children as $child){
                                    $child['questionhtml'] = json_decode($child['questionhtml'],true);
                                    $question['children'][] = $child;
                                    $levelNumber++;
                                    if($levelNumber == $number)break;
                                }
                            }
                            $questions[$key]['rowsQuestions'][] = $question;
                        }
                        else
                        {
                            $question['questionhtml'] = json_decode($question['questionhtml'],true);
                            $questions[$key]['questions'][] = $question;
                            $existsIds[] = $question['questionid'];
                            $levelNumber++;
                            if($levelNumber == $number)break;
                        }
                    }
                }
            }
        }
        return $questions;
    }

    static private function drawScalePaper(ExamPaper $paper):array|Error
    {
        $questions = [];
        $existsIds = [];
        $points = [];
        foreach($paper->examSetting['questionTypes'] as $key => $questionType){
            if($questionType['number'])
            {
                $questions[$key] = [
                    'questions' => [],
                    'rowsQuestions' => [],
                ];
                if(isset($paper->examSetting['examScale'][$key]) && $paper->examSetting['examScale'][$key])
                {
                    $scales = explode("\n",$paper->examSetting['examScale'][$key]);
                    $scales = array_filter($scales,function($item){
                        return trim($item);
                    });
                    array_walk($scales,function(&$item){
                        $item = explode(":",$item);
                    });
                    foreach($scales as $scale)
                    {
                        $pointIds = explode(",",$scale[0]);
                        $ids = [];
                        foreach($pointIds as $pointId)
                        {
                            if(!isset($points[$pointId]))
                            {
                                $points[$pointId] = Point::find($pointId)->getRaw();
                                $points[$pointId]['pointquestions'] = json_decode($points[$pointId]['pointquestions'],true);
                            }
                            $point = $points[$pointId];
                            if(!empty($point['pointquestions'][$key]))
                            {
                                foreach($point['pointquestions'][$key] as $level => $question){
                                    if(!isset($ids[$level]))$ids[$level] = [];
                                    $ids[$level] = [...$ids[$level],...$question];
                                }
                            }
                        }
                        $numbers = explode(",",$scale[2]);
                        foreach($numbers as $sub => $number)
                        {
                            if($number < 1)continue;
                            $level = $sub + 1;
                            if(isset($ids[$level]))
                            {
                                $ids[$level] = array_diff($ids[$level],$existsIds);
                                if(count($ids[$level]) <= $number)
                                {
                                    $tmpIds = $ids[$level];
                                }
                                else
                                {
                                    $tmpIds = array_rand($ids[$level],$number);
                                    if(!is_array($tmpIds))$tmpIds = [$tmpIds];
                                }
                                $relations = Relation::getQuery()->select(['qkquestionid','qkpointid'])
                                    ->whereIn('qkquestionid',$tmpIds)
                                    ->get();
                                $questionRelations = [];
                                foreach($relations as $relation)
                                {
                                    $questionRelations[$relation['qkquestionid']][] = $relation['qkpointid'];
                                }
                                $tmpQuestions = Question::getQuery()->select(['questionid','questiontype','question','questionhtml','questionisparent','questionanswer'])
                                    ->whereIn('questionid',$tmpIds)
                                    ->get();
                                $levelNumber = 0;
                                foreach($tmpQuestions as $question){
                                    $question['points'] = $questionRelations[$question['questionid']]??[];
                                    if($question['questionisparent'])
                                    {
                                        $existsIds[] = $question['questionid'];
                                        $children = Question::getQuery()->select(['questionid','questiontype','question','questionhtml','questionlevel','questionparent','questionanswer'])
                                            ->where('questionparent',$question['questionid'])
                                            ->orderBy('questionsequence','ASC')
                                            ->get();
                                        if(($levelNumber + count($children)) < $number)
                                        {
                                            array_walk($children,function(&$item){
                                                $item['questionhtml'] = json_decode($item['questionhtml'],true);
                                            });
                                            $question['children'] = $children;
                                        }
                                        else
                                        {
                                            $question['children'] = [];
                                            foreach($children as $child){
                                                $child['questionhtml'] = json_decode($child['questionhtml'],true);
                                                $question['children'][] = $child;
                                                $levelNumber++;
                                                if($levelNumber == $number)break;
                                            }
                                        }
                                        $questions[$key]['rowsQuestions'][] = $question;
                                    }
                                    else
                                    {
                                        $question['questionhtml'] = json_decode($question['questionhtml'],true);
                                        $questions[$key]['questions'][] = $question;
                                        $existsIds[] = $question['questionid'];
                                        $levelNumber++;
                                        if($levelNumber == $number)break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $questions;
    }

    public static function finishExamSession(ExamPaperSession $examSession):int | Error
    {
        $userAnswers = $examSession->examSessionUserAnswer;
        array_walk($userAnswers,function(&$item){
            if(is_array($item))$item = implode('',$item);
        });
        $questionScore = $examSession->examSessionSetting['questionScore'];
        $defaultScore = [];
        foreach($examSession->examSessionSetting['questionTypes'] as $key => $questionType){
            $defaultScore[$key] = $questionType['score'];
        }
        $questionTypes = QuestionType::getAll();
        $questionTypes = array_column($questionTypes,null,'questid');

        $scores = [];
        $hasSubjective = 0;
        foreach($examSession->examSessionQuestion as $key => $sessionQuestions)
        {
            if($sessionQuestions['questions']??false || $sessionQuestions['rowsQuestions']??false)
            {
                if(!$questionTypes[$key]['questsort'])
                {
                    if(!empty($sessionQuestions['questions']) && is_array($sessionQuestions['questions']))
                    {
                        foreach($sessionQuestions['questions'] as $question)
                        {
                            if(!isset($questionScore[$question['questionid']]))
                            {
                                $questionScore[$question['questionid']] = $defaultScore[$key];
                            }
                            if($userAnswers[$question['questionid']]??false)
                            {
                                if($question['questionanswer'] == $userAnswers[$question['questionid']])
                                {
                                    $scores[$question['questionid']] = $questionScore[$question['questionid']];
                                }
                                else
                                {
                                    if($questionTypes[$key]['questchoice'] == 3)
                                    {
                                        $answerLength = strlen($question['questionanswer']);
                                        $tmp = explode('',$userAnswers[$question['questionid']]);
                                        $rightLength = 0;
                                        foreach($tmp as $answer)
                                        {
                                            if(str_contains($answer,$question['questionanswer']))
                                            {
                                                $rightLength ++ ;
                                            }
                                            else
                                            {
                                                $rightLength = 0;
                                                break;
                                            }
                                        }
                                        $scores[$question['questionid']] = round($questionScore[$question['questionid']] * $rightLength / $answerLength,2);
                                    }
                                    else $scores[$question['questionid']] = 0;
                                }
                            }
                            else
                            {
                                $scores[$question['questionid']] = 0;
                            }
                        }
                    }
                }
                else
                {
                    $hasSubjective = 1;
                    foreach($sessionQuestions['questions'] as $question) {
                        if (!isset($questionScore[$question['questionid']])) {
                            $questionScore[$question['questionid']] = $defaultScore[$key];
                        }
                    }
                }
                if(!empty($sessionQuestions['rowsQuestions']) && is_array($sessionQuestions['rowsQuestions']) )
                {
                    $tmp = $sessionQuestions['rowsQuestions'];
                    foreach($tmp as $rows)
                    {
                        foreach($rows['children'] as $question)
                        {
                            if(!isset($questionScore[$question['questionid']]))
                            {
                                $questionScore[$question['questionid']] = $defaultScore[$key];
                            }
                            if(!$questionTypes[$question['questiontype']]['questsort'])
                            {
                                if($userAnswers[$question['questionid']]??false)
                                {
                                    if($question['questionanswer'] == $userAnswers[$question['questionid']])
                                    {
                                        $scores[$question['questionid']] = $questionScore[$question['questionid']];
                                    }
                                    else
                                    {
                                        if($questionTypes[$question['questiontype']]['questchoice'] == 3)
                                        {
                                            $answerLength = strlen($question['questionanswer']);
                                            $tmp = explode('',$userAnswers[$question['questionid']]);
                                            $rightLength = 0;
                                            foreach($tmp as $answer)
                                            {
                                                if(str_contains($answer,$question['questionanswer']))
                                                {
                                                    $rightLength ++ ;
                                                }
                                                else
                                                {
                                                    $rightLength = 0;
                                                    break;
                                                }
                                            }
                                            $scores[$question['questionid']] = round($questionScore[$question['questionid']] * $rightLength / $answerLength,2);
                                        }
                                        else $scores[$question['questionid']] = 0;
                                    }
                                }
                                else
                                {
                                    $scores[$question['questionid']] = 0;
                                }
                            }
                            else $hasSubjective = 1;
                        }
                    }
                }
            }
        }
        $score = round(array_sum($scores),2);
        $historyData = [
            'ehplanid' => $examSession->examSessionPlanId,
            'ehpaperid' => $examSession->examSessionPaperid,
            'ehexam' => $examSession->examSession,
            'ehtype' => $examSession->examSessionType,
            'ehbasicid' => $examSession->examSessionBasicId,
            'ehtime' => TIME - $examSession->examSessionStartTime,
            'ehscore' => $score,
            'ehpassport' => $examSession->examSessionPassport,
            'ehstarttime' => $examSession->examSessionStartTime,
            'ehendtime' => TIME,
            'ehstatus' => $hasSubjective?0:1,
            'ehneedresit' => 0,
            'ehispass' => $score >= $examSession->examSessionSetting['passMark']?1:0,
            'ehstats' => "",
            'ehstartip' => $examSession->examSessionIp,
            'ehendip' => Env::getClientIp(),
            'ehstartclient' => $examSession->examSessionClient,
            'ehendclient' => Env::getClientEnv()
        ];
        $history = History::fillWithInit($historyData);
        $examSession->examSessionSetting = [...$examSession->examSessionSetting,'questionScore' => $questionScore];
        $detailData = [
            'ehdscores' => json_encode($scores,JSON_UNESCAPED_UNICODE),
            'ehdsetting' => json_encode($examSession->examSessionSetting,JSON_UNESCAPED_UNICODE),
            'ehdquestion' => json_encode($examSession->examSessionQuestion,JSON_UNESCAPED_UNICODE),
            'ehdanswer' => json_encode($userAnswers,JSON_UNESCAPED_UNICODE),
            'ehdfacelist' => json_encode($examSession->examSessionFaceList,true),
            'ehdtimes' => json_encode([],JSON_UNESCAPED_UNICODE)
        ];
        $detail = HistoryDetail::fillWithInit($detailData);
        try {
            return History::getDB()->transaction(function () use ($history,$detail,$examSession) {
                $history->toValidate();
                $history->save();
                $ehId = $history->ehid;
                $detail->ehdEhId = $ehId;
                $detail->toValidate();
                $detail->save();
                $examSession->delete();
                return $ehId;
            });
        } catch (\Exception $e) {
            return error(['error' => $e->getMessage()]);
        }
    }

    static public function cacheExamHistoryDetail(History $history):History
    {
        $detail = HistoryDetail::findByEhId($history->ehid);
        if($detail->ehdid)
        {
            $detail->ehdscores = json_decode($detail->ehdscores,true);
            $detail->ehdsetting = json_decode($detail->ehdsetting,true);
            $detail->ehdquestion = json_decode($detail->ehdquestion,true);
            $detail->ehdanswer = json_decode($detail->ehdanswer,true);
            $ehstats = [
                'setting' => [
                    'totalScore' => $detail->ehdsetting['totalScore'],
                    'totalTime' => $detail->ehdsetting['totalTime'],
                    'passMark' => $detail->ehdsetting['passMark'],
                ]
            ];
            if($history->ehstatus) {
                $totalTypeNumber = [];
                $rightTypeNumber = [];
                $totalTypeScore = [];
                $rightTypeScore = [];
                $totalPointScore = [];
                $rightPointScore = [];
                $totalPointNumber = [];
                $rightPointNumber = [];
                foreach ($detail->ehdquestion as $key => $questionData) {
                    $totalTypeNumber[$key] = 0;
                    $rightTypeNumber[$key] = 0;
                    $totalTypeScore[$key] = 0;
                    $rightTypeScore[$key] = 0;
                    if ($questionData['questions'] ?? false) {
                        foreach ($questionData['questions'] as $question) {
                            $questionScore = $detail->ehdsetting['questionScore'][$question['questionid']] ?? $detail->ehdsetting['questionTypes'][$key]['score'] ?? 0;
                            $score = $detail->ehdscores[$question['questionid']] ?? 0;
                            $totalTypeNumber[$key]++;
                            if ($questionScore == $score) $rightTypeNumber[$key]++;
                            $totalTypeScore[$key] += $questionScore;
                            $rightTypeScore[$key] += $score;
                            if (!empty($question['points'])) {
                                foreach ($question['points'] as $point) {
                                    if (!isset($totalPointScore[$point])) $totalPointScore[$point] = 0;
                                    if (!isset($totalPointNumber[$point])) $totalPointNumber[$point] = 0;
                                    if (!isset($rightPointNumber[$point])) $rightPointNumber[$point] = 0;
                                    if (!isset($rightPointScore[$point])) $rightPointScore[$point] = 0;
                                    $totalPointScore[$point] += $questionScore;
                                    $totalPointNumber[$point]++;
                                    if ($questionScore == $score) $rightPointNumber[$point]++;
                                    $rightPointScore[$point] += $score;
                                }
                            }
                        }
                    }
                    if ($questionData['rowsQuestions'] ?? false) {
                        foreach ($questionData['rowsQuestions'] as $rows) {
                            foreach ($rows['children'] as $question) {
                                $questionScore = $detail->ehdsetting['questionScore'][$question['questionid']] ?? $detail->ehdsetting['questionTypes'][$key]['score'] ?? 0;
                                $score = $detail->ehdscores[$question['questionid']] ?? 0;
                                $totalTypeNumber[$key]++;
                                if ($questionScore == $score) $rightTypeNumber[$key]++;
                                $totalTypeScore[$key] += $questionScore;
                                $rightTypeScore[$key] += $score;
                                if (!empty($rows['points'])) {
                                    foreach ($rows['points'] as $point) {
                                        if (!isset($totalPointScore[$point])) $totalPointScore[$point] = 0;
                                        if (!isset($totalPointNumber[$point])) $totalPointNumber[$point] = 0;
                                        if (!isset($rightPointNumber[$point])) $rightPointNumber[$point] = 0;
                                        if (!isset($rightPointScore[$point])) $rightPointScore[$point] = 0;
                                        $totalPointScore[$point] += $questionScore;
                                        $totalPointNumber[$point]++;
                                        if ($questionScore == $score) $rightPointNumber[$point]++;
                                        $rightPointScore[$point] += $score;
                                    }
                                }
                            }
                        }
                    }
                }
                $ehstats['pointAnalysis'] = compact('totalPointScore', 'rightPointScore', 'totalPointNumber', 'rightPointNumber');
                $ehstats['questionTypeAnalysis'] = compact('totalTypeScore', 'rightTypeScore', 'totalTypeNumber', 'rightTypeNumber');
                $history->ehstats = json_encode($ehstats, JSON_UNESCAPED_UNICODE);
                $history->save();
            }
            else $history->ehstats = json_encode($ehstats, JSON_UNESCAPED_UNICODE);
        }
        return $history;
    }
}