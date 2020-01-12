<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Signal;

use FilesystemIterator;
use RuntimeException;
use SplFileInfo;
use function file_exists;
use function file_put_contents;
use function is_writable;
use function sleep;
use function time;
use function unlink;

final class Signal implements SignalInterface
{
    private const SIGNAL_WAIT_SECONDS = 30;
    private const SIGNAL_CHECK_INTERVAL_SECONDS = 15;

    /** @var string  */
    private $path;

    /** @var int  */
    private $waitSeconds;

    /** @var int  */
    private $checkIntervalSeconds;

    public function __construct(string $path, int $waitSeconds = self::SIGNAL_WAIT_SECONDS, int $checkIntervalSeconds = self::SIGNAL_CHECK_INTERVAL_SECONDS)
    {
        if (!is_writable($path)) {
            throw new RuntimeException('cannot write to signal path ' . $path);
        }

        $this->path = $path;
        $this->waitSeconds = $waitSeconds;
        $this->checkIntervalSeconds = $checkIntervalSeconds;
    }

    public function create(): bool
    {
        if ((new FilesystemIterator($this->path))->valid()) {
            return false;
        } else {
            $signalFile = $this->path . DIRECTORY_SEPARATOR . time();

            file_put_contents($signalFile, '');

            sleep($this->waitSeconds);

            if (!file_exists($signalFile)) {
                return false;
            }

            unlink($signalFile);
        }

        return true;
    }

    public function check(): void
    {
        $filesystemIterator = new FilesystemIterator($this->path);

        if (!$filesystemIterator->valid()) {
            return;
        }

        foreach ($filesystemIterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile()) {
                unlink($file->getPathname());
            }
        }
    }

    public function getCheckIntervalSeconds(): int
    {
        return $this->checkIntervalSeconds;
    }
}