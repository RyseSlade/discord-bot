<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

interface EventInterface
{
    // Subscribe to all events
    public const ALL = 'All';

    // Internal events
    public const HELLO = 'HELLO';
    public const READY = 'READY';
    public const HEARTBEAT_ACK = 'HEARTBEAT_ACK';
    public const RECONNECT = 'RECONNECT';
    public const INVALID_SESSION = 'INVALID_SESSION';
    public const RESUMED = 'RESUMED';

    /**
     * Name of the event. One of the constants defined in the interface
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Marks internal events that cannot be subscribed to
     *
     * @return bool
     */
    public function isInternal(): bool;

    /**
     * Return the data array
     *
     * @param string|null $key
     * @return mixed
     */
    public function getData(string $key = null);

    /**
     * Get the event sequence number
     *
     * @return int|null
     */
    public function getSequenceNumber(): ?int;
}