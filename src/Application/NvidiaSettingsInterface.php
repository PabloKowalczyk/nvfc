<?php

declare(strict_types=1);

namespace NvFanController\Application;

interface NvidiaSettingsInterface
{
    public function enableFanControl(): void;

    public function changeFanSpeed(FanSpeed $fanSpeed): void;

    public function version(): string;
}
