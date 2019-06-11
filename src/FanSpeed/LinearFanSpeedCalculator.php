<?php

declare(strict_types=1);

namespace NvFanController\FanSpeed;

use NvFanController\Application\FanSpeed;

final class LinearFanSpeedCalculator implements FanSpeedCalculator
{
    /** @var int */
    private $startTemp;
    /** @var int */
    private $startFanSpeed;
    /** @var int */
    private $endTemp;
    /** @var int */
    private $endFanSpeed;

    public function __construct(
        int $startTemp,
        int $startFanSpeed,
        int $endTemp,
        int $endFanSpeed
    ) {
        $this->startTemp = $startTemp;
        $this->startFanSpeed = $startFanSpeed;
        $this->endTemp = $endTemp;
        $this->endFanSpeed = $endFanSpeed;

        if ($startTemp > $endTemp) {
            throw new \InvalidArgumentException('Start temp is higher than end');
        }

        if ($startFanSpeed > $endFanSpeed) {
            throw new \InvalidArgumentException('Start fan speed is higher than end');
        }
    }

    public function calculate(int $temperature): FanSpeed
    {
        if ($temperature < $this->startTemp) {
            return FanSpeed::fromInteger($this->startFanSpeed);
        }

        if ($temperature >= $this->endTemp) {
            return FanSpeed::fromInteger($this->endFanSpeed);
        }

        $percent = (($temperature - $this->startTemp) / ($this->endTemp - $this->startTemp));

        return FanSpeed::fromInteger((int) (($percent * ($this->endFanSpeed - $this->startFanSpeed)) + $this->startFanSpeed));
    }
}
