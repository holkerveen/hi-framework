<?php

namespace Hi;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FileLogger extends AbstractLogger implements LoggerInterface
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
