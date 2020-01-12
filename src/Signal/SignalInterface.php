<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Signal;

interface SignalInterface
{
    public function create(): bool;
    public function check(): void;
    public function getCheckIntervalSeconds(): int;
}