<?php

require('vendor/autoload.php');

$token = '';

if (!$token) {
    exit;
}

// This script will start a bot that periodically checks for a command file
// If there is a user id in the command file a message will be sent to that user and the file will be cleared

// Create a constant with command file path
define('COMMAND_FILE', __DIR__ . '/command.txt');

// Create an invokable class with access to the rest api
class Sender
{
    private \Aedon\DiscordBot\Rest\RestApi $restApi;

    public function __construct(\Aedon\DiscordBot\Rest\RestApi $restApi)
    {
        $this->restApi = $restApi;
    }

    public function __invoke(): void
    {
        echo 'Sender processing...' . PHP_EOL;

        if (!is_readable(COMMAND_FILE)) {
            echo 'No command file found' . PHP_EOL;
            return;
        }

        $userId = file_get_contents(COMMAND_FILE);

        if (!$userId) {
            echo 'Empty user id in command file' . PHP_EOL;
            return;
        }

        echo 'Requesting DM channel to user id ' . $userId . PHP_EOL;

        $response = $this->restApi->post('/users/@me/channels', [
            'recipient_id' => $userId,
        ]);

        $data = $response->getData();

        if (isset($data['id'])) {
            echo 'Creating message for user id ' . $userId . '; channel id ' . $data['id'] . PHP_EOL;

            $this->restApi->post('/channels/' . $data['id'] . '/messages', [
                'content' => 'Ping',
            ]);
        }

        file_put_contents(COMMAND_FILE, '');
    }
}

// Create rest api
$restApi = new \Aedon\DiscordBot\Rest\RestApi($token);

$sender = new Sender($restApi);

do {
    $bot = new \Aedon\DiscordBot\Service\DiscordBot($token);

    $loop = \React\EventLoop\Factory::create();

    // Add a periodic timer that will invoke the sender object every 5 seconds
    $loop->addPeriodicTimer(5, $sender);

    $bot->initialize($loop, new \Ratchet\Client\Connector($loop));

    $loop->run();

    sleep(10);
} while (true);