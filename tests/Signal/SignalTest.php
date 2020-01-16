<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Test\Signal;

use Aedon\DiscordBot\Signal\Signal;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use function time;

class SignalTest extends TestCase
{
    public function testShouldSucceedCreatingSignal(): void
    {
        $vfs = vfsStream::setup('var', null, [
            'signal' => [],
        ]);

        $subject = new Signal($vfs->getChild('signal')->url(), 1);

        $result = $subject->create();

        self::assertTrue($result);
    }

    public function testShouldFailIfSignalFileExists(): void
    {
        $vfs = vfsStream::setup('var', null, [
            'signal' => [
                (string)time() => '',
            ],
        ]);

        $subject = new Signal($vfs->getChild('signal')->url(), 1);

        $result = $subject->create();

        self::assertFalse($result);
    }

    public function testShouldRemoveSignalFile(): void
    {
        $filename1 = (string)time();
        $filename2 = (string)(time() + 1);

        $vfs = vfsStream::setup('var', null, [
            'signal' => [],
        ]);

        $subject = new Signal($vfs->getChild('signal')->url(), 1);

        $subject->check();

        vfsStream::newFile($filename1)->withContent('')->at($vfs->getChild('signal'));
        vfsStream::newFile($filename2)->withContent('')->at($vfs->getChild('signal'));

        $subject->check();

        self::assertFalse($vfs->hasChild('signal/' . $filename1));
        self::assertFalse($vfs->hasChild('signal/' . $filename2));
    }
}