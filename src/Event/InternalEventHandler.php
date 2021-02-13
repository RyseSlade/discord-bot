<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

use Aedon\DiscordBot\Command\CommandInterface;
use Aedon\DiscordBot\Command\CommandList;
use Aedon\DiscordBot\Event\Discord\Hello;
use Aedon\DiscordBot\Event\Discord\InvalidSession;
use Aedon\DiscordBot\Event\Discord\Ready;
use Aedon\Expect;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\Frame;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use function array_merge;
use function assert;
use function json_encode;

class InternalEventHandler implements InternalEventHandlerInterface
{
    private string $token;
    private CommandList $commandList;

    private ?WebSocket $webSocket = null;
    private ?LoopInterface $loop = null;

    private bool $isHeartbeatStarted = false;
    private bool $heartbeatACKReceived = true;
    private string $sessionId = '';
    private ?int $sequenceNumber = null;

    public function __construct(string $token, CommandList $commandList)
    {
        $this->token = $token;
        $this->commandList = $commandList;
    }

    public function setWebSocket(WebSocket $webSocket): void
    {
        $this->webSocket = $webSocket;
    }

    public function setLoop(LoopInterface $loop): void
    {
        $this->loop = $loop;
    }

    public function updateSequenceNumber(int $sequenceNumber): void
    {
        $this->sequenceNumber = $sequenceNumber;
    }

    private function sendCommand(string $name, array $mergeData = []): void
    {
        assert($this->webSocket instanceof WebSocket);

        $commandClass = $this->commandList->getCommand($name);

        Expect::isNotNull($commandClass);

        /** @var CommandInterface $command */
        $command = new $commandClass();

        Expect::isInstanceOf($command, CommandInterface::class);

        /** @var string $payload */
        $payload = json_encode([
            'op' => $command->getOpcode(),
            'd' => array_merge($command->jsonSerialize(), $mergeData),
        ]);

        $payloadLength = mb_strlen($payload);

        Expect::isNotFalse($payload);
        Expect::isLowerThanOrEqual($payloadLength, 4096);

        $this->webSocket->send(new Frame($payload, true, $command->getOpcode()));
    }

    public function process(EventInterface $event): void
    {
        switch ($event->getName()) {
            case EventInterface::HELLO:
                assert($event instanceof Hello);

                if ($this->sessionId) {
                    $this->resume();
                    $this->isHeartbeatStarted = false;
                } else {
                    $this->identify();
                }

                $this->startHeartbeat($event->getHeartbeatInterval());

                break;

            case EventInterface::READY:
                assert($event instanceof Ready);

                $this->sessionId = $event->getSessionId();

                break;

            case EventInterface::HEARTBEAT_ACK:
                $this->heartbeatACKReceived = true;

                break;

            case EventInterface::RECONNECT:
                $this->shutdown();

                break;

            case EventInterface::INVALID_SESSION:
                assert($event instanceof InvalidSession);

                $this->invalidateSession($event);

                break;
        }
    }

    private function startHeartbeat(float $heartbeatInterval): void
    {
        if ($this->isHeartbeatStarted || !$heartbeatInterval) {
            return;
        }

        assert($this->loop instanceof LoopInterface);

        $this->loop->addPeriodicTimer($heartbeatInterval, function(TimerInterface $timer) {
            if (!$this->heartbeatACKReceived) {
                $this->shutdown();
            } else {
                $this->sendCommand(CommandInterface::HEARTBEAT, ['d' => $this->sequenceNumber]);

                $this->heartbeatACKReceived = false;
            }
        });

        $this->isHeartbeatStarted = true;
    }

    private function identify(): void
    {
        $this->sendCommand(CommandInterface::IDENTIFY, ['token' => $this->token]);
    }

    private function resume(): void
    {
        $this->sendCommand(CommandInterface::RESUME, [
            'token' => $this->token,
            'session_id' => $this->sessionId,
            'seq' => $this->sequenceNumber,
        ]);
    }

    private function invalidateSession(InvalidSession $event): void
    {
        $this->shutdown();
    }

    private function shutdown(): void
    {
        assert($this->webSocket instanceof WebSocket);

        $this->webSocket->close();

        $this->sequenceNumber = null;
        $this->sessionId = '';
        $this->isHeartbeatStarted = false;
        $this->heartbeatACKReceived = false;
    }
}