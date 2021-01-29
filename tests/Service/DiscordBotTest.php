<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Service;

use Aedon\DiscordBot\Gateway\BotGateway;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Ratchet\Client\Connector;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

class NullLogger implements LoggerInterface
{
    public function emergency($message, array $context = array())
    {

    }

    public function alert($message, array $context = array())
    {

    }

    public function critical($message, array $context = array())
    {

    }

    public function error($message, array $context = array())
    {

    }

    public function warning($message, array $context = array())
    {

    }

    public function notice($message, array $context = array())
    {

    }

    public function info($message, array $context = array())
    {

    }

    public function debug($message, array $context = array())
    {

    }

    public function log($level, $message, array $context = array())
    {

    }
}

class DiscordBot extends TestCase
{
    use ProphecyTrait;

    public function testShouldCallConnector(): void
    {
        $subject = new \Aedon\DiscordBot\Service\DiscordBot('token');

        $subject->setLogger(new NullLogger());

        $subject->setBotGatewayUrl('url');

        $promise = $this->prophesize(PromiseInterface::class);
        $loop = $this->prophesize(LoopInterface::class);
        $connector = $this->prophesize(Connector::class);
        $connector->__invoke('url')->willReturn($promise->reveal());

        $subject->initialize($loop->reveal(), $connector->reveal());

        $promise->then(Argument::cetera())->shouldHaveBeenCalled();
    }

    public function testShouldThrowExceptionIfBotGatewayUrlIsUndefined(): void
    {
        $botGateway = $this->prophesize(BotGateway::class);
        $botGateway->getUrl()->willReturn('');

        $subject = new \Aedon\DiscordBot\Service\DiscordBot('token', null, null, null, null, $botGateway->reveal());

        $subject->setLogger(new NullLogger());

        $loop = $this->prophesize(LoopInterface::class);
        $connector = $this->prophesize(Connector::class);

        self::expectException(InvalidArgumentException::class);

        $subject->initialize($loop->reveal(), $connector->reveal());
    }
}