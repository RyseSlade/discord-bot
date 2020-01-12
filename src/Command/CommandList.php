<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Command;

final class CommandList
{
    /** @var string[] */
    private $commands = [
        CommandInterface::IDENTIFY => IdentifyCommand::class,
        CommandInterface::RESUME => ResumeCommand::class,
        CommandInterface::HEARTBEAT => HeartbeatCommand::class,
    ];

    public function register(string $name, string $commandClass): self
    {
        $this->commands[$name] = $commandClass;

        return $this;
    }

    public function getCommand(string $name): ?string
    {
        return $this->commands[$name] ?? null;
    }
}