<?php

namespace PHPEMS\Lib\Face\Drivers;

use PHPEMS\Lib\Config\Site\Face;
use PHPEMS\Lib\Face\FaceInterface;

class TestFace implements FaceInterface
{
    public function FaceComparison(string $imageA, string $imageB):bool
    {
        return true;
    }
}