#!/usr/bin/env php
<?php
// bin/stop_worker.php
// 定义项目根目录
use PHPEMS\Lib\Config\Site\Site;

define("PEPATH", dirname(__DIR__));

// 引入CLI环境
require_once PEPATH . "/boot/cli.php";

$queue = $argv[1] ?? 'default';
$stopFile = DI(Site::class)->tmpDir. '/worker_' . $queue . '.stop';

file_put_contents($stopFile, time());

echo "[" . date('Y-m-d H:i:s') . "] Stop signal sent to worker: {$queue}\n";
echo "  Stop file: {$stopFile}\n";