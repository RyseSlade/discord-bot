<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Command;

use Aedon\DiscordBot\Command\CommandInterface;
use Aedon\DiscordBot\Command\HeartbeatCommand;
use PHPUnit\Framework\TestCase;

class HeartbeatCommandTest extends TestCase
{
    public function testShouldReturnExpectedValues(): void
    {
        $subject = new HeartbeatCommand();

        self::assertEquals(['d' => null], $subject->jsonSerialize());
        self::assertEquals(1, $subject->getOpcode());
        self::assertEquals(CommandInterface::HEARTBEAT, $subject->getName());
    }
}