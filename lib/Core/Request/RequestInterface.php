<?php

namespace PHPEMS\Lib\Core\Request;

use PHPEMS\App\User\Service\Model\User;

interface RequestInterface
{
    public function getFile($key);
    public function getFiles();
    public function get($key);
    public function getUser();
    public function setUser(User $user);
    public function getRoute();
}