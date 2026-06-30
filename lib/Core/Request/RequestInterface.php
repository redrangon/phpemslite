<?php

namespace PHPEMS\Lib\Core\Request;

interface RequestInterface
{
    public function getFile($key);
    public function getFiles();
    public function get($key);
    public function getUser();
    public function setUser($user);
    public function getRoute();
}