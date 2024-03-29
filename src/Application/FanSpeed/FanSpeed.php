<?php

declare(strict_types=1);

namespace NvFanController\Application\FanSpeed;

final class FanSpeed
{
    private function __construct(private readonly int $fanSpeed)
    {
    }

    public static function fromInteger(int $fanSpeed): self
    {
        return self::createFromInteger($fanSpeed);
    }

    public static function fromString(string $fanSpeed): self
    {
        return self::createFromInteger((int) $fanSpeed);
    }

    public function toInteger(): int
    {
        return $this->fanSpeed;
    }

    public function toString(): string
    {
        return (string) $this->fanSpeed;
    }

    private static function createFromInteger(int $fanSpeed): self
    {
        if ($fanSpeed < 0 || $fanSpeed > 100) {
            throw new \InvalidArgumentException('Fan speed must be in 0-100 range.');
        }

        return new self($fanSpeed);
    }
}
