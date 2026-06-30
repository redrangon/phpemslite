<?php

namespace PHPEMS\App\Core\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * App 模型
 * 表名: x2_app
 */
class App extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'app';
    protected static $primaryKeys = ["appid"];
    protected static $defaultValues = [
        "appcode" => null,
        "appname" => "",
        "appstatus" => 1,
        "appsetting" => 0
    ];

    // ========== 常用查询方法 ==========

    public static function getAppByCode(string $appCode): static
    {
        return new static(self::getQuery()->where('appcode', $appCode)->first()??[]);
    }

}
