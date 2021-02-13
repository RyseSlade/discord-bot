<?php

require('vendor/autoload.php');

$token = '';

if (!$token) {
    exit;
}

// Send a message "hello bot" to the bot and receive a greeting

// With the RestApiSubscriberInterface the rest api object will be injected when processing events
class SampleSubscriber implements \Aedon\DiscordBot\Event\EventSubscriberInterface, \Aedon\DiscordBot\Rest\RestApiSubscriberInterface
{
    private \Aedon\DiscordBot\Rest\RestApiInterface $restApi;

    public function process(\Aedon\DiscordBot\Event\EventInterface $event): void
    {
        echo 'Event ' . $event->getName() . ' received' . PHP_EOL;

        // Listen for the MESSAGE_CREATE event
        if ($event->getName() === 'MESSAGE_CREATE') {
            $data = $event->getData('d');

            // Check for a message "hello bot"
            if (isset($data['content']) && strtolower($data['content']) === 'hello bot') {
                if (isset($data['channel_id']) && !empty($data['channel_id'])) {
                    // Send "Hello World" message to the channel where the "hello bot" message was issued
                    $this->restApi->post('/channels/' . $data['channel_id'] . '/messages', [
                        'content' => 'Hello ' . $data['author']['username'] ?? 'unknown user',
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

do {
    $bot = new \Aedon\DiscordBot\Service\DiscordBot($token);

    $bot->subscribe(new SampleSubscriber(), \Aedon\DiscordBot\Event\EventInterface::ALL);

    $loop = \React\EventLoop\Factory::create();

    $bot->initialize($loop, new \Ratchet\Client\Connector($loop));

    $loop->run();

    sleep(10);
} while (true);