<?php

declare(strict_types=1);

namespace NvFanController;

use NvFanController\Application\FanSpeed\FanSpeedCalculator;
use NvFanController\Application\NvidiaSettingsInterface;
use NvFanController\Application\Promise\PromiseFactoryInterface;
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
    /** @var PromiseFactoryInterface */
    private $promiseFactory;

    public function __construct(
        FanSpeedCalculator $fanSpeedCalculator,
        LoopInterface $loop,
        WritableStreamInterface $writableStream,
        NvidiaSettingsInterface $nvidiaSettings,
        PromiseFactoryInterface $promiseFactory
    ) {
        $this->fanSpeedCalculator = $fanSpeedCalculator;
        $this->loop = $loop;
        $this->writableStream = $writableStream;
        $this->nvidiaSettings = $nvidiaSettings;
        $this->promiseFactory = $promiseFactory;
    }

    public function __invoke(): void
    {
        $rpmResolver = function (callable $resolve, callable $reject): void {
            $rpm = '';
            $process = new Process('nvidia-settings -q GPUCurrentFanSpeedRPM');
            $process->start($this->loop);
            $process->stdout->on('data', static function (string $chunk) use (&$rpm): void {
                $rpm .= \trim($chunk);
            });

            $process->on(
                'exit',
                static function () use (&$rpm, $resolve): void {
                    $lines = \explode('.', $rpm);
                    $lines = \explode(']): ', $lines[0] ?? '');

                    $validatedRpm = \filter_var($lines[1] ?? '0', FILTER_VALIDATE_INT);

                    $resolve($validatedRpm);
                }
            );
        };

        $rpmPromise = $this->promiseFactory
            ->create($rpmResolver);

        $resolver = function (callable $resolve, callable $reject): void {
            $temp = '';
            $process = new Process("nvidia-settings -q GPUCoreTemp |awk -F \":\" 'NR==2{print $3}' |sed 's/[^0-9]*//g'");
            $process->start($this->loop);
            $process->stdout->on('data', static function (string $chunk) use (&$temp): void {
                $temp .= \trim($chunk);
            });

            $process->on(
                'exit',
                static function () use (&$temp, $resolve): void {
                    $validatedTemp = \filter_var($temp, FILTER_VALIDATE_INT);

                    $resolve($validatedTemp);
                }
            );
        };
        $tempPromise = $this->promiseFactory
            ->create($resolver);

        $allPromises = $this->promiseFactory
            ->all(
                [
                    'rpm' => $rpmPromise,
                    'temp' => $tempPromise,
                ]
            );

        $allPromises->then(
            function (array $data): void {
                $temp = $data['temp'] ?? 0;
                $rpm = $data['rpm'] ?? 0;
                $fanSpeed = $this->fanSpeedCalculator
                    ->calculate($temp);

                $this->nvidiaSettings
                    ->changeFanSpeed($fanSpeed);

                $now = new \DateTimeImmutable();
                $this->writableStream
                    ->write(
                        "[{$now->format('Y-m-d H:i:s.u')}] GPU temp: {$temp}; Fan speed: {$fanSpeed->toString()}; Fan RPM: {$rpm}" . PHP_EOL
                    );
            }
        );
    }
}
