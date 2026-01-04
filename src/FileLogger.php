<?php

namespace Hi;

use Hi\Attributes\Service;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

#[Service]
class FileLogger implements LoggerInterface
{
    private ?string $logFile;

    public function __construct(?string $logFile = null)
    {
        $this->logFile = $logFile;

        if ($logFile !== null) {
            $directory = dirname($logFile);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $logMessage = sprintf(
            "[%s] %s: %s%s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            empty($context) ? "" : " " . json_encode($context),
        );

        if ($this->logFile) {
            file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        } else {
            file_put_contents($this->getStreamForLevel($level), $logMessage, FILE_APPEND);
        }
    }

    private function getStreamForLevel(string $level): string
    {
        $errorLevels = [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
        ];

        return in_array($level, $errorLevels) ? 'php://stderr' : 'php://stdout';
    }
}
