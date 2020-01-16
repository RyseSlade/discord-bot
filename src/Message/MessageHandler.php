<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Message;

use Aedon\DiscordBot\Event\Discord\GenericEvent;
use Aedon\DiscordBot\Event\Discord\HeartbeatACK;
use Aedon\DiscordBot\Event\Discord\Hello;
use Aedon\DiscordBot\Event\Discord\InvalidSession;
use Aedon\DiscordBot\Event\Discord\Ready;
use Aedon\DiscordBot\Event\Discord\Reconnect;
use Aedon\DiscordBot\Event\EventInterface;
use Aedon\Expect;
use Ratchet\RFC6455\Messaging\MessageInterface;
use function json_decode;

final class MessageHandler implements MessageHandlerInterface
{
    /** @var string[] */
    private $messageEvents = [
        // System events identified by their op code
        10 => Hello::class,
        11 => HeartbeatACK::class,
        7 => Reconnect::class,
        9 => InvalidSession::class,

        // System events identified by their name
        EventInterface::READY => Ready::class,
    ];

    public function register(string $id, string $eventClass): MessageHandlerInterface
    {
        $this->messageEvents[$id] = $eventClass;

        return $this;
    }

    public function convertToEvent(MessageInterface $message): ?EventInterface
    {
        $data = json_decode((string)$message, true);

        if ($data === null || !is_array($data)) {
            return null;
        }

        $opcode = isset($data['op']) && is_numeric($data['op']) ? (int)$data['op'] : 0;
        $key = $opcode !== 0 ? $opcode : (string)$data['t'];

        if (isset($this->messageEvents[$key])) {
            /** @var EventInterface $event */
            $event = new $this->messageEvents[$key]($data);

            Expect::isInstanceOf($event, EventInterface::class);

            return $event;
        } else if ($opcode === 0 && isset($data['t']) && is_string($data['t'])) {
            return new GenericEvent($data);
        }

        return null;
    }
}