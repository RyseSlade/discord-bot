<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Command;

final class IdentifyCommand implements CommandInterface
{
    public function jsonSerialize(): array
    {
        return [
            'token' => '',
            'properties' => [
                '$os' => 'linux',
                '$browser' => 'Aedon Discord Bot',
                '$device' => 'Aedon Discord Bot',
            ],
            'compress' => false,
        ];
    }

    public function getOpcode(): int
    {
        return 2;
    }

    public function getName(): string
    {
        return CommandInterface::IDENTIFY;
    }
}