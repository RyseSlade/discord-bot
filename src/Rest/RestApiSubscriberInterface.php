<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Rest;

interface RestApiSubscriberInterface
{
    public function setRestApi(RestApiInterface $restApi): void;
}