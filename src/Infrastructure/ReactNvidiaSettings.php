<?php

declare(strict_types=1);

namespace NvFanController\Infrastructure;

use NvFanController\Application\FanSpeed\FanSpeed;
use NvFanController\Application\NvidiaSettingsInterface;
use RuntimeException;
use Symfony\Component\Process\Process;

final class ReactNvidiaSettings implements NvidiaSettingsInterface
{
    private readonly string $version;

    public function __construct()
    {
        $versionProcess = new Process(['nvidia-settings', '-v']);
        $versionProcess->run();
        $exitCode = $versionProcess->getExitCode();
        $versionOutput = \trim($versionProcess->getOutput());
        $matches = [];
        $hasVersion = \preg_match(
            "@^nvidia-settings:  version (?<version>\d+\.\d+)@",
            $versionOutput,
            $matches
        );
        $version = $matches['version'] ?? '';

        if (0 !== $exitCode || 1 !== $hasVersion || '' === $version) {
            throw new RuntimeException('Unable to check "nvidia-settings" version.');
        }

        $this->version = $version;
    }

    public function enableFanControl(): void
    {
        $enableFanSpeedControl = new Process(
            ['nvidia-settings', '-a', 'GPUFanControlState=1']
        );
        $enableFanSpeedControl->run();
        $exitCode = $enableFanSpeedControl->getExitCode();
        $enableFanSpeedControlOutput = \trim($enableFanSpeedControl->getOutput());

        if (0 !== $exitCode || 1 !== \preg_match("@^Attribute 'GPUFanControlState' \(.*\) assigned value 1\.$@", $enableFanSpeedControlOutput)) {
            throw new RuntimeException('Unable to enable fan control');
        }
    }

    public function changeFanSpeed(FanSpeed $fanSpeed): void
    {
        $changeFanSpeed = new \React\ChildProcess\Process("nvidia-settings -a \"GPUTargetFanSpeed={$fanSpeed->toString()}\" 2>&1 >/dev/null");
        $changeFanSpeed->on('error', static function (): void {
            throw new \Exception('Unable to change fan speed');
        });
        $changeFanSpeed->start();
    }

    public function version(): string
    {
        return $this->version;
    }
}
