<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Log;

use Aedon\DiscordBot\Log\ConsoleLogger;
use PHPUnit\Framework\TestCase;
use function ob_get_clean;
use function ob_start;

class ConsoleLoggerTest extends TestCase
{
    public function testShouldLogMessage(): void
    {
        $subject = new ConsoleLogger();

        ob_start();
        $subject->log('info', 'test');
        $content = ob_get_clean();

        self::assertStringContainsString('[info] test', $content);
    }

    public function provideTestData(): array
    {
        return [
            ['emergency', 'emergency', 'test'],
            ['alert', 'alert', 'test'],
            ['critical', 'critical', 'test'],
            ['error', 'error', 'test'],
            ['warning', 'warning', 'test'],
            ['notice', 'notice', 'test'],
            ['info', 'info', 'test'],
            ['debug', 'debug', 'test'],
        ];
    }

    /**
     * @dataProvider provideTestData
     */
    public function testShouldLogShortcutMessage(string $method, string $level, string $message): void
    {
        $subject = new ConsoleLogger();

        ob_start();
        $subject->{$method}($message);
        $content = ob_get_clean();

        self::assertStringContainsString('[' . $level . '] ' . $message, $content);
    }
}