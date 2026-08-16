# 任务系统 CLI 脚本使用文档

## 一、Worker 脚本

### 1. 脚本位置

```text
task/worker.php
```

### 2. 脚本内容

```php
#!/usr/task/env php
<?php

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
```

### 3. 运行方式

#### 方式一：使用 PHP 命令运行（推荐）

```bash
# 基本用法：启动 default 队列的 Worker
php task/worker.php

# 指定队列类型
php task/worker.php email

# 指定队列、最大任务数和内存限制
php task/worker.php email 50 256

# 查看帮助
php task/worker.php --help
php task/worker.php -h
```

#### 方式二：直接执行（需先赋予执行权限）

```bash
# 赋予执行权限
chmod +x task/worker.php

# 直接运行
./task/worker.php default

# 带参数运行
./task/worker.php email 50 256
```

#### 方式三：后台运行

**Linux / macOS**

```bash
# 使用 nohup 后台运行
nohup php task/worker.php default > /dev/null 2>&1 &

# 或使用 screen
screen -S worker-default
php task/worker.php default
# 按 Ctrl+A+D 脱离会话

# 或使用 tmux
tmux new -s worker-default
php task/worker.php default
# 按 Ctrl+B+D 脱离会话
```

**Windows**

```bash
# 使用 start /B 后台运行
start /B php task\worker.php default

# 或使用 PowerShell 的 Start-Job
Start-Job -ScriptBlock { php task\worker.php default }
```

### 4. 参数说明

| 参数 | 类型 | 默认值 | 说明 |
| :--- | :--- | :--- | :--- |
| queue | string | default | 队列类型，对应 task.tasktype 字段 |
| max_jobs | int | 100 | 处理多少任务后主动退出，防止内存泄漏 |
| max_memory | int | 128 | 最大内存限制（MB），超限后自动退出 |

### 5. 输出示例

```bash
$ php task/worker.php default
[2024-01-15 14:30:00] Starting worker
  Queue: default
  Max jobs: 100
  Max memory: 128MB
[2024-01-15 14:30:00] Worker started
  PID: 12345
  Queue: default
  Worker ID: worker_12345_66a1b2c3d4e5
  Stop file: /tmp/worker_default.stop
  Max jobs: 100, Max memory: 128MB
[2024-01-15 14:30:00] To stop: touch /tmp/worker_default.stop
[2024-01-15 14:30:05] Executing task #1: PHPEMS\App\Task\Job\TestJob
  → Hello, Worker!
  ✅ Task #1 completed in 0.05s
[2024-01-15 14:30:10] Executing task #2: PHPEMS\App\Task\Job\SendEmailJob
  → Sending email to user@example.com
  → Email sent successfully
  ✅ Task #2 completed in 1.23s
```

## 二、停止脚本

### 1. 脚本位置

```text
task/stoper.php
```

### 2. 脚本内容

```php
#!/usr/task/env php
<?php

$queue = $argv[1] ?? 'default';

// 生成停止标记文件路径
$stopFile = sys_get_temp_dir() . '/worker_' . $queue . '.stop';

// 写入停止标记
file_put_contents($stopFile, time());

echo "[" . date('Y-m-d H:i:s') . "] Stop signal sent to worker: {$queue}\n";
echo "  Stop file: {$stopFile}\n";
echo "  Worker will exit after completing current task.\n";
```

### 3. 运行方式

#### 方式一：使用 PHP 命令运行

```bash
# 停止 default 队列的 Worker
php task/stoper.php

# 停止指定队列的 Worker
php task/stoper.php email
php task/stoper.php report
```

#### 方式二：直接执行（需先赋予执行权限）

```bash
# 赋予执行权限
chmod +x task/stoper.php

# 直接运行
./task/stoper.php default
```

### 4. 输出示例

```bash
$ php task/stoper.php default
[2024-01-15 14:45:00] Stop signal sent to worker: default
  Stop file: /tmp/worker_default.stop
  Worker will exit after completing current task.
```

## 三、完整工作流示例

**场景：启动并管理多个 Worker**

### 1. 启动所有 Worker

```bash
# 启动 default 队列（2个进程）
php task/worker.php default 100 128 &
php task/worker.php default 100 128 &

# 启动 email 队列（2个进程）
php task/worker.php email 50 128 &
php task/worker.php email 50 128 &

# 启动 report 队列（1个进程）
php task/worker.php report 10 256 &
```

### 2. 查看运行中的 Worker

```bash
# Linux / macOS
ps aux | grep worker.php

# 输出示例：
# www-data 12345  0.5  1.2  php task/worker.php default
# www-data 12346  0.3  1.1  php task/worker.php default
# www-data 12347  0.4  1.3  php task/worker.php email
# www-data 12348  0.2  1.0  php task/worker.php email
# www-data 12349  0.1  2.1  php task/worker.php report
```

### 3. 停止所有 Worker

```bash
# 逐个停止
php task/stoper.php default
php task/stoper.php email
php task/stoper.php report

# 或批量停止（Shell 脚本）
for queue in default email report; do
    php task/stoper.php $queue
done
```

### 4. 强制清理（紧急情况）

```bash
# 如果 Worker 卡死，需要强制杀死
# 先找到 PID
ps aux | grep worker.php

# 然后强制杀死
kill -9 12345 12346 12347

# 清理遗留的停止标记文件
rm -f /tmp/worker_*.stop
```

## 四、Supervisor 管理方式

### 1. Supervisor 配置文件

```ini
# /etc/supervisor/conf.d/worker.conf

[program:worker-default]
command=php /path/to/task/worker.php default 100 128
process_name=%(program_name)s_%(process_num)02d
numprocs=2
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/worker-default.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=10
stopasgroup=true
killasgroup=true
stopwaitsecs=10

[program:worker-email]
command=php /path/to/task/worker.php email 50 128
numprocs=2
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/worker-email.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=10
stopasgroup=true
killasgroup=true
stopwaitsecs=10

[program:worker-report]
command=php /path/to/task/worker.php report 10 256
numprocs=1
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/worker-report.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=10
stopasgroup=true
killasgroup=true
stopwaitsecs=10
```

### 2. Supervisor 管理命令

```bash
# 查看所有 Worker 状态
supervisorctl status

# 启动特定 Worker
supervisorctl start worker-default:*

# 停止特定 Worker
supervisorctl stop worker-default:*

# 重启特定 Worker
supervisorctl restart worker-default:*

# 启动所有 Worker
supervisorctl start all

# 停止所有 Worker
supervisorctl stop all

# 重新加载配置
supervisorctl reread
supervisorctl update
```

## 五、常见问题

### 1. Worker 无法启动

```bash
# 检查 PHP 环境
php -v

# 检查文件权限
ls -la task/worker.php

# 检查 Composer 依赖
composer install

# 检查数据库连接
php task/worker.php --help
```

### 2. Worker 停止不生效

```bash
# 检查停止标记文件是否存在
ls -la /tmp/worker_*.stop

# 手动创建停止标记文件
touch /tmp/worker_default.stop

# 如果还不行，强制杀死
ps aux | grep worker.php
kill -9 [PID]
```

### 3. 查看 Worker 日志

```bash
# 如果使用 Supervisor
tail -f /var/log/worker-default.log

# 如果手动运行，输出在终端
# 或重定向到文件
php task/worker.php default > worker.log 2>&1 &
tail -f worker.log
```

## 六、命令速查表

| 操作 | 命令 |
| :--- | :--- |
| 启动 Worker | `php task/worker.php [queue] [max_jobs] [max_memory]` |
| 启动 default 队列 | `php task/worker.php default` |
| 启动 email 队列 | `php task/worker.php email 50 256` |
| 启动 report 队列 | `php task/worker.php report 10 256` |
| 停止 Worker | `php task/stoper.php [queue]` |
| 停止 default 队列 | `php task/stoper.php default` |
| 查看 Worker 状态 | `ps aux \| grep worker.php` |
| 查看帮助 | `php task/worker.php --help` |
| 查看 Supervisor 状态 | `supervisorctl status` |
| Supervisor 停止 Worker | `supervisorctl stop worker-default:*` |
| Supervisor 启动 Worker | `supervisorctl start worker-default:*` |
| Supervisor 重启 Worker | `supervisorctl restart worker-default:*` |

## 七、Windows 特别说明

### 1. PowerShell 中运行

```powershell
# 启动 Worker
php task\worker.php default

# 后台运行
Start-Job -ScriptBlock { php task\worker.php default }

# 查看后台任务
Get-Job

# 停止 Worker（创建停止文件）
php task\stoper.php default
```

### 2. CMD 中运行

```cmd
# 启动 Worker
php task\worker.php default

# 后台运行
start /B php task\worker.php default

# 停止 Worker
php task\stoper.php default
```

### 3. Windows 服务管理（NSSM）

```bash
# 安装为 Windows 服务
nssm install WorkerDefault "C:\php\php.exe" "C:\path\to\task\worker.php default"
nssm set WorkerDefault Start SERVICE_AUTO_START
nssm set WorkerDefault AppStdout "C:\logs\worker-default.log"
nssm start WorkerDefault
```

## 八、停止脚本原理说明

### 为什么需要单独的停止脚本？

Worker 是常驻进程，通过 `while(true)` 循环持续运行。停止脚本通过创建停止标记文件的方式通知 Worker 优雅退出。

### 对比项

| 对比项 | 强制杀死 (kill -9) | 停止脚本 |
| :--- | :--- | :--- |
| 跨平台 | ✅ 支持 | ✅ 支持 |
| 任务完整性 | ❌ 可能中断 | ✅ 完成当前任务 |
| 数据一致性 | ❌ 可能脏数据 | ✅ 一致 |
| 易用性 | ❌ 需要找 PID | ✅ 简单命令 |

### 流程说明

```text
启动 Worker → Worker 进入循环
                ↓
         每次循环检查停止文件
                ↓
         存在停止文件 → 退出循环
                ↓
        Worker 完成当前任务后停止
```

---

**文档版本**: v1.0  
**最后更新**: 2024-01-15