<?php

declare(strict_types=1);

namespace NvFanController\Tests\Unit\Application;

use NvFanController\Application\Interval;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    public function test_interval_cannot_be_lower_than_one(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Interval cannot be lower than '1', but '0.9' was passed.");

        Interval::fromString('0.9');
    }

    public function test_interval_can_be_represented_as_string(): void
    {
        $interval = Interval::fromString('4.78');

        $this->assertSame(4.78, $interval->toFloat());
    }
}
