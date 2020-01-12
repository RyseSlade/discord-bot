<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Command;

use JsonSerializable;

interface CommandInterface extends JsonSerializable
{
    public const IDENTIFY = 'identify';
    public const HEARTBEAT = 'heartbeat';
    public const RESUME = 'resume';

    public function getOpcode(): int;
    public function getName(): string;
}