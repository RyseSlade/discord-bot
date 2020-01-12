<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Command;

final class ResumeCommand implements CommandInterface
{
    public function jsonSerialize(): array
    {
        return [
            'token' => '',
            'session_id' => '',
            'seq' => null,
        ];
    }

    public function getOpcode(): int
    {
        return 6;
    }

    public function getName(): string
    {
        return CommandInterface::RESUME;
    }
}