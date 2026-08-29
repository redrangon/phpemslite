<?php

namespace PHPEMS\Lib\Utils;


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

class QrcodeProvider
{
    public static function outSimpleQrcodeString(string $txt):string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            data: $txt,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High
        );
        $result = $builder->build();
        return $result->getString();
    }
}