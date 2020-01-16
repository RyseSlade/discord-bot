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
    /** @var string  */
    private $token;

    /** @var CommandList  */
    private $commandList;

    /** @var WebSocket|null  */
    private $webSocket = null;

    /** @var LoopInterface|null  */
    private $loop = null;

    /** @var bool  */
    private $isHeartbeatStarted = false;

    /** @var bool  */
    private $heartbeatACKReceived = true;

    /** @var string  */
    private $sessionId = '';

    /** @var int|null  */
    private $sequenceNumber = null;

    public function __construct(string $token, CommandList $commandList)
    {
        $this->token = $token;
        $this->commandList = $commandList;
    }

    public function setWebSocket(WebSocket $webSocket): InternalEventHandlerInterface
    {
        if ($this->webSocket instanceof WebSocket) {
            return $this;
        }

        $this->webSocket = $webSocket;

        return $this;
    }

    public function setLoop(LoopInterface $loop): InternalEventHandlerInterface
    {
        if ($this->loop instanceof LoopInterface) {
            return $this;
        }

        $this->loop = $loop;

        return $this;
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

        Expect::isNotFalse($payload);
        Expect::isLowerThanOrEqual(mb_strlen($payload), 4096);

        $this->webSocket->send(new Frame($payload, true, $command->getOpcode()));
    }

    public function process(EventInterface $event): void
    {
        switch ($event->getName()) {
            case EventInterface::HELLO:
                assert($event instanceof Hello);

                $this->identify();
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

    private function invalidateSession(InvalidSession $event): void
    {
        assert($this->webSocket instanceof WebSocket);
        assert($this->loop instanceof LoopInterface);

        if (!$event->canResume()) {
            $this->shutdown();
        } else {
            $this->sendCommand(CommandInterface::RESUME, [
                'token' => $this->token,
                'session_id' => $this->sessionId,
                'seq' => $this->sequenceNumber,
            ]);
        }
    }

    private function shutdown(): void
    {
        assert($this->webSocket instanceof WebSocket);
        assert($this->loop instanceof LoopInterface);

        $this->webSocket->close();
        $this->loop->stop();
    }
}