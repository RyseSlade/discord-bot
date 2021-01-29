<?php

declare(strict_types=1);

namespace Aedon\DiscordBotTest\Rest;

use Aedon\DiscordBot\Rest\RestApiResponse;
use PHPUnit\Framework\TestCase;

class RestApiResponseTest extends TestCase
{
    public function testShouldReturnExpectedData(): void
    {
        $subject = new RestApiResponse(200, ['test' => true]);

        self::assertEquals(200, $subject->getHttpCode());
        self::assertEquals(['test' => true], $subject->getData());
    }
}