<?php

declare(strict_types=1);

namespace NvFanController;

use NvFanController\Application\NvidiaSettingsInterface;
use NvFanController\FanSpeed\FanSpeedCalculator;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Stream\WritableStreamInterface;

final class FanController
{
    /** @var FanSpeedCalculator */
    private $fanSpeedCalculator;
    /** @var LoopInterface */
    private $loop;
    /** @var WritableStreamInterface */
    private $writableStream;
    /** @var NvidiaSettingsInterface */
    private $nvidiaSettings;

    public function __construct(
        FanSpeedCalculator $fanSpeedCalculator,
        LoopInterface $loop,
        WritableStreamInterface $writableStream,
        NvidiaSettingsInterface $nvidiaSettings
    ) {
        $this->fanSpeedCalculator = $fanSpeedCalculator;
        $this->loop = $loop;
        $this->writableStream = $writableStream;
        $this->nvidiaSettings = $nvidiaSettings;
    }

    public function __invoke(): void
    {
        $temp = '';
        $process = new Process("nvidia-settings -q GPUCoreTemp |awk -F \":\" 'NR==2{print $3}' |sed 's/[^0-9]*//g'");
        $process->start($this->loop);
        $process->stdout->on('data', static function (string $chunk) use (&$temp) {
            $temp .= \trim($chunk);
        });

        $process->on(
            'exit',
            function () use (&$temp): void {
                $tempInt = (int) $temp;
                $fanSpeed = $this->fanSpeedCalculator
                    ->calculate($tempInt);

                $this->nvidiaSettings
                    ->changeFanSpeed($fanSpeed);

                $now = new \DateTimeImmutable();
                $this->writableStream
                    ->write(
                        "[{$now->format('Y-m-d H:i:s.u')}] GPU temp: {$tempInt}; Fan speed: {$fanSpeed->toString()}" . PHP_EOL
                    );
            }
        );
    }
}
