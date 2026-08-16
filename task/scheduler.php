<?php
/**
 * 任务调度worker
 */

// 定义项目根目录
use PHPEMS\App\Task\Service\ScheduleService;

define("PEPATH", dirname(__DIR__));

// 引入CLI环境
require_once(PEPATH."/boot/cli.php");

$scheduler = new ScheduleService();
$mode = $argv[1] ?? 'once';

if ($mode === 'once') {
    // 单次执行模式（推荐用于 Supervisor）
    $scheduler->runOnce();
} else {
    // 持续运行模式（不推荐，仅用于调试）
    $scheduler->run();
}