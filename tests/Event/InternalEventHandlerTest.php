<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Event;

use Aedon\DiscordBot\Command\CommandList;
use Aedon\DiscordBot\Command\IdentifyCommand;
use Aedon\DiscordBot\Command\ResumeCommand;
use Aedon\DiscordBot\Event\Discord\Hello;
use Aedon\DiscordBot\Event\Discord\InvalidSession;
use Aedon\DiscordBot\Event\Discord\Reconnect;
use Aedon\DiscordBot\Event\InternalEventHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ratchet\Client\WebSocket;
use React\EventLoop\LoopInterface;
use function json_encode;

class InternalEventHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldIdentifyAndStartHeartbeatOnHelloEvent(): void
    {
        $subject = new InternalEventHandler('token', new CommandList());

        $loop = $this->prophesize(LoopInterface::class);
        $webSocket = $this->prophesize(WebSocket::class);

        $subject->setLoop($loop->reveal());
        $subject->setWebSocket($webSocket->reveal());

        $subject->process(new Hello([
                'd' => [
                    'heartbeat_interval' => 45000,
                ],
            ]));

        $data = (new IdentifyCommand())->jsonSerialize();
        $data['token'] = 'token';

        $webSocket->send(json_encode([
            'op' => 2,
            'd' => $data,
        ]))->shouldHaveBeenCalledOnce();

        $loop->addPeriodicTimer(45, Argument::any())->shouldHaveBeenCalled();
    }

    public function testShouldShutdownOnInvalidSessionEvent(): void
    {
        $subject = new InternalEventHandler('token', new CommandList());

        $loop = $this->prophesize(LoopInterface::class);
        $webSocket = $this->prophesize(WebSocket::class);

        $subject->setLoop($loop->reveal());
        $subject->setWebSocket($webSocket->reveal());

        $subject->process(new InvalidSession([
                'd' => false,
            ]));

        $webSocket->close()->shouldHaveBeenCalled();
    }

    public function testShouldResumeOnInvalidSessionEvent(): void
    {
        $subject = new InternalEventHandler('token', new CommandList());

        $loop = $this->prophesize(LoopInterface::class);
        $webSocket = $this->prophesize(WebSocket::class);

        $subject->setLoop($loop->reveal());
        $subject->setWebSocket($webSocket->reveal());

        $subject->process(new InvalidSession([
                'd' => true,
            ]));

        $data = (new ResumeCommand())->jsonSerialize();
        $data['token'] = 'token';

        $webSocket->send(json_encode([
            'op' => 6,
            'd' => $data,
        ]))->shouldHaveBeenCalledOnce();
    }

    public function testShouldShutdownOnReconnectEvent(): void
    {
        $subject = new InternalEventHandler('token', new CommandList());

        $loop = $this->prophesize(LoopInterface::class);
        $webSocket = $this->prophesize(WebSocket::class);

        $subject->setLoop($loop->reveal());
        $subject->setWebSocket($webSocket->reveal());

        $subject->process(new Reconnect([]));

        $webSocket->close()->shouldHaveBeenCalled();
    }
}