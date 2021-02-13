<?php

require('vendor/autoload.php');

$token = '';

if (!$token) {
    exit;
}

do {
    // Create the bot
    $bot = new \Aedon\DiscordBot\Service\DiscordBot($token);

    // Create the loop
    $loop = \React\EventLoop\Factory::create();

    // Initialize the bot
    $bot->initialize($loop, new \Ratchet\Client\Connector($loop));

    // Start loop
    $loop->run();

    // Wait 10 seconds before recreating the cycle
    sleep(10);
} while (true);