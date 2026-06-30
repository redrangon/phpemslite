<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Exam\Service\Model\Subject;
use PHPEMS\App\User\Service\Model\User;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Office\Xlsx\Reader;

class Task extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'import' => 'Import',
            'data' => 'Data',
            'add' => 'Add',
            'edit' => 'Edit',
            'del' => 'Del',
            'index' => 'Index'
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

    public function Import()
    {
        $taskId = $this->request->taskid;
        $file = $this->request->getFile('file')->getTempFilePath();
        if(file_exists($file))
        {
            $reader = new Reader($file);
            $sheets = $reader->getSheetNames();
            $sheet = array_key_first($sheets);
            $data = $reader->getSheetData($sheet);
            if(!$data)return error(['error' => '文件中没有数据']);
            $taskData = [];
            foreach ($data as $row)
            {
                if(empty($row))continue;
                if($row[8]??false)
                {
                    if($row[9]??false)
                    {
                        $taskData[] = [
                            $row[0],
                            $row[1],
                            1
                        ];
                    }
                }
                else
                {
                    $taskData[] = [
                        $row[0],
                        $row[1],
                        0
                    ];
                }
            }
            $task = \PHPEMS\App\Core\Service\Model\Task::find($taskId);
            if(!$task->taskid)return error(['error' => '任务数据不存在']);
            $task->taskdata = json_encode($taskData, JSON_UNESCAPED_UNICODE);
            $task->taskstatus = 1;
            $task->save();
            return ['message' => '导入成功'];
        }
        else return error(['error' => '文件不存在']);
    }

    public function Edit(): array|Error
    {
        $taskid = $this->request->taskid;
        $task = \PHPEMS\App\Core\Service\Model\Task::find($taskid);
        if(!$task->taskid)return error(['error' => '任务数据不存在']);
        $data = [
            'taskname' => $this->request->taskname
        ];
        $data = array_filter($data,fn($value) => $value !== null);
        \PHPEMS\App\Core\Service\Model\Task::getQuery()
            ->where('taskid', $taskid)
            ->update($data);
        return ['msg'=>'修改成功'];
    }

    public function Del(): array
    {
        $ids = $this->request->ids;
        \PHPEMS\App\Core\Service\Model\Task::getQuery()->whereIn('taskid', $ids)->delete();
        return ['message' => '删除成功'];
    }

    public function Add(): array|Error
    {
        $taskname = $this->request->taskname??null;
        $tasksubjectid = $this->request->tasksubjectid??null;
        if($taskname && $tasksubjectid)
        {
            $subject = Subject::find($tasksubjectid);
            $data = [
                'taskname' => $taskname,
                'tasksubjectid' => $tasksubjectid,
                'tasksubject' => $subject->subject
            ];
            $data = \PHPEMS\App\Core\Service\Model\Task::fill($data);
            if(\PHPEMS\App\Core\Service\Model\Task::getQuery()->insert($data))
            return ['message' => '添加成功'];
            else return error(['error' => '添加失败']);
        }
        else return error(['error' => '参数错误']);
    }

    public function Data(): array
    {
        $page = $this->request->page??1;
        $limit = $this->request->limit??10;
        $query = \PHPEMS\App\Core\Service\Model\Task::getQuery()->select(['taskid,taskname,tasksubject,tasksubjectid','taskstatus']);
        $data = $query->paginate($page,$limit);
        return $data;
    }

    public function Index(): array
    {
        $datas = Subject::getAll();
        $subjects = [];
        foreach ($datas as $data)
        {
            $subjects[$data['subjectid']] = $data['subject'];
        }
        return $subjects;
    }
}