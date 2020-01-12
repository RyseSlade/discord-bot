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

    // Subscribable events
    public const MESSAGE_CREATE = 'MESSAGE_CREATE';
    public const MESSAGE_UPDATE = 'MESSAGE_UPDATE';
    public const MESSAGE_DELETE = 'MESSAGE_DELETE';

    public const MESSAGE_REACTION_ADD = 'MESSAGE_REACTION_ADD';
    public const MESSAGE_REACTION_REMOVE = 'MESSAGE_REACTION_REMOVE';

    public const GUILD_CREATE = 'GUILD_CREATE';
    public const GUILD_DELETE = 'GUILD_DELETE';
    public const GUILD_MEMBER_UPDATE = 'GUILD_MEMBER_UPDATE';
    public const GUILD_MEMBER_REMOVE = 'GUILD_MEMBER_REMOVE';
    public const GUILD_ROLE_UPDATE = 'GUILD_ROLE_UPDATE';

    public const TYPING_START = 'TYPING_START';

    public const CHANNEL_CREATE = 'CHANNEL_CREATE';
    public const CHANNEL_UPDATE = 'CHANNEL_UPDATE';
    public const CHANNEL_DELETE = 'CHANNEL_DELETE';
    public const CHANNEL_PINS_UPDATE = 'CHANNEL_PINS_UPDATE';

    public const PRESENCE_UPDATE = 'PRESENCE_UPDATE';

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
     * @param string $key
     * @return mixed[]
     */
    public function getData(string $key = null): array;

    /**
     * Get the event sequence number
     *
     * @return int|null
     */
    public function getSequenceNumber(): ?int;
}