<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event\Discord;

use Aedon\DiscordBot\Event\AbstractInternalEvent;
use Aedon\DiscordBot\Event\EventInterface;

final class InvalidSession extends AbstractInternalEvent
{
    private bool $canResume = false;

    public function getName(): string
    {
        return EventInterface::INVALID_SESSION;
    }

    protected function convertData(array $data): void
    {
        $this->canResume = (bool)$data['d'];
    }

    public function canResume(): bool
    {
        return $this->canResume;
    }
}