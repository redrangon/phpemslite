<?php

namespace PHPEMS\App\Course\Controller\Master;

use PHPEMS\App\Course\Service\Model\CourseSubject;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Course extends Controller implements ControllerInterface
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
            'all' => 'All',
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
    public function Add():array|Error
    {
        // 获取请求数据
        $user = $this->request->getUser();
        $data = [
            'coursetitle' => $this->request->coursetitle??null,
            'coursemodule' => $this->request->coursemodule??null,
            'coursecsid' => $this->request->coursecsid??null,
            'coursedirid' => $this->request->coursedirid??0,
            'coursethumb' => $this->request->coursethumb??"",
            'courseuserid' => $user->userid,
            'courseinputtime' => TIME,
            'coursemodifytime' => TIME,
            'coursesequence' => $this->request->coursesequence??0,
            'coursedescribe' => $this->request->coursedescribe??"",
            'coursepath' => $this->request->coursepath??"",
            'coursepasstime' => $this->request->coursepasstime??0,
        ];

        $model = \PHPEMS\App\Course\Service\Model\Course::fillWithInit($data);
        if(!$model->save()) return error(['error' => '添加失败']);

        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array|Error
    {
        $courseId = $this->request->courseid;
        $model = \PHPEMS\App\Course\Service\Model\Course::find($courseId);
        if(!$model->courseid) return error(['error' => '记录不存在']);

        $data = [
            'coursetitle' => $this->request->coursetitle??null,
            'coursemodule' => $this->request->coursemodule??null,
            'coursecsid' => $this->request->coursecsid??null,
            'coursedirid' => $this->request->coursedirid??null,
            'coursethumb' => $this->request->coursethumb??null,
            'coursemodifytime' => TIME,
            'coursesequence' => $this->request->coursesequence??null,
            'coursedescribe' => $this->request->coursedescribe??null,
            'coursepath' => $this->request->coursepath??null,
            'coursepasstime' => $this->request->coursepasstime??null,
        ];

        if($data['coursemodule'] != $model->coursemodule)
        {
            if($model->coursemodule == 'dir')
            {
                $number = \PHPEMS\App\Course\Service\Model\Course::getQuery()->where('coursedirid', $courseId)->count();
                if($number > 0)return error(['error' => '修改失败，此文件夹下有课件']);
            }
        }

        if($data['coursedirid'])
        {
            if($data['coursedirid'] == $courseId)return error(['error' => '修改失败，不能移动到自己目录下']);
        }

        $data = array_filter($data, function($item) {
            return !is_null($item);
        });

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
        $ids = $this->request->ids;
        if(empty($ids)) return error(['error' => '未选择要删除的记录']);

        \PHPEMS\App\Course\Service\Model\Course::getQuery()->whereIn('courseid', $ids)->delete();

        return ['msg' => '删除成功'];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array|Error
    {
        $csid = $this->request->csid ?? null;
        if(!$csid)return error(['error' => '缺少课程ID']);
        $model = CourseSubject::find($csid);
        if(!$model->csid)return error(['error' => '缺少课程']);
        $query = \PHPEMS\App\Course\Service\Model\Course::getQuery()
            ->where('coursecsid','=',$csid)
            ->orderBy('coursesequence', 'desc')->orderBy('courseid', 'DESC');
        $page = $this->request->page ?? 1;
        $dirid = $this->request->dirid ?? 0;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        if($search) {
            if($search['keyword'] ?? false) {
                $query->where('coursetitle', 'like', '%'.$search['keyword'].'%');
            }
            if($search['coursemodule'] ?? false) {
                $query->where('coursemodule', $search['coursemodule']);
            }
            if($search['coursecsid'] ?? false) {
                $query->where('coursecsid', $search['coursecsid']);
            }
        }
        $query->where('coursedirid',$dirid);
        $data = $query->paginate($page, $limit);

        if($data['total'] > 0) {
            array_walk($data['data'], function (&$item) {
                $item['courseinputtime'] = date('Y-m-d H:i:s', $item['courseinputtime']);
                $item['coursemodifytime'] = date('Y-m-d H:i:s', $item['coursemodifytime']);
            });
        }

        return $data;
    }

    public function All():array | Error
    {
        $query = \PHPEMS\App\Course\Service\Model\Course::getQuery()
            ->select(['courseid', 'coursetitle', 'coursemodule', 'coursecsid','coursedirid'])
            ->orderBy('coursesequence', 'desc')->orderBy('courseid', 'DESC');
        $csid = $this->request->csid??null;
        if(!$csid)return error(['error' => '错误的课程ID']);
        $model = CourseSubject::find($csid);
        if(!$model->csid)return error(['error' => '错误的课程ID']);
        return $query->where('coursecsid', '=', $csid)->get();
    }

    public function Index(): Error|array
    {
        $courseId = $this->request->courseid ?? null;
        if($courseId) {
            $data = \PHPEMS\App\Course\Service\Model\Course::find($courseId)->getRaw();
            if($data && $data['courseinputtime']) {
                $data['courseinputtime'] = date('Y-m-d H:i:s', $data['courseinputtime']);
            }
            if($data && $data['coursemodifytime']) {
                $data['coursemodifytime'] = date('Y-m-d H:i:s', $data['coursemodifytime']);
            }
            return $data;
        }
        return [];
    }
}
