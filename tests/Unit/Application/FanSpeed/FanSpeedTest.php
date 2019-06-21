<?php

declare(strict_types=1);

namespace NvFanController\Tests\Unit\Application\FanSpeed;

use NvFanController\Application\FanSpeed\FanSpeed;
use PHPUnit\Framework\TestCase;

final class FanSpeedTest extends TestCase
{
    /**
     * @dataProvider fanSpeedProvider
     */
    public function test_fan_speed_is_in_accepted_range(int $fanSpeed): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Fan speed must be in 0-100 range.');

        FanSpeed::fromInteger($fanSpeed);
    }

    public function test_fan_speed_can_be_created_from_string(): void
    {
        $speed = \random_int(0, 100);
        $fanSpeed = FanSpeed::fromString((string) $speed);

        $this->assertSame($speed, $fanSpeed->toInteger());
    }

    public function fanSpeedProvider(): iterable
    {
        yield '-1' => [-1];
        yield '101' => [101];
    }
}
