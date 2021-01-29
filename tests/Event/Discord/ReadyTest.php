<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Event\Discord;

use Aedon\DiscordBot\Event\Discord\Ready;
use Aedon\DiscordBot\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class ReadyTest extends TestCase
{
    public function testShouldBeInternal(): void
    {
        $subject = new Ready([
            't' => EventInterface::READY,
            'd' => [
                'session_id' => '123',
            ],
        ]);

        self::assertTrue($subject->isInternal());
    }

    public function testShouldNotProvideSequenceNumber(): void
    {
        $subject = new Ready([
            't' => EventInterface::READY,
            'd' => [
                'session_id' => '123',
            ],
        ]);

        self::assertNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new Ready([
            't' => EventInterface::READY,
            'd' => [
                'session_id' => '123',
            ],
        ]);

        self::assertEquals(EventInterface::READY, $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new Ready([
            't' => EventInterface::READY,
            'd' => [
                'session_id' => '123',
            ],
        ]);

        self::assertEquals(['session_id' => '123'], $subject->getData('d'));
    }

    public function testShouldReturnSessionId(): void
    {
        $subject = new Ready([
            't' => EventInterface::READY,
            'd' => [
                'session_id' => '123',
            ],
        ]);

        self::assertEquals('123', $subject->getSessionId());
    }
}