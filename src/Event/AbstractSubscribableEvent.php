<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

abstract class AbstractSubscribableEvent extends AbstractEvent
{
    public function isInternal(): bool
    {
        return false;
    }
}