<?php

declare(strict_types=1);

namespace NvFanController\Tests\Unit\Application\FanSpeed;

use NvFanController\Application\FanSpeed\LinearFanSpeedCalculator;
use PHPUnit\Framework\TestCase;

final class LinearFanSpeedCalculatorTest extends TestCase
{
    /** @dataProvider fanSpeedProvider */
    public function test_calculator_calculates_fan_speed(int $fanSpeed): void
    {
        $calculator = new LinearFanSpeedCalculator(
            0,
            0,
            100,
            100
        );

        $temperature = $calculator->calculate($fanSpeed);

        $this->assertSame($fanSpeed, $temperature->toInteger());
    }

    public function fanSpeedProvider(): iterable
    {
        $cases = \array_keys(
            \array_fill(
                0,
                100,
                ''
            )
        );

        foreach ($cases as $case) {
            yield "Temperature: {$case}" => [$case];
        }
    }
}
