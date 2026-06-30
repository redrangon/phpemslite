<?php

namespace PHPEMS\App\Core\Controller\App;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Utils\Office\Xlsx\Reader;
use PHPEMS\Lib\Utils\Office\Xlsx\Writer;

class Index extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Tpl'];
    static public function getRoutes():array
    {
        return [
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

    public function Index(): array
    {
        return [
            'tpl' => 'Index',
            'data' => [
                'mainJs' => vite()
            ]
        ];
    }
}