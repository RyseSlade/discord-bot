<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Event\Discord;

use Aedon\DiscordBot\Event\Discord\HeartbeatACK;
use Aedon\DiscordBot\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class HeartbeatACKTest extends TestCase
{
    public function testShouldBeInternal(): void
    {
        $subject = new HeartbeatACK([]);

        self::assertTrue($subject->isInternal());
    }

    public function testShouldNotProvideSequenceNumber(): void
    {
        $subject = new HeartbeatACK([]);

        self::assertNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new HeartbeatACK([]);

        self::assertEquals(EventInterface::HEARTBEAT_ACK, $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new HeartbeatACK([]);

        self::assertEquals([], $subject->getData());
    }
}