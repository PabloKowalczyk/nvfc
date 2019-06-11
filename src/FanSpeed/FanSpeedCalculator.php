<?php

declare(strict_types=1);

namespace NvFanController\FanSpeed;

use NvFanController\Application\FanSpeed;

interface FanSpeedCalculator
{
    public function calculate(int $temperature): FanSpeed;
}
