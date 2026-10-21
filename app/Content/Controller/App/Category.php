<?php

namespace PHPEMS\App\Content\Controller\App;

use PHPEMS\App\Content\Service\Model\ContentCategory;
use PHPEMS\App\Core\Service\Model\App;
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
            $cat = ContentCategory::find($catId)->getRaw();
        }
        else $cat = ['catid' => 0, 'catname' => '全部分类','isRoot' => true];
        $query = ContentCategory::getQuery()->where('catparent',$catId)->orderBy('catlite','desc');
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

    public function index():array
    {
        $catId = intval($this->request->catId);
        if(!$catId)return [];
        $category = ContentCategory::find($catId);
        $category = $category->getRaw();
        return $category??[];
    }
}