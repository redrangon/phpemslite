<?php

namespace PHPEMS\App\Exam\Controller\Master;

use Exception;
use PHPEMS\App\Exam\Service\ExamService;
use PHPEMS\App\Exam\Service\Model\Point;
use PHPEMS\App\Exam\Service\Model\Subject;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

use PHPEMS\Lib\Utils\FileProvider;

class Section extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'all' => 'All',
            'data' => 'Data',
            'modify' => 'Modify',
            'delete' => 'Delete',
            'add' => 'Add',
            'import' => 'Import',
            'export' => 'Export',
            'refresh' => 'RefreshCache',
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

    public function Export():void
    {
        $subjectId = $this->request->subjectId??null;
        if(!$subjectId)throw new Exception('参数错误');
        $subject = Subject::find($subjectId);
        if(!$subject->subjectId)throw new Exception('科目不存在');
        $data = ExamService::exportSectionWithPoint($subjectId);
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function Import():array
    {
        $subjectId = $this->request->subjectid??null;
        if(!$subjectId)error(['error' => '参数错误']);
        $subject = Subject::find($subjectId);
        if(!$subject->subjectId)error(['error' => '科目不存在']);
        $service = new FileProvider();
        $file = $this->request->getFile('file');
        try{
            $result = $service->upload($file);
            if($result['success'])
            {
                $JSON = file_get_contents($result['path']);
                $JSON = json_decode($JSON,true,512,JSON_THROW_ON_ERROR);
                if(empty($JSON))throw new \Exception('JSON文件格式错误');
                ExamService::importSectionWithPoint($JSON,$subjectId);
                return ['msg' => '导入成功'];
            }
            else error(['error' => $result['error']]);
        }catch (Exception $e){
            error(['error' => $e->getMessage()]);
        }
    }

    public function RefreshCache():array
    {
        $ids = $this->request->ids??null;
        if(!$ids) error(['error' => '记录不存在']);
        $points = Point::getQuery()->select(['pointid'])
            ->whereIn('pointsectionid',$ids)
            ->get();
        foreach($points as $point)
        {
            ExamService::refreshPointCache($point['pointid']);
        }
        return ['msg' => '刷新成功'];
    }

    /**
     * 添加操作
     */
    public function Add():array
    {
        // 获取请求数据
        $data = [
            'section' => $this->request->section??null,
            'sectionsubjectid' => $this->request->sectionsubjectid??null
        ];

        // TODO: 实现添加逻辑
        // 示例：
        $model = \PHPEMS\App\Exam\Service\Model\Section::fillWithInit($data);
        $result = $model->toValidate();
        if($result !== true)return $result;
        if(!$model->save()) error(['error' => '添加失败']);
        return ['msg' => '添加成功'];
    }

    /**
     * 修改操作
     */
    public function modify():array
    {
        // TODO: 实现修改逻辑
        // 示例：
        $sectionId = $this->request->sectionid;
        $model = \PHPEMS\App\Exam\Service\Model\Section::find($sectionId);
        if(!$model->sectionid) error(['error' => '记录不存在']);
        $data = [
            'section' => $this->request->section??null,
            'sectiondescribe' => $this->request->sectiondescribe??null,
            'sectionsequence' => $this->request->sectionsequence??null,
        ];
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        if(!$model->save()) error(['error' => '修改失败']);
        return ['msg' => '修改成功'];
    }

    /**
     * 删除操作
     */
    public function Delete():array
    {
        $ids = $this->request->ids??[];
        if(empty($ids)) error(['error' => '未选择要删除的记录']);
        \PHPEMS\App\Exam\Service\Model\Section::getQuery()->whereIn('sectionid', $ids)->delete();
        return ['msg' => '删除成功'];
    }

    public function All():array
    {
        $subjectId = $this->request->subjectid??null;
        if(!$subjectId)error(['error' => '参数错误']);
        $query = \PHPEMS\App\Exam\Service\Model\Section::getQuery();
        $items = $query->select(['sectionid','section'])->where('sectionsubjectid', $subjectId)->get();
        $sections = [];
        foreach ($items as $item) {
            $sections[$item['sectionid']] = $item['section'];
        }
        return $sections;
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        // TODO: 实现数据查询逻辑
        // 示例：
        $query = \PHPEMS\App\Exam\Service\Model\Section::getQuery()->orderBy('sectionid', 'DESC');
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $subjectid = $this->request->subjectid??null;
        if($subjectid)$query->where('sectionsubjectid', $subjectid);
        $search = $this->request->search;
        if($search) {
            if($search['keyword']??false)$query->where('section', 'like', '%'.$search['keyword'].'%');
        }
        return $query->paginate($page, $limit);
    }

    public function Index(): array
    {
        $sectionId = $this->request->sectionid??null;
        if(!$sectionId)error(['error' => '记录不存在']);
        $section = \PHPEMS\App\Exam\Service\Model\Section::find($sectionId);
        if(!$section->sectionid)error(['error' => '记录不存在']);
        return $section->getRaw();
    }
}
