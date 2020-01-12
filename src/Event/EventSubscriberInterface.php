<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

interface EventSubscriberInterface
{
    public function process(EventInterface $event): void;
}