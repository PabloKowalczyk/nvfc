<?php

declare(strict_types=1);

namespace NvFanController\Tests\Unit\Application;

use NvFanController\Application\Temperature;
use PHPUnit\Framework\TestCase;

final class TemperatureTest extends TestCase
{
    /**
     * @dataProvider wrongTemperatureProvider
     */
    public function test_temperature_is_in_accepted_range(string $temperature): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Temperature must be in 0-120 range.');

        Temperature::fromString($temperature);
    }

    public function test_temperature_can_be_presented_as_string(): void
    {
        $temperature = \random_int(0, 120);
        $temperatureObject = Temperature::fromString((string) $temperature);

        $this->assertSame($temperature, $temperatureObject->toInteger());
    }

    public function wrongTemperatureProvider(): iterable
    {
        yield '-1' => ['-1'];
        yield '121' => ['121'];
    }
}
