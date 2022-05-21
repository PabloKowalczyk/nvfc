<?php

declare(strict_types=1);

namespace NvFanController;

use NvFanController\Application\FanSpeed\FanSpeedCalculator;
use NvFanController\Application\NvidiaSettingsInterface;
use NvFanController\Application\Promise\PromiseFactoryInterface;
use NvFanController\Application\Promise\PromiseInterface;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Stream\WritableStreamInterface;

final class FanController
{
    public function __construct(
        private readonly FanSpeedCalculator $fanSpeedCalculator,
        private readonly LoopInterface $loop,
        private readonly WritableStreamInterface $writableStream,
        private readonly NvidiaSettingsInterface $nvidiaSettings,
        private readonly PromiseFactoryInterface $promiseFactory
    ) {
    }

    public function __invoke(): void
    {
        $rpmPromise = $this->createFetchIntAttributePromise('GPUCurrentFanSpeedRPM');
        $temperaturPromise = $this->createFetchIntAttributePromise('GPUCoreTemp');
        $promises = $this->promiseFactory
            ->all(
                [
                    'rpm' => $rpmPromise,
                    'temperature' => $temperaturPromise,
                ]
            )
        ;

        $promises->then(
            function (array $data): void {
                $temperature = $data['temperature'] ?? 0;
                $rpm = $data['rpm'] ?? 0;
                $fanSpeed = $this->fanSpeedCalculator
                    ->calculate($temperature)
                ;
                $this->nvidiaSettings
                    ->changeFanSpeed($fanSpeed)
                ;
                $now = new \DateTimeImmutable();
                $this->writableStream
                    ->write(
                        "[{$now->format('Y-m-d H:i:s.u')}] GPU temp: {$temperature}; Fan speed: {$fanSpeed->toString()}; Fan RPM: {$rpm}" . PHP_EOL
                    )
                ;
            }
        );
    }

    private function createFetchIntAttributePromise(string $name): PromiseInterface
    {
        $resolver = function (callable $resolve, callable $reject) use ($name): void {
            $returnValue = '';
            $process = new Process("nvidia-settings -t -q {$name}");
            $process->start($this->loop);
            $process->stdout
                ->on(
                    'data',
                    static function (string $chunk) use (&$returnValue): void {
                        $returnValue .= \trim($chunk);
                    }
                )
            ;

            $process->on(
                'exit',
                static function () use (&$returnValue, $resolve): void {
                    $resolve(
                        \filter_var(
                            $returnValue,
                            FILTER_VALIDATE_INT,
                        ),
                    );
                }
            );
        };

        return $this->promiseFactory
            ->create($resolver)
        ;
    }
}
