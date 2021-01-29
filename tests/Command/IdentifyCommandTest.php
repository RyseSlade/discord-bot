<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Command;

use Aedon\DiscordBot\Command\CommandInterface;
use Aedon\DiscordBot\Command\IdentifyCommand;
use PHPUnit\Framework\TestCase;

class IdentifyCommandTest extends TestCase
{
    public function testShouldReturnExpectedValues(): void
    {
        $subject = new IdentifyCommand();

        self::assertEquals([
            'token' => '',
            'properties' => [
                '$os' => 'linux',
                '$browser' => 'Aedon Discord Bot',
                '$device' => 'Aedon Discord Bot',
            ],
            'compress' => false,
        ], $subject->jsonSerialize());
        self::assertEquals(2, $subject->getOpcode());
        self::assertEquals(CommandInterface::IDENTIFY, $subject->getName());
    }
}