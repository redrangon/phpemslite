<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\Model\Basic;
use PHPEMS\App\Exam\Service\Model\ExamMember;
use PHPEMS\App\Exam\Service\Model\ExamPrice;
use PHPEMS\App\Exam\Service\Model\ExamSession;
use PHPEMS\App\Exam\Service\Model\QuestionType;
use PHPEMS\App\User\Service\Model\UserExpense;
use PHPEMS\App\User\Service\Model\UserMoney;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Index extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth','Json'];
    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'session' => 'setSession',
            'type' => 'QuestionType',
            'data' => 'Data',
            'basic' => 'Basic',
            'price' => 'Price',
            'buy' => 'Buy'
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

    public function Buy(): array | Error
    {
        $priceId = $this->request->priceId??null;
        if($priceId)
        {
            $price = ExamPrice::find($priceId);
            if($price->epId)
            {
                $user = $this->request->getUser();
                $userMoney = UserMoney::findByPassport($user->userpassport);
                if($userMoney->umamount >= $price->epamount)
                {
                    try{
                        $basic = Basic::find($price->epbasicid);
                        if(!$basic->basicId)throw new \Exception('考场记录不存在');
                        ExamMember::getDB()->transaction(function() use ($price, $user,$basic){
                            UserMoney::getQuery()->update([
                                'umamount' => function() use ($price){
                                    return "umamount - {$price->epamount}";
                                }
                            ]);
                            UserExpense::getQuery()->insert([
                                'ueamount' => $price->epamount,
                                'uepassport' => $user->userpassport,
                                'uetime' => TIME,
                                'uedescribe' => '购买考场'.$basic->basic,
                            ]);
                            $examMember = ExamMember::findByPassportAndSubjectId($user->userpassport, $basic->basicId);
                            if(!$examMember->emId)
                            {
                                $examMember = ExamMember::fillWithInit([
                                    'embasicid' => $basic->basicId,
                                    'empassport' => $user->userpassport,
                                    'emstarttime' => TIME,
                                    'emendtime' => TIME + 86400 * $price->epdays,
                                ]);
                                $examMember->save();
                            }
                            else
                            {
                                $examMember->emendtime = max(TIME,$examMember->emendtime) + 86400 * $price->epdays;
                                $examMember->save();
                            }
                        });
                        return ['msg' => '开通成功'];
                    }
                    catch (\Exception $e)
                    {
                        return error(['error' => '扣款失败，请稍后尝试！'.$e->getMessage()]);
                    }
                }
                else return error(['error' => '金额不足']);
            }
            return error(['error' => '价格记录不存在']);
        }
        return error(['error' => '记录不存在']);
    }

    public function Price(): array | Error
    {
        $basicId = $this->request->basicId??null;
        if($basicId)
        {
            $data = Basic::find($basicId);
            if($data->basicId)
            {
                return ExamPrice::findByBasicId($basicId);
            }
        }
        return [];
    }

    public function Basic(): Error|array
    {
        $basicId = $this->request->basicId??null;
        $basic = Basic::find($basicId)->getRaw();
        if($basic??false){
            $basic['basicpoint'] = json_decode($basic['basicpoint'],true)??[];
            $basic['basicexam'] = json_decode($basic['basicexam'],true)??[];
            $user = $this->request->getUser();
            $examMember = ExamMember::findByPassportAndSubjectIdWithTime($user->userpassport,$basicId);
            if($examMember->emid)
            {
                $basic['isPurchased'] = true;
            }
        }
        return $basic??[];
    }

    public function setSession(): array | Error
    {
        $examId = $this->request->examId??null;
        if(!$examId)return error(['error' => '参数错误']);
        $basic = Basic::find($examId);
        if(!$basic->basicid)return error(['error' => '考场不存在']);
        $user = $this->request->getUser();
        $basicMember = ExamMember::findByPassportAndSubjectIdWithTime($user->userpassport,$examId);
        if(!$basicMember->emid)return error(['error' => '您没有开通本考场']);
        $session = ExamSession::findByPassport($user->userpassport);
        if(!$session->esid)
        {
            $session = ExamSession::fillWithInit([
                'espassport' => $user->userpassport,
                'esbasicid' => $examId,
            ]);
            $session->save();
        }
        else
        {
            if($session->esbasicid != $examId)
            {
                $session->esbasicid = $examId;
                $session->save();
            }
        }
        return ['msg' => '设置成功'];
    }

    public function QuestionType(): array | Error
    {
        $questionTypes = QuestionType::getAll();
        return array_column($questionTypes,null,'questid');
    }

    public function Data(): array
    {
        $query = Basic::getQuery()->select(['basicid','basic','basicnumber','basicsubjectid','basicthumb','basicdescribe'])->orderBy('basicid', 'DESC');
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

    public function Index(): array
    {
        return [];
    }
}