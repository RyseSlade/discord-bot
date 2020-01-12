<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Rest;

final class RestApiResponse
{
    /** @var int|null  */
    private $httpCode;

    /** @var mixed[]|null */
    private $data;

    /**
     * @param int $httpCode
     * @param mixed[]|null $data
     */
    public function __construct(?int $httpCode, ?array $data)
    {
        $this->httpCode = $httpCode;
        $this->data = $data;
    }

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    /**
     * @return mixed[]|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}