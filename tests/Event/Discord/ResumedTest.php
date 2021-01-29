<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Event\Discord;

use Aedon\DiscordBot\Event\Discord\Resumed;
use Aedon\DiscordBot\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class ResumedTest extends TestCase
{
    public function testShouldBeInternal(): void
    {
        $subject = new Resumed([
            't' => EventInterface::RESUMED,
        ]);

        self::assertTrue($subject->isInternal());
    }

    public function testShouldNotProvideSequenceNumber(): void
    {
        $subject = new Resumed([
            't' => EventInterface::RESUMED,
        ]);

        self::assertNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new Resumed([
            't' => EventInterface::RESUMED,
        ]);

        self::assertEquals(EventInterface::RESUMED, $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new Resumed([
            't' => EventInterface::RESUMED,
        ]);

        self::assertEquals(['t' => EventInterface::RESUMED,], $subject->getData());
    }
}