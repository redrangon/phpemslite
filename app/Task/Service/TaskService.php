<?php

namespace PHPEMS\App\Task\Service;

class TaskService
{
    static public function getTaskEventClass():array
    {
        return [
            'Demo' => [
                'name' => 'Demo',
                'class' => '\PHPEMS\App\Task\Service\Event\TaskDemo'
            ]
        ];
    }
}