<?php

namespace PHPEMS\App\Core\Service\Model;

use PHPEMS\Lib\Rules\Model;

/**
 * Knows 模型
 * 表名: x2_knows
 */
class Task extends Model
{
    protected static $dbName = 'mysql.default';
    protected static $dbTable = 'tasks';
    protected static $primaryKeys = ["taskid"];
    protected static $defaultValues = [
        'taskid' => null,
        'tasksubject' => '',
        'tasksubjectid' => null,
        'taskdata' => '',
        'taskstatus' => 0
    ];

    protected static $rules = [
        'taskid' => 'required',
        'tasksubjectid' => 'required',
    ];
}
