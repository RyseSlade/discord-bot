<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Message;

use Aedon\DiscordBot\Event\Discord\GenericEvent;
use Aedon\DiscordBot\Event\Discord\Hello;
use Aedon\DiscordBot\Message\MessageHandler;
use PHPUnit\Framework\TestCase;
use Ratchet\RFC6455\Messaging\MessageInterface;
use function json_encode;

class MessageHandlerTest extends TestCase
{
    public function testShouldConvertToHelloEvent(): void
    {
        $subject = new MessageHandler();

        $message = $this->prophesize(MessageInterface::class);

        $data = [
            't' => null,
            's' => null,
            'op' => 10,
            'd' => [
                'heartbeat_interval' => 41250,
            ],
        ];

        $message->__toString()->willReturn(json_encode($data));

        /** @var Hello $result */
        $result = $subject->convertToEvent($message->reveal());

        self::assertInstanceOf(Hello::class, $result);
        self::assertEquals(41.250, $result->getHeartbeatInterval());
    }

    public function testShouldConvertToGenericEvent(): void
    {
        $subject = new MessageHandler();

        $message = $this->prophesize(MessageInterface::class);

        $data = [
            'op' => 0,
            's' => 123,
            't' => 'MESSAGE_CREATE',
            'd' => [
                'id' => '11111',
                'author' => [],
                'content' => 'Hello World',
                'webhook_id' => null,
            ],
        ];

        $message->__toString()->willReturn(json_encode($data));

        /** @var GenericEvent $result */
        $result = $subject->convertToEvent($message->reveal());

        self::assertInstanceOf(GenericEvent::class, $result);
        self::assertEquals(123, $result->getSequenceNumber());
        self::assertEquals('MESSAGE_CREATE', $result->getName());
        self::assertEquals($data, $result->getData());
    }
}