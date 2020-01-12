<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Event;

abstract class AbstractEvent implements EventInterface
{
    /** @var mixed[] */
    protected array $data = [];

    protected string $name = '';
    protected ?int $sequenceNumber = null;

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->sequenceNumber = isset($data['s']) && is_numeric($data['s']) ? (int)$data['s'] : null;
        $this->name = isset($data['t']) && is_string($data['t']) ? $data['t'] : '';

        $this->convertData($data);
    }

    /**
     * @param string $key
     * @return mixed[]
     */
    public function getData(string $key = null): array
    {
        if ($key !== null && isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $this->data;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed[] $data
     */
    protected function convertData(array $data): void
    {

    }
}