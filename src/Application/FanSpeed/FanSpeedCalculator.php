<?php

declare(strict_types=1);

namespace NvFanController\Application\FanSpeed;

interface FanSpeedCalculator
{
    public function calculate(int $temperature): FanSpeed;
}
