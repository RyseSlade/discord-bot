<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

abstract class AbstractInternalEvent extends AbstractEvent
{
    public function isInternal(): bool
    {
        return true;
    }
}