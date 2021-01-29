<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Command;

use Aedon\DiscordBot\Command\CommandInterface;
use Aedon\DiscordBot\Command\ResumeCommand;
use PHPUnit\Framework\TestCase;

class ResumeCommandTest extends TestCase
{
    public function testShouldReturnExpectedValues(): void
    {
        $subject = new ResumeCommand();

        self::assertEquals([
            'token' => '',
            'session_id' => '',
            'seq' => null,
        ], $subject->jsonSerialize());
        self::assertEquals(6, $subject->getOpcode());
        self::assertEquals(CommandInterface::RESUME, $subject->getName());
    }
}