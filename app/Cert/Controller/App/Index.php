<?php

namespace PHPEMS\App\Cert\Controller\App;

use PHPEMS\App\Cert\Service\Model\Cert;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Index extends Controller implements ControllerInterface
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
        return $flows[$action]??static::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    /**
     * 获取数据列表
     */
    public function Data(): array
    {
        $page = $this->request->page ?? 1;
        $limit = $this->request->limit ?? 20;
        $search = $this->request->search;
        $query = Cert::getQuery();
        return $query->paginate($page, $limit);
    }

    public function Index(): Error|array
    {
        return [];
    }
}
