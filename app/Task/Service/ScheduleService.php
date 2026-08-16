<?php

namespace PHPEMS\App\Task\Service;

use Cron\CronExpression;
use PHPEMS\App\Task\Service\Model\TaskSchedules;
use PHPEMS\App\Task\Service\Model\Task;
use PHPEMS\Lib\Config\Site\Site;
use DateTime;
use Exception;

class ScheduleService
{
    private string $lockFile;
    private $lockHandle = null;

    /**
     * @param string $lockFile 锁文件路径（可选）
     */
    public function __construct()
    {
        $this->lockFile = DI(Site::class)->tmpDir. '/scheduler.lock';
    }

    /**
     * 执行一次完整的调度扫描
     *
     * 设计为单次执行，由外部进程管理工具（Supervisor）循环调用
     * 或者由调用方自行控制循环
     *
     * @return bool 是否成功执行
     */
    public function runOnce(): bool
    {
        // 获取锁（防止多个调度器实例同时执行）
        if (!$this->acquireLock()) {
            echo "[" . date('Y-m-d H:i:s') . "] Another scheduler is running, skipping...\n";
            return false;
        }

        try {
            $this->initializeNextRun();
            $this->scanAndSchedule();
            return true;
        } catch (Exception $e) {
            echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
            return false;
        } finally {
            $this->releaseLock();
        }
    }

    /**
     * 调度器主循环（兼容模式）
     *
     * 如果需要在 CLI 中持续运行，可以使用此方法
     * 但推荐使用 runOnce() + Supervisor 的方式
     */
    public function run(): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] Scheduler started (PID: " . getmypid() . ")\n";

        // 首次运行时初始化所有 tsnextrun
        $this->initializeNextRun();

        while (true) {
            $this->runOnce();

            // 每 3 秒扫描一次
            sleep(3);
        }
    }

    /**
     * 初始化所有循环任务的 tsnextrun
     */
    private function initializeNextRun(): void
    {
        $schedules = TaskSchedules::getQuery()->select(['tsid','tsloop','tslastrun'])
            ->where('tsenabled',1)
            ->where('tsnextrun',0)
            ->get();
        if (empty($schedules)) {
            return;
        }

        echo "[" . date('Y-m-d H:i:s') . "] Initializing " . count($schedules) . " schedules...\n";

        foreach ($schedules as $schedule) {
            $this->calculateAndUpdateNextRun($schedule);
        }
    }

    /**
     * 扫描并调度到期的循环任务
     */
    private function scanAndSchedule(): void
    {
        $now = time();

        // ============================================
        // 1. 查询所有需要调度的循环任务
        // ============================================

        $schedules = TaskSchedules::getQuery()->where('tsenabled',1)
            ->where('tsnextrun','>',0)
            ->where('tsnextrun','<=',$now)
            ->get();

        if (empty($schedules)) {
            return;
        }

        echo "[" . date('Y-m-d H:i:s') . "] Found " . count($schedules) . " schedules to process\n";

        // ============================================
        // 2. 遍历每个配置，生成任务实例
        // ============================================
        foreach ($schedules as $schedule) {
            $this->processSchedule($schedule, $now);
        }
    }

    /**
     * 处理单个循环任务配置
     */
    private function processSchedule(array $schedule, int $now): void
    {
        TaskSchedules::getDB()->transaction(
            function () use ($schedule, $now) {
                $tsid = $schedule['tsid'];
                // ============================================
                // 1. 计算下次执行时间
                // ============================================
                $nextRun = $this->calculateNextRun($schedule, $now);

                // ============================================
                // 2. 开启事务（保证一致性）
                // ============================================

                // ============================================
                // 3. 创建任务实例入队（INSERT INTO task）
                // ============================================
                $this->createTaskInstance($schedule, $now);

                // ============================================
                // 4. 更新调度配置
                // ============================================
                $this->updateSchedule($tsid, $now, $nextRun);
                echo "  → Scheduled #{$tsid}: {$schedule['tsname']}, next at " . date('Y-m-d H:i:s', $nextRun) . "\n";
            }
        );
    }

    /**
     * 计算下次执行时间
     */
    private function calculateNextRun(array $schedule, int $now): int
    {
        $cron = new CronExpression($schedule['tsloop']);

        // 获取上次执行时间
        $lastRun = $schedule['tslastrun'] > 0 ? $schedule['tslastrun'] : $now;

        // 计算下次执行时间
        $nextRun = $cron->getNextRunDate(new DateTime("@$lastRun"))->getTimestamp();

        // 如果下次执行时间 <= 当前时间，说明系统有延迟，立即执行
        if ($nextRun <= $now) {
            $nextRun = $now;
        }

        return $nextRun;
    }

    /**
     * 计算并更新单个循环任务的 tsnextrun
     * 用于初始化时设置首次执行时间
     */
    private function calculateAndUpdateNextRun(array $schedule): void
    {
        $tsid = $schedule['tsid'];

        try {
            // 1. 解析 Cron 表达式
            $cron = new CronExpression($schedule['tsloop']);

            // 2. 计算下次执行时间
            // 如果有 tslastrun，从它开始计算；否则从当前时间开始
            if ($schedule['tslastrun'] > 0) {
                $nextRun = $cron->getNextRunDate(
                    new DateTime("@{$schedule['tslastrun']}")
                )->getTimestamp();
            } else {
                $nextRun = $cron->getNextRunDate()->getTimestamp();
            }


            // 3. 更新数据库
            $model = TaskSchedules::find($tsid);
            $model->tsnextrun = $nextRun;
            $model->save();

            echo "  → Initialized #{$tsid}: next at " . date('Y-m-d H:i:s', $nextRun) . "\n";

        } catch (Exception $e) {
            echo "  → [ERROR] Initializing #{$tsid}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 创建任务实例（插入 task 表）
     */
    private function createTaskInstance(array $schedule, int $now): int
    {
        $model = Task::fillWithInit([
            'tasksid' => $schedule['tsid'],
            'taskname' => $schedule['tsname'],
            'taskclass' => $schedule['tsclass'],
            'taskpayload' => $schedule['tspayload'],
            'tasktype' => $schedule['tstype'],
            'taskmaxattempts' => $schedule['tsattempts'],
            'taskpriority' => $schedule['tspriority'],
            'tasktimeout' => 300,
            'taskstatus' => 'pending',
            'taskscheduletime' => $now,
            'taskattempts' => 0,
            'taskresult' => 0,
            'taskinfo' => '',
            'taskstarttime' => 0,
            'taskfinishtime' => 0
        ]);
        $result = $model->toValidate();
        if($result === true)
        {
            if(!$model->save())throw new Exception('保存任务实例失败');
        }
        else throw new Exception('插入任务实例失败');
        return (int) $model->taskId;
    }

    /**
     * 更新调度配置
     */
    private function updateSchedule(int $tsid, int $now, int $nextRun): void
    {
        $model = TaskSchedules::find($tsid);
        $model->tslastrun = $now;
        $model->tsnextrun = $nextRun;
        $model->tstimes = $model->tstimes + 1;
        $model->save();
    }

    /**
     * 获取分布式锁（使用文件锁，Windows 也支持）
     */
    private function acquireLock(): bool
    {
        $this->lockHandle = fopen($this->lockFile, 'w');
        if (!$this->lockHandle) {
            return false;
        }

        return flock($this->lockHandle, LOCK_EX | LOCK_NB);
    }

    /**
     * 释放分布式锁
     */
    private function releaseLock(): void
    {
        if ($this->lockHandle??false) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
        }
    }
}