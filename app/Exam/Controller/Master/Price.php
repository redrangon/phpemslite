<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\Model\ExamPrice;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\App\Exam\Service\Model\Basic;
class Price extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'add' => 'Add',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'data' => 'Data'
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

    public function Data():array | Error
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

    public function Delete():array | Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        ExamPrice::getQuery()->whereIn('epid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array | Error
    {
        $epId = $this->request->epid??null;
        if(!$epId) return error(['error' => '参数错误']);
        $model = ExamPrice::find($epId);
        if(!$model->epId) return error(['error' => '记录不存在']);
        $data = [
            'epbasicid' => $this->request->epbasicid??null,
            'epdays' => $this->request->epdays??null,
            'epamount' => $this->request->epamount??null,
        ];
        $data = array_filter($data,function($item){
            return $item !== null;
        });
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        if(!$model->save()) return error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    public function Add():array | Error
    {
        $data = [
            'epbasicid' => $this->request->epbasicid??null,
            'epdays' => $this->request->epdays??null,
            'epamount' => $this->request->epamount??null,
        ];
        $model = ExamPrice::fillWithInit($data);
        $result = $model->toValidate();
        if($result !== true)return $result;
        if(!$model->save()) return error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }
    public function Index():array | Error
    {
        return [];
    }
}