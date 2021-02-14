<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Service;

use Aedon\DiscordBot\Log\ConsoleLogger;
use Aedon\DiscordBot\Rest\RestApi;
use Aedon\DiscordBot\Rest\RestApiInterface;
use Psr\Log\LoggerInterface;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\Frame;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use function json_decode;
use function json_encode;

class SimpleBot
{
    private string $token;
    private RestApiInterface $restApi;
    private LoggerInterface $logger;

    private string $sessionId = '';
    private int $sequenceNumber = 0;
    private bool $heartbeatACKReceived = false;

    public function __construct(
        string $token,
        RestApiInterface $restApi = null
    )
    {
        $this->token = $token;
        $this->restApi = $restApi ?? new RestApi($token);
        $this->logger = new ConsoleLogger();
    }

    public function initialize(LoopInterface $loop, Connector $connector): void
    {
        $connector('wss://gateway.discord.gg/?v=6&encoding=json')->then(
            function(WebSocket $webSocket) use ($loop) {
                $webSocket->on('message', function(MessageInterface $message) use ($webSocket, $loop) {
                    $this->logger->info($message->getPayload());

                    $data = json_decode($message->getPayload(), true);

                    if (isset($data['op']) && $data['op'] !== 0) {
                        // HELLO
                        if ($data['op'] == 10) {
                            $this->logger->info('HELLO received');

                            if (!$this->sessionId) {
                                $this->send($webSocket, 2, 'IDENTIFY', [
                                    'op' => 2,
                                    'd' => [
                                        'token' => $this->token,
                                        'properties' => [
                                            '$os' => 'linux',
                                            '$browser' => 'Aedon Discord Bot',
                                            '$device' => 'Aedon Discord Bot',
                                        ],
                                        'compress' => false,
                                    ],
                                ]);

                                $heartbeatInterval = (float)($data['d']['heartbeat_interval'] / 1000);

                                $this->logger->info('Starting heartbeat interval ' . $heartbeatInterval);

                                $this->heartbeatACKReceived = true;

                                $this->createHeartbeatTimer($loop, $webSocket, $heartbeatInterval);
                            } else {
                                $this->send($webSocket, 6, 'RESUME', [
                                    'op' => 6,
                                    'd' => [
                                        'token' => $this->token,
                                        'session_id' => $this->sessionId,
                                        'seq' => $this->sequenceNumber,
                                    ],
                                ]);

                                $heartbeatInterval = (float)($data['d']['heartbeat_interval'] / 1000);

                                $this->logger->info('Starting heartbeat interval ' . $heartbeatInterval);

                                $this->heartbeatACKReceived = true;

                                $this->createHeartbeatTimer($loop, $webSocket, $heartbeatInterval);
                            }
                        } else if ($data['op'] == 11) { // HEARTBEAT ACK
                            $this->logger->info('HEARTBEAT ACK received');
                            $this->heartbeatACKReceived = true;
                        } else if ($data['op'] == 1) { // HEARTBEAT request
                            $this->logger->info('HEARTBEAT requested');

                            $this->send($webSocket, 1, 'HEARTBEAT', [
                                'op' => 1,
                                'd' => $this->sequenceNumber,
                            ]);
                        } else {
                            $this->logger->info('Unspecified system event ' . $data['op'] . ' received');
                        }
                    } else {
                        // READY
                        if (isset($data['t']) && $data['t'] == 'READY') {
                            $this->logger->info('READY received');

                            $this->sessionId = (string)$data['d']['session_id'];
                        } else {
                            $this->logger->info('Unspecified event ' . $data['t'] . ' received');
                        }
                    }

                    if (isset($data['s']) && $data['s']) {
                        $this->sequenceNumber = (int)$data['s'];
                    }

                    $this->logger->info('SequenceNumber: ' . $this->sequenceNumber);
                });
                $webSocket->on('close', function($code = null, $reason = null) use ($loop) {
                    $this->logger->info('Connection closed (' . $code . ')');
                    $loop->stop();
                });
            }
        );
    }

    private function createHeartbeatTimer(LoopInterface $loop, WebSocket $webSocket, float $heartbeatInterval): void
    {
        $loop->addPeriodicTimer($heartbeatInterval, function(TimerInterface $timer) use ($webSocket) {
            if (!$this->heartbeatACKReceived) {
                $this->logger->info('No heartbeat received. Disconnecting...');
                $webSocket->close(6000);
            } else {
                $this->send($webSocket, 1, 'HEARTBEAT', [
                    'op' => 1,
                    'd' => $this->sequenceNumber,
                ]);

                $this->heartbeatACKReceived = false;
            }
        });
    }

    private function send(WebSocket $webSocket, int $op, string $label, array $data): void
    {
        /** @var string $payload */
        $payload = json_encode($data);

        $this->logger->info('Sending ' . $label);
        $this->logger->info($payload);

        $webSocket->send(new Frame($payload, true, $op));
    }
}