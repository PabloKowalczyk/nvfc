<?php

declare(strict_types=1);

namespace NvFanController\Tests\Unit\Application\FanSpeed;

use NvFanController\Application\FanSpeed\LinearFanSpeedCalculator;
use PHPUnit\Framework\TestCase;

final class LinearFanSpeedCalculatorTest extends TestCase
{
    public function test_calculator_calculates_fan_speed(): void
    {
        $calculator = new LinearFanSpeedCalculator(
            0,
            0,
            100,
            100
        );

        $fanSpeed = \random_int(0, 100);
        $temperature = $calculator->calculate($fanSpeed);

        $this->assertSame($fanSpeed, $temperature->toInteger());
    }
}
