<?php

namespace PHPEMS\App\Task\Controller\Master;

use PHPEMS\App\Task\Service\TaskService;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

class Index extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'event' => 'Event'
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

    public function Event():array
    {
        return TaskService::getTaskEventClass();
    }

    public function Index():array
    {
        return [];
    }
}