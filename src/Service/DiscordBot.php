<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Service;

use Aedon\DiscordBot\Command\CommandList;
use Aedon\DiscordBot\Event\EventInterface;
use Aedon\DiscordBot\Event\EventSubscriberInterface;
use Aedon\DiscordBot\Event\InternalEventHandler;
use Aedon\DiscordBot\Event\InternalEventHandlerInterface;
use Aedon\DiscordBot\Rest\RestApiSubscriberInterface;
use Aedon\DiscordBot\Gateway\BotGateway;
use Aedon\DiscordBot\Log\ConsoleLogger;
use Aedon\DiscordBot\Message\MessageHandler;
use Aedon\DiscordBot\Message\MessageHandlerInterface;
use Aedon\DiscordBot\Rest\RestApi;
use Aedon\DiscordBot\Rest\RestApiInterface;
use Aedon\Expect;
use Exception;
use Psr\Log\LoggerInterface;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;
use Throwable;

final class DiscordBot
{
    private string $token;
    private LoggerInterface $logger;
    private BotGateway $botGateway;
    private InternalEventHandlerInterface $internalEventHandler;
    private MessageHandlerInterface $messageHandler;
    private RestApiInterface $restApi;
    private string $botGateWayUrl = '';

    /** @var EventSubscriberInterface[][] */
    private array $eventSubscribers = [
        EventInterface::ALL => [],
    ];

    public function __construct(
        string $token,
        RestApiInterface $restApi = null,
        MessageHandlerInterface $messageHandler = null,
        CommandList $commandList = null,
        InternalEventHandlerInterface $internalEventHandler = null,
        BotGateway $botGateway = null
    )
    {
        $this->token = $token;
        $this->restApi = $restApi ?? new RestApi($token);
        $this->messageHandler = $messageHandler ?? new MessageHandler();
        $this->botGateway = $botGateway ?? new BotGateway($token);
        $this->internalEventHandler = $internalEventHandler ?? new InternalEventHandler($token, $commandList ?? new CommandList());
        $this->logger = new ConsoleLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setBotGatewayUrl(string $botGatewayUrl): void
    {
        $this->botGateWayUrl = $botGatewayUrl;
    }

    public function subscribe(EventSubscriberInterface $subscriber, string $event = EventInterface::ALL): self
    {
        if (!isset($this->eventSubscribers[$event])) {
            $this->eventSubscribers[$event] = [];
        }

        $this->eventSubscribers[$event][] = $subscriber;

        return $this;
    }

    public function initialize(LoopInterface $loop, Connector $connector): void
    {
        $this->logger->info('Initializing Discord bot...');

        $botGatewayUrl = '';

        if (!empty($this->botGateWayUrl)) {
            $this->logger->info('Using supplied gateway url');
            $botGatewayUrl = $this->botGateWayUrl;
        } else if ($this->botGateway instanceof BotGateway) {
            $this->logger->info('Requesting gateway url...');
            $botGatewayUrl = $this->botGateway->getUrl();
            $this->botGateWayUrl = $botGatewayUrl;
        }

        Expect::isNotEmpty($botGatewayUrl);

        $this->logger->info('Gateway url: ' . $botGatewayUrl);

        $connector($botGatewayUrl)->then(
            function(WebSocket $webSocket) use ($loop) {
                $webSocket->on('message', function(MessageInterface $message) use ($webSocket, $loop) {
                    try {
                        $this->internalEventHandler->setWebSocket($webSocket);
                        $this->internalEventHandler->setLoop($loop);

                        $event = $this->messageHandler->convertToEvent($message);

                        if ($event instanceof EventInterface) {
                            $this->logger->info('Received event ' . $event->getName() . ($event->getSequenceNumber() ? ' (' . $event->getSequenceNumber() . ')' : ''));

                            if ($event->isInternal()) {
                                $this->internalEventHandler->process($event);
                            } else {
                                if (isset($this->eventSubscribers[$event->getName()])) {
                                    foreach ($this->eventSubscribers[$event->getName()] as $subscriber) {
                                        if ($subscriber instanceof RestApiSubscriberInterface) {
                                            $subscriber->setRestApi($this->restApi);
                                        }

                                        $subscriber->process($event);
                                    }
                                }

                                foreach ($this->eventSubscribers[EventInterface::ALL] as $subscriber) {
                                    if ($subscriber instanceof RestApiSubscriberInterface) {
                                        $subscriber->setRestApi($this->restApi);
                                    }

                                    $subscriber->process($event);
                                }
                            }

                            if ($event->getSequenceNumber() !== null) {
                                $this->internalEventHandler->updateSequenceNumber($event->getSequenceNumber());
                            }
                        } else {
                            $this->logger->info('Unhandled message received: ' . $message->getPayload());
                        }
                    } catch (Throwable $throwable) {
                        $this->logger->error($throwable->getMessage());

                        $webSocket->close();
                        $loop->stop();
                    }
                });

                $webSocket->on('close', function($code = null, $reason = null) use ($loop) {
                    $this->logger->info('Connection closed (' . $code . ')');

                    $loop->stop();
                });
            },
            function(Exception $exception) use ($loop) {
                $this->logger->error($exception->getMessage());

                $loop->stop();
            }
        );
    }
}