<?php

declare(strict_types=1);

namespace NvFanController\Application;

final class Temperature
{
    /** @var int */
    private $temperature;

    private function __construct(int $temperature)
    {
        $this->temperature = $temperature;
    }

    public static function fromString(string $temperature): self
    {
        return self::createFromInteger((int) $temperature);
    }

    public function toInteger(): int
    {
        return $this->temperature;
    }

    private static function createFromInteger(int $temperature): self
    {
        if ($temperature < 0 || $temperature > 120) {
            throw new \InvalidArgumentException('Temperature must be in 0-120 range.');
        }

        return new self($temperature);
    }
}
