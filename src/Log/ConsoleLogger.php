<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Log;

use Psr\Log\LoggerInterface;
use function date;

final class ConsoleLogger implements LoggerInterface
{
    /**
     * @param string $message
     * @param string[] $context
     */
    public function emergency($message, array $context = array()): void
    {
        $this->log('emergency', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function alert($message, array $context = array()): void
    {
        $this->log('alert', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function critical($message, array $context = array()): void
    {
        $this->log('critical', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function error($message, array $context = array()): void
    {
        $this->log('error', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function warning($message, array $context = array()): void
    {
        $this->log('warning', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function notice($message, array $context = array()): void
    {
        $this->log('notice', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function info($message, array $context = array()): void
    {
        $this->log('info', $message);
    }

    /**
     * @param string $message
     * @param string[] $context
     */
    public function debug($message, array $context = array()): void
    {
        $this->log('debug', $message);
    }

    /**
     * @param string $level
     * @param string $message
     * @param string[] $context
     */
    public function log($level, $message, array $context = array()): void
    {
        echo date('Y-m-d H:i:s') . ' [' . (string)$level . '] ' . (string)$message . PHP_EOL;
    }
}