<?php

declare(strict_types=1);

namespace NvFanController\Application;

final class Interval
{
    private function __construct(private readonly float $interval)
    {
    }

    public static function fromString(string $interval): self
    {
        $floatInterval = (float) $interval;

        if ($floatInterval < 1) {
            throw new \InvalidArgumentException("Interval cannot be lower than '1', but '{$floatInterval}' was passed.");
        }

        return new self($floatInterval);
    }

    public function toFloat(): float
    {
        return $this->interval;
    }
}
