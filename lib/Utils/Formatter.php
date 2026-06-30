<?php

namespace PHPEMS\Lib\Utils;

class Formatter
{
    static public function formatPassport(string $sn):array
    {
        $year = substr($sn,6,4);
        $month = substr($sn,10,2);
        $day = substr($sn,12,2);
        $sex = substr($sn,16,1);
        return array(
            'birthday' => "{$year}-{$month}-{$day}",
            'sex' => $sex %2?'男':'女',
            'passport' => $sn
        );
    }

}