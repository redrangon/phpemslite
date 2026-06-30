<?php

namespace PHPEMS\App\Course\Controller\App;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\App\Course\Service\Model\CourseCategory;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

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

    public function Data(): array | Error
    {
        $catId = intval($this->request->catId);
        $query = CourseCategory::getQuery()->where('catparent',$catId)->orderBy('catlite','desc');
        $data = $query->get();
        return $data;
    }

    public function Index():array | Error
    {
        $catId = intval($this->request->catId);
        if(!$catId)return [];
        $category = CourseCategory::find($catId);
        $category = $category->getRaw();
        return $category??[];
    }
}