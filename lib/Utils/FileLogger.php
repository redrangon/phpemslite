<?php

namespace PHPEMS\Lib\Utils;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use DateTime;
class FileLogger extends AbstractLogger
{
    private $logFile;
    private $enabled;

    /**
     * @param string $logFile 日志文件完整路径，例如：storage/logs/ai.log
     * @param bool $enabled 是否启用日志（方便开关）
     */
    public function __construct(string $logFile = '', bool $enabled = true)
    {
        $this->logFile = $logFile;
        $this->enabled = $enabled;

        // 确保日志目录存在
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * 记录日志
     *
     * @param mixed $level 日志级别（如 'error', 'warning'）
     * @param string $message 日志消息
     * @param array $context 上下文数据（可选）
     */
    public function log($level, $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        // 只记录 error 及以上级别（可根据需要调整）
        $validLevels = [LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR];
        if (!in_array($level, $validLevels, true)) {
            return;
        }

        $timestamp = (new DateTime())->format('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);

        // 简单格式化上下文（用于异常堆栈等）
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $logLine = "[{$timestamp}] AI.{$levelUpper}: {$message}{$contextStr}" . PHP_EOL;

        // 写入文件（追加模式）
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}