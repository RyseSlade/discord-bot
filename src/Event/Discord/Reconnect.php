<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event\Discord;

use Aedon\DiscordBot\Event\AbstractInternalEvent;
use Aedon\DiscordBot\Event\EventInterface;

final class Reconnect extends AbstractInternalEvent
{
    public function getName(): string
    {
        return EventInterface::RECONNECT;
    }
}