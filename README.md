# Aedon Discord Bot

[![GitHub release](https://img.shields.io/github/v/release/RyseSlade/discord-bot.svg)](https://github.com/RyseSlade/discord-bot/releases/)
[![Build Status](https://travis-ci.org/RyseSlade/discord-bot.svg?branch=master)](https://travis-ci.org/RyseSlade/discord-bot)
[![GitHub license](https://img.shields.io/badge/license-MIT-green)](https://github.com/RyseSlade/discord-bot/blob/master/LICENSE)

A Discord bot implementation written in PHP.

* Integrates the Discord websocket API
* Adds basic support for the Discord REST API

### Requirements

PHP 7.4

Required extensions:
* ext-curl
* ext-json
* ext-mbstring

There is also a backport for PHP 7.3 available.

### Getting your bot up and running

You have created a Discord bot and it's now in your server but _offline_ all day... Let's start!

#### Bring your bot to life

Create a PHP file and run it from the command line.

```php
<?php

require('vendor/autoload.php');

$token = '<insert-your-bot-token>';

$bot = new \Aedon\DiscordBot\Service\DiscordBot($token);

$bot->initialize()->run();
```

Even though your bot won't do much for now it should actually be online and it should receive heartbeat events in the console.

#### Subscribe to events

The bot receives events from Discord now. You can subscribe to a specific event or all events. In this example you will listen to MESSAGE_CREATE events.

```php
class MySubscriber implements \Aedon\Discordbot\Event\EventSubscriberInterface
{
    public function process(\Aedon\Discordbot\Event\EventInterface $event): void
    {
        print_r($event->getData('d'));
    }
}

$bot->subscribe(new MySubscriber(), \Aedon\DiscordBot\Event\EventInterface::MESSAGE_CREATE);

$bot->initialize()->run();
```

#### Let the bot do something in Discord

The bot library has very basic support for the Discord REST API that is used to e.g. create a message in Discord.

Add the RestApiSubscriberInterface to your subscriber and it will get access to the rest api object.

```php
class MySubscriber implements \Aedon\Discordbot\Event\EventSubscriberInterface, 
    \Aedon\DiscordBot\Rest\RestApiSubscriberInterface
{
    private \Aedon\DiscordBot\Rest\RestApiInterface $restApi;

    public function process(\Aedon\Discordbot\Event\EventInterface $event): void
    {
        if ($event->getName() == \Aedon\DiscordBot\Event\EventInterface::MESSAGE_CREATE) {
            $data = $event->getData('d');

            if (isset($data['content']) && $data['content'] == '/roll') {
                if (isset($data['channel_id']) && !empty($data['channel_id'])) {
                    $this->restApi->post('/channels/' . $data['channel_id'] . '/messages', [
                        'content' => 'Roll Result (1-6): ' . (int)mt_rand(1, 6),
                    ]);
                }
            }
        }
    }

    public function setRestApi(\Aedon\DiscordBot\Rest\RestApiInterface $restApi): void
    {
        $this->restApi = $restApi;
    }
}
```

The bot will listen for MESSAGE_CREATE events and when someone writes "/roll" it will answer back to the user with "Roll Result (1-6): X".

### FAQ

#### How do you execute the bot script?

Probably not the best way to do it but you can create a cron job and run the PHP script like every 5 minutes. There is a basic locking mechanism available that you can use to prevent running multiple bot processes at the same time.

```php
$bot->setSignal(new \Aedon\DiscordBot\Signal\Signal('<path to an empty writable directory>'));
```

### Support

Join Discord: https://discord.gg/xhk7dN

Aedon Discord Bot created by Michael "Striker" Berger
