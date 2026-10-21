<?php

namespace PHPEMS\App\Exam\Controller\App;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\Exam\Service\Model\ExamCategory;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;


class Category extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth','Json'];
    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'data' => 'Data'
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

    public function Data(): array
    {
        $catId = intval($this->request->catId);
        if($catId)
        {
            $cat = ExamCategory::find($catId)->getRaw();
        }
        else $cat = ['catid' => 0, 'catname' => '全部分类','isRoot' => true];
        $query = ExamCategory::getQuery()->where('catparent',$catId)->orderBy('catlite','desc');
        $data = $query->get();
        if(!empty($data))$cat['isRoot'] = true;
        $data = [$cat,...$data];
        if($catId)
        {
            $data[] = [
                'catid' => $cat['catparent']??0,
                'catname' => '上级目录',
                'isTop' => true
            ];
        }
        return $data;
    }

    public function Index():array
    {
        $catId = intval($this->request->catId);
        if(!$catId)return [];
        $category = ExamCategory::find($catId);
        $category = $category->getRaw();
        return $category??[];
    }
}