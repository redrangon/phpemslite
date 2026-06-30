<?php

namespace PHPEMS\App\Course\Controller\Master;

use PHPEMS\App\Course\Service\Model\CoursePrice;
use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

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
        $csId = $this->request->csId??null;
        if($csId)
        {
            $data = CourseSubject::find($csId);
            if($data->csId)
            {
                return CoursePrice::findByCsId($csId);
            }
        }
        return [];
    }

    public function Delete():array | Error
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        CoursePrice::getQuery()->whereIn('cpid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function Modify():array | Error
    {
        $cpId = $this->request->cpid??null;
        if(!$cpId) return error(['error' => '参数错误']);
        $model = CoursePrice::find($cpId);
        if(!$model->cpId) return error(['error' => '记录不存在']);
        $data = [
            'cpcsid' => $this->request->cpcsid??null,
            'cpdays' => $this->request->cpdays??null,
            'cpamount' => $this->request->cpamount??null,
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
            'cpcsid' => $this->request->cpcsid??null,
            'cpdays' => $this->request->cpdays??null,
            'cpamount' => $this->request->cpamount??null,
        ];
        $model = CoursePrice::fillWithInit($data);
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