<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Exam\Service\Model\FeedBack;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Feed extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth','Json'];
    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'add' => 'Add'
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

    public function Data(): array | Error
    {
        $user = $this->request->getUser();
        $page = $this->request->page()??1;
        $limit = $this->request->limit()??20;
        $data = FeedBack::getListByPage($page, $limit,[
            'fbuserid' => $user->userid,
        ]);
        return [];
    }

    public function Add(): array | Error
    {
        $user = $this->request->getUser();
        $fbType = $this->request->fbtype??null;
        $fbContent = $this->request->fbcontent??null;
        $fbQuestionId = $this->request->fbquestionid??null;
        if($fbType && $fbContent && $fbQuestionId)
        {
            $data = [
                'fbtype' => $fbType,
                'fbcontent' => $fbContent,
                'fbquestionid' => $fbQuestionId,
                'fbuserid' => $user->userid,
                'fbtime' => TIME
            ];
            $feed = FeedBack::fillWithInit($data);
            $result = $feed->toValidate();
            if($result === true)
            {
                $feed->save();
                return ['msg' => '添加成功'];
            }
            else return $result;
        }
        else return error(['error' => '错误的参数']);
    }

    public function Index():array | Error
    {
        return [];
    }
}