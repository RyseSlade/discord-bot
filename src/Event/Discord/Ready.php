<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event\Discord;

use Aedon\DiscordBot\Event\AbstractInternalEvent;

final class Ready extends AbstractInternalEvent
{
    /** @var string  */
    private $sessionId = '';

    protected function convertData(array $data): void
    {
        if (isset($data['d']) && is_array($data['d'])) {
            $this->sessionId = $data['d']['session_id'] ?? '';
        }
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}