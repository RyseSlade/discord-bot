<?php

require('vendor/autoload.php');

$token = '';

if (!$token) {
    exit;
}

define('LOCK_FILE', __DIR__ . '/bot.lock');

if (is_readable(LOCK_FILE)) {
    $lockedDate = file_get_contents(LOCK_FILE);
    $testDate = date('Y-m-d H:i:s', strtotime('now - 2 minutes'));

    if (DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $lockedDate) > DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $testDate)) {
        echo 'Bot already running' . PHP_EOL;
        exit;
    }
} else {
    if (!file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'))) {
        echo 'Could not create lock file. Missing write access for ' . LOCK_FILE . PHP_EOL;
        exit;
    }
}

if (!file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'))) {
    echo 'Could not create lock file. Missing write access for ' . LOCK_FILE . PHP_EOL;
    exit;
}

do {
    $bot = new \Aedon\DiscordBot\Service\DiscordBot($token);

    $loop = \React\EventLoop\Factory::create();

    $loop->addPeriodicTimer(60, function() {
        file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'));
    });

    $bot->initialize($loop, new \Ratchet\Client\Connector($loop));

    $loop->run();

    sleep(10);
} while (true);