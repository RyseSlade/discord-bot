<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Event\Discord;

use Aedon\DiscordBot\Event\Discord\GenericEvent;
use PHPUnit\Framework\TestCase;

class GenericEventTest extends TestCase
{
    public function testShouldNotBeInternal(): void
    {
        $subject = new GenericEvent([
            's' => 99,
            't' => 'TEST',
            'd' => [
                'test' => true,
            ],
        ]);

        self::assertFalse($subject->isInternal());
    }

    public function testShouldProvideSequenceNumber(): void
    {
        $subject = new GenericEvent([
            's' => 99,
            't' => 'TEST',
            'd' => [
                'test' => true,
            ],
        ]);

        self::assertNotNull($subject->getSequenceNumber());
    }

    public function testShouldProvideName(): void
    {
        $subject = new GenericEvent([
            's' => 99,
            't' => 'TEST',
            'd' => [
                'test' => true,
            ],
        ]);

        self::assertEquals('TEST', $subject->getName());
    }

    public function testShouldReturnExpectedData(): void
    {
        $subject = new GenericEvent([
            's' => 99,
            't' => 'TEST',
            'd' => [
                'test' => true,
            ],
        ]);

        self::assertEquals(['test' => true], $subject->getData('d'));
    }
}