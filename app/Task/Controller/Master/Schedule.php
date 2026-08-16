<?php

namespace PHPEMS\App\Task\Controller\Master;

use PHPEMS\App\Task\Service\Model\TaskSchedules;
use PHPEMS\App\Task\Service\Model\Task;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Schedule extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add'
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

    /**
     * 添加操作
     */
    public function Add():array | Error
    {
        // 获取请求数据
        $tsexetype = $this->request->tsexetype??null;
        $data = [
            'tsname' => $this->request->tsname??null,
            'tsdescribe' => $this->request->tsdescribe??'',
            'tsuserid' => $this->request->getUser()->userid,
            'tsclass' => $this->request->tsclass??null,
            'tspayload' => $this->request->tspayload??json_encode(null,JSON_UNESCAPED_UNICODE),
            'tstype' => $this->request->tstype??null,
            'tspriority' => $this->request->tspriority??1,
            'tsattempts' => $this->request->tsattempts??null,
            'tsloop' => $this->request->tsloop??null,
            'tstimes' => $this->request->tstimes??0,
            'tsenabled' => $this->request->tsenabled??0,
            'tslastrun' => $this->request->tslastrun??0,
            'tsnextrun' => $this->request->tsnextrun??0,
            'tstime' => TIME,
        ];

        // TODO: 实现添加逻辑
        // 示例：
        if($tsexetype == 3)
        {
            $model = TaskSchedules::fillWithInit($data);
            $result = $model->toValidate();
            if($result !== true) return $result;
            if(!$model->save()) return error(['error' => '添加失败']);
            return ['msg' => '添加成功'];
        }
        else
        {
            $taskData = [
                'taskname' => $data['tsname'],
                'taskclass' => $data['tsclass'],
                'taskpayload' => $data['tspayload'],
                'tasktype' => $data['tstype'],
                'taskmaxattempts' => $data['tsattempts'],
                'tasktimeout' => 30,
                'taskattempts' => 0,
                'taskstatus' => 'pending',
                'taskscheduletime' => $tsexetype == 2?strtotime($data['tsloop']):0,
                'tasklocktime' => 0,
                'taskresult' => 0,
                'taskinfo' => '',
                'taskstarttime' => 0,
                'taskfinishtime' => 0,
                'taskpriority' => $data['tspriority']
            ];
            $model = \PHPEMS\App\Task\Service\Model\Task::fillWithInit($taskData);
            $result = $model->toValidate();
            if($result !== true) return $result;
            if(!$model->save()) return error(['error' => '添加失败']);
            return ['msg' => '任务已加入队列'];
        }
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        // TODO: 实现修改逻辑
        // 示例：
        $tsid = $this->request->tsid??null;
        $model = TaskSchedules::find($tsid);
        if(!$model->tsId) return error(['error' => '记录不存在']);
        $data = [
            'tsname' => $this->request->tsname??null,
            'tsdescribe' => $this->request->tsdescribe??'',
            'tsclass' => $this->request->tsclass??null,
            'tspayload' => $this->request->tspayload??null,
            'tstype' => $this->request->tstype??null,
            'tspriority' => $this->request->tspriority??1,
            'tsattempts' => $this->request->tsattempts??null,
            'tsloop' => $this->request->tsloop??null,
            'tsenabled' => $this->request->tsenabled??0,
        ];
        foreach ($data as $key => $value) {
            $model->$key = $value;
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
        $ids = $this->request->ids??[];
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);
        TaskSchedules::getQuery()->whereIn('tsid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = TaskSchedules::getQuery()->orderBy('tsid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search??null;
        if($search) {
            if($search['tsname']??false)$query->where('tsname', 'like', '%'.$search['tsname'].'%');
            if($search['tstype']??false)$query->where('tstype', $search['tstype']);
            if($search['tsenabled']??false)$query->where('tsenabled', $search['tsenabled']);
        }
        $data = $query->paginate($page, $limit);
        return $data;
    }

    public function Index(): Error|array
    {
        return [];
    }
}
