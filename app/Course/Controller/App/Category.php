<?php

namespace PHPEMS\App\Course\Controller\App;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\Course\Service\Model\CourseCategory;
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
            $cat = CourseCategory::find($catId)->getRaw();
        }
        else $cat = ['catid' => 0, 'catname' => '全部分类','isRoot' => true];
        $query = CourseCategory::getQuery()->where('catparent',$catId)->orderBy('catlite','desc');
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
        $category = CourseCategory::find($catId);
        $category = $category->getRaw();
        return $category??[];
    }
}