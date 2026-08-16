<?php

namespace PHPEMS\App\Task\Service;

use Exception;
use PHPEMS\App\Task\Service\Model\Task;
use PHPEMS\Lib\Config\Site\Site;

/**
 * Worker 服务类
 * 负责从 task 表中拉取任务并执行
 *
 * 特性：
 * - 使用 FOR UPDATE SKIP LOCKED 防止重复消费
 * - 支持任务重试机制
 * - 内存和任务数量自检
 * - 跨平台停止信号（文件标记）
 */
class WorkerService
{
    private string $workerId;
    private string $queueType;
    private int $maxJobs = 100;
    private int $maxMemory = 128;
    private int $processedCount = 0;
    private bool $running = true;
    private string $stopFile;

    /**
     * @param string $queueType 队列类型（如 default, email, report）
     * @param int $maxJobs 处理多少任务后主动退出（防止内存泄漏）
     * @param int $maxMemory 最大内存限制（MB）
     */
    public function __construct(
        string $queueType = 'default',
        int $maxJobs = 100,
        int $maxMemory = 128
    ) {
        $this->queueType = $queueType;
        $this->maxJobs = $maxJobs;
        $this->maxMemory = $maxMemory;
        $this->workerId = 'worker_' . getmypid() . '_' . uniqid();
        $this->stopFile = DI(Site::class)->tmpDir . '/worker_' . $this->queueType . '.stop';

        // 注册 shutdown 函数（进程退出时清理）
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * Worker 主循环
     */
    public function run(): void
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title('php: worker-' . $this->queueType);
        }

        echo "[" . date('Y-m-d H:i:s') . "] Worker started\n";
        echo "  PID: " . getmypid() . "\n";
        echo "  Queue: {$this->queueType}\n";
        echo "  Worker ID: {$this->workerId}\n";
        echo "  Stop file: {$this->stopFile}\n";
        echo "  Max jobs: {$this->maxJobs}, Max memory: {$this->maxMemory}MB\n";
        echo "[" . date('Y-m-d H:i:s') . "] To stop: touch {$this->stopFile}\n";

        // 清除旧的停止标记
        if (file_exists($this->stopFile)) {
            unlink($this->stopFile);
        }

        while ($this->running) {
            // 检查停止标记
            if (file_exists($this->stopFile)) {
                echo "[" . date('Y-m-d H:i:s') . "] Stop file detected, shutting down...\n";
                $this->running = false;
                break;
            }

            // 拉取任务
            $task = $this->popTask();

            if ($task) {
                $this->executeTask($task);
                $this->processedCount++;

                // 健康检查
                if (!$this->healthCheck()) {
                    break;
                }
            } else {
                // 没有任务，休息 0.1 秒
                usleep(100000);
            }
        }

        echo "[" . date('Y-m-d H:i:s') . "] Worker stopped. Processed {$this->processedCount} tasks.\n";
    }

    /**
     * 从队列拉取一个任务（原子操作）
     * 使用 FOR UPDATE SKIP LOCKED 防止重复消费
     */
    private function popTask(): ?array
    {
        $now = time();
        $result = null;

        try {
            Task::getDB()->transaction(function () use ($now, &$result) {
                // 1. 查询并锁定任务
                $task = Task::getQuery()
                    ->select(['*'])
                    ->where('taskstatus', 'pending')
                    ->where('tasktype', $this->queueType)
                    ->where('taskscheduletime', '<=', $now)
                    ->whereRaw('taskattempts < taskmaxattempts')
                    ->orderBy('taskid', 'asc')
                    ->limit(1)
                    ->lockForUpdate(true)  // FOR UPDATE SKIP LOCKED
                    ->first();

                if (!$task) {
                    return;  // 没有任务，直接返回
                }

                // 2. 更新任务状态
                $affected = Task::getQuery()
                    ->where('taskid', $task['taskid'])
                    ->where('taskstatus', 'pending')
                    ->update([
                        'taskstatus' => 'locked',
                        'tasklocktime' => $now,
                        'taskstarttime' => $now,
                        'taskattempts' => function() {
                            return 'taskattempts + 1';
                        }
                    ]);

                if ($affected > 0) {
                    // 3. 重新查询获取最新数据
                    $result = Task::getQuery()
                        ->where('taskid', $task['taskid'])
                        ->first();
                }
            });

            return $result;

        } catch (Exception $e) {
            echo "[" . date('Y-m-d H:i:s') . "] Error popping task: " . $e->getMessage() . "\n";
            return null;
        }
    }

    /**
     * 执行任务
     */
    private function executeTask(array $task): void
    {
        $taskId = $task['taskid'];
        $taskClass = $task['taskclass'];
        $payload = json_decode($task['taskpayload'], true);

        echo "[" . date('Y-m-d H:i:s') . "] Executing task #{$taskId}: {$taskClass}\n";

        try {
            // ============================================
            // 1. 检查任务类是否存在
            // ============================================
            if (!class_exists($taskClass)) {
                throw new Exception("Task class not found: {$taskClass}");
            }

            // ============================================
            // 2. 动态实例化任务
            // ============================================
            $taskInstance = new $taskClass($payload);

            if (!method_exists($taskInstance, 'Run')) {
                throw new Exception("Task class does not have Run() method: {$taskClass}");
            }

            // ============================================
            // 3. 执行任务
            // ============================================
            $startTime = microtime(true);
            $taskInstance->Run();
            $duration = microtime(true) - $startTime;

            // ============================================
            // 4. 标记成功
            // ============================================
            $this->markTaskSuccess($taskId, $duration);

            echo "  ✅ Task #{$taskId} completed in " . round($duration, 2) . "s\n";

        } catch (Exception $e) {
            // ============================================
            // 5. 处理失败
            // ============================================
            $this->handleTaskFailure($task, $e);
        }
    }

    /**
     * 标记任务成功
     */
    private function markTaskSuccess(int $taskId, float $duration): void
    {
        Task::getQuery()->where('taskid', $taskId)
            ->update([
                'taskstatus' => 'finished',
                'taskresult' => 2,
                'taskinfo' => json_encode([
                    'duration' => round($duration, 2),
                    'timestamp' => time(),
                    'worker_id' => $this->workerId
                ], JSON_UNESCAPED_UNICODE),
                'taskfinishtime' => time()
            ]);
    }

    /**
     * 处理任务失败
     */
    private function handleTaskFailure(array $task, Exception $e): void
    {
        $taskId = $task['taskid'];
        $attempts = (int) $task['taskattempts'] + 1;
        $maxAttempts = (int) $task['taskmaxattempts'];

        $errorMessage = $e->getMessage();

        echo "  ❌ Task #{$taskId} failed (attempt {$attempts}/{$maxAttempts}): " . $errorMessage . "\n";

        if ($attempts < $maxAttempts) {
            // ============================================
            // 需要重试：重置为 pending 状态
            // ============================================
            Task::getQuery()->where('taskid', $taskId)
                ->update([
                    'taskstatus' => 'pending',
                    'tasklocktime' => null,
                    'taskattempts' => $attempts,
                    'taskinfo' => 'Retry: ' . $errorMessage,
                    'taskresult' => 1
                ]);

            echo "  🔄 Task #{$taskId} will be retried\n";

        } else {
            // ============================================
            // 达到最大重试次数：标记为最终失败
            // ============================================
            Task::getQuery()->where('taskid', $taskId)
                ->update([
                    'taskstatus' => 'finished',
                    'taskresult' => 1,
                    'taskinfo' => 'Final failure: ' . $errorMessage . "\n" . $e->getTraceAsString(),
                    'taskfinishtime' => time()
                ]);

            echo "  ❌ Task #{$taskId} permanently failed after {$attempts} attempts\n";
        }
    }

    /**
     * 健康检查
     */
    private function healthCheck(): bool
    {
        // 1. 内存检查
        $memoryUsed = memory_get_usage() / 1024 / 1024;
        if ($memoryUsed > $this->maxMemory) {
            echo "[" . date('Y-m-d H:i:s') . "] Memory limit exceeded: "
                . round($memoryUsed, 2) . "MB / {$this->maxMemory}MB\n";
            return false;
        }

        // 2. 任务数量检查
        if ($this->processedCount >= $this->maxJobs) {
            echo "[" . date('Y-m-d H:i:s') . "] Max jobs reached: {$this->processedCount}\n";
            return false;
        }

        return true;
    }

    /**
     * Shutdown 回调（进程退出时清理）
     */
    public function shutdown(): void
    {
        // 清理停止标记文件
        if (file_exists($this->stopFile)) {
            @unlink($this->stopFile);
        }
    }

    /**
     * 请求 Worker 停止（外部调用）
     */
    public function requestStop(): void
    {
        file_put_contents($this->stopFile, time());
        echo "[" . date('Y-m-d H:i:s') . "] Stop requested for queue: {$this->queueType}\n";
    }
}