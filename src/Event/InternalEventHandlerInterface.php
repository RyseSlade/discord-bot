<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

use Ratchet\Client\WebSocket;
use React\EventLoop\LoopInterface;

interface InternalEventHandlerInterface
{
    public function setWebSocket(WebSocket $webSocket): self;
    public function setLoop(LoopInterface $loop): self;
    public function updateSequenceNumber(int $sequenceNumber): void;
    public function process(EventInterface $event): void;
}