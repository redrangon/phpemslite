#!/usr/bin/env php
<?php
// bin/worker.php

use PHPEMS\App\Task\Service\WorkerService;

// 定义项目根目录
define("PEPATH", dirname(__DIR__));

// 引入CLI环境
require_once PEPATH . "/boot/cli.php";

// ============================================
// 解析参数
// ============================================
$queue = $argv[1] ?? 'default';
$maxJobs = (int) ($argv[2] ?? 100);
$maxMemory = (int) ($argv[3] ?? 128);

// ============================================
// 显示帮助
// ============================================
if ($queue === '--help' || $queue === '-h') {
    echo "Usage: php worker.php [queue] [max_jobs] [max_memory]\n";
    echo "  queue       - 队列类型 (default, email, report, payment)\n";
    echo "  max_jobs    - 处理多少任务后退出 (默认: 100)\n";
    echo "  max_memory  - 最大内存限制MB (默认: 128)\n";
    echo "\nExamples:\n";
    echo "  php worker.php default\n";
    echo "  php worker.php email 50 256\n";
    exit(0);
}

echo "[" . date('Y-m-d H:i:s') . "] Starting worker\n";
echo "  Queue: {$queue}\n";
echo "  Max jobs: {$maxJobs}\n";
echo "  Max memory: {$maxMemory}MB\n";

// ============================================
// 启动 Worker
// ============================================
$worker = new WorkerService($queue, $maxJobs, $maxMemory);
$worker->run();