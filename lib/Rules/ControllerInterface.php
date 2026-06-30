<?php

namespace PHPEMS\Lib\Rules;

interface ControllerInterface
{
    static public function getRoutes();
    public function Index();
}