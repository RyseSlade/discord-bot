<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event\Discord;

use Aedon\DiscordBot\Event\AbstractInternalEvent;
use Aedon\DiscordBot\Event\EventInterface;

final class Hello extends AbstractInternalEvent
{
    private float $heartbeatInterval = 0.0;

    public function getName(): string
    {
        return EventInterface::HELLO;
    }

    protected function convertData(array $data): void
    {
        if (isset($data['d']['heartbeat_interval'])) {
            $this->heartbeatInterval = (float)$data['d']['heartbeat_interval'] / 1000;
        }
    }

    public function getHeartbeatInterval(): float
    {
        return $this->heartbeatInterval;
    }
}