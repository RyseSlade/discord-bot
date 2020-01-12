<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Message;

use Aedon\DiscordBot\Event\EventInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

interface MessageHandlerInterface
{
    public function register(string $id, string $eventClass): self;
    public function convertToEvent(MessageInterface $message): ?EventInterface;
}