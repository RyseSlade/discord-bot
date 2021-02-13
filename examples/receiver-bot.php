<?php

require('vendor/autoload.php');

$token = '';

if (!$token) {
    exit;
}

class SampleSubscriber implements \Aedon\DiscordBot\Event\EventSubscriberInterface
{
    public function process(\Aedon\DiscordBot\Event\EventInterface $event): void
    {
        echo 'Event ' . $event->getName() . ' received' . PHP_EOL;
        print_r($event->getData());
    }
}

do {
    $bot = new \Aedon\DiscordBot\Service\DiscordBot($token);

    // Subscribe the bot to the event listener - listen for all events
    $bot->subscribe(new SampleSubscriber(), \Aedon\DiscordBot\Event\EventInterface::ALL);

    $loop = \React\EventLoop\Factory::create();

    $bot->initialize($loop, new \Ratchet\Client\Connector($loop));

    $loop->run();

    sleep(10);
} while (true);