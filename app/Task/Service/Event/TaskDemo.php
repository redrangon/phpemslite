<?php

namespace PHPEMS\App\Task\Service\Event;

use PHPEMS\App\Task\Service\Event\TaskInterface;
class TaskDemo implements TaskInterface
{
    public static function Run():void
    {
        echo 'Demo Task Runs';
    }
}