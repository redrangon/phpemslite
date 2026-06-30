<?php

namespace PHPEMS\Lib\Face;

interface FaceInterface
{
    public function FaceComparison(string $imageA, string $imageB);
}