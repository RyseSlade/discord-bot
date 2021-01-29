<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Command;

use Aedon\DiscordBot\Command\CommandInterface;
use Aedon\DiscordBot\Command\CommandList;
use Aedon\DiscordBot\Command\HeartbeatCommand;
use Aedon\DiscordBot\Command\IdentifyCommand;
use Aedon\DiscordBot\Command\ResumeCommand;
use PHPUnit\Framework\TestCase;

class CommandListTest extends TestCase
{
    public function testShouldReturnDefaultCommands(): void
    {
        $subject = new CommandList();

        self::assertEquals(ResumeCommand::class, $subject->getCommand(CommandInterface::RESUME));
        self::assertEquals(IdentifyCommand::class, $subject->getCommand(CommandInterface::IDENTIFY));
        self::assertEquals(HeartbeatCommand::class, $subject->getCommand(CommandInterface::HEARTBEAT));
    }

    public function testShouldReturnRegisteredCommand(): void
    {
        $subject = new CommandList();

        $subject->register('test', 'TestClass');

        self::assertEquals('TestClass', $subject->getCommand('test'));
    }
}