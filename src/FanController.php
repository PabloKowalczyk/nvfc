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
                    $resolve($temp);
                }
            );
        };
        $promise = $this->promiseFactory
            ->create($resolver);
        $promise->then(
            function (string $temp): void {
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
