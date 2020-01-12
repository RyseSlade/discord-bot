<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Command;

final class HeartbeatCommand implements CommandInterface
{
    public function jsonSerialize(): array
    {
        return [
            'd' => null,
        ];
    }

    public function getOpcode(): int
    {
        return 1;
    }

    public function getName(): string
    {
        return CommandInterface::HEARTBEAT;
    }
}