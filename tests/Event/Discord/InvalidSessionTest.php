<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Event\Discord;

use Aedon\DiscordBot\Event\Discord\InvalidSession;
use Aedon\DiscordBot\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class InvalidSessionTest extends TestCase
{
    public function testShouldBeInternal(): void
    {
        $subject = new InvalidSession([
            'd' => true,
        ]);

        self::assertTrue($subject->isInternal());
    }

    public function testShouldNotProvideSequenceNumber(): void
    {
        $subject = new InvalidSession([
            'd' => true,
        ]);

        self::assertNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new InvalidSession([
            'd' => true,
        ]);

        self::assertEquals(EventInterface::INVALID_SESSION, $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new InvalidSession([
            'd' => true,
        ]);

        self::assertTrue($subject->getData('d'));
    }

    public function testShouldBeResumable(): void
    {
        $subject = new InvalidSession([
            'd' => true,
        ]);

        self::assertTrue($subject->canResume());
    }
}