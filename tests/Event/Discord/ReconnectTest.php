<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Event\Discord;

use Aedon\DiscordBot\Event\Discord\Reconnect;
use Aedon\DiscordBot\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class ReconnectTest extends TestCase
{
    public function testShouldBeInternal(): void
    {
        $subject = new Reconnect([]);

        self::assertTrue($subject->isInternal());
    }

    public function testShouldNotProvideSequenceNumber(): void
    {
        $subject = new Reconnect([]);

        self::assertNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new Reconnect([]);

        self::assertEquals(EventInterface::RECONNECT, $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new Reconnect([]);

        self::assertEquals([], $subject->getData());
    }
}