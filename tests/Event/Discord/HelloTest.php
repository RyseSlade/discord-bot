<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Event\Discord;

use Aedon\DiscordBot\Event\Discord\Hello;
use Aedon\DiscordBot\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class HelloTest extends TestCase
{
    public function testShouldBeInternal(): void
    {
        $subject = new Hello([
            'd' => [
                'heartbeat_interval' => 45000,
            ],
        ]);

        self::assertTrue($subject->isInternal());
    }

    public function testShouldNotProvideSequenceNumber(): void
    {
        $subject = new Hello([
            'd' => [
                'heartbeat_interval' => 45000,
            ],
        ]);

        self::assertNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new Hello([
            'd' => [
                'heartbeat_interval' => 45000,
            ],
        ]);

        self::assertEquals(EventInterface::HELLO, $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new Hello([
            'd' => [
                'heartbeat_interval' => 45000,
            ],
        ]);

        self::assertEquals(['heartbeat_interval' => 45000], $subject->getData('d'));
    }

    public function testShouldReturnHeartbeatInterval(): void
    {
        $subject = new Hello([
            'd' => [
                'heartbeat_interval' => 45000,
            ],
        ]);

        self::assertEquals(45, $subject->getHeartbeatInterval());
    }
}