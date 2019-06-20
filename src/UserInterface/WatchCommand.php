<?php

declare(strict_types=1);

namespace NvFanController\UserInterface;

use NvFanController\Application\FanSpeed\FanSpeed;
use NvFanController\Application\Interval;
use NvFanController\Application\Temperature;
use NvFanController\FanController;
use NvFanController\FanSpeed\LinearFanSpeedCalculator;
use NvFanController\Infrastructure\React\Promise\ReactPromiseFactory;
use NvFanController\Infrastructure\ReactNvidiaSettings;
use React\EventLoop\Factory;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class WatchCommand extends Command
{
    protected static $defaultName = 'watch';

    protected function configure(): void
    {
        $this
            ->setDescription('Monitor temperature and set fan speed accordingly')
            ->addOption(
                'interval',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Probing interval in seconds',
                '4'
            )
            ->addOption(
                'start-fan',
                null,
                InputOption::VALUE_OPTIONAL,
                'Start value for fan speed',
                '25'
            )
            ->addOption(
                'start-temp',
                null,
                InputOption::VALUE_OPTIONAL,
                'Keep start fan speed until this value',
                '30'
            )
            ->addOption(
                'end-fan',
                null,
                InputOption::VALUE_OPTIONAL,
                'End value for fan speed',
                '100'
            )
            ->addOption(
                'end-temp',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set maximum fan speed above this value',
                '90'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $interval = Interval::fromString((string) $input->getOption('interval'));
        $startFanSpeed = FanSpeed::fromString((string) $input->getOption('start-fan'));
        $endFanSpeed = FanSpeed::fromString((string) $input->getOption('end-fan'));
        $startTemperature = Temperature::fromString((string) $input->getOption('start-temp'));
        $endTemperature = Temperature::fromString((string) $input->getOption('end-temp'));

        $loop = Factory::create();

        $nvidiaSettings = new ReactNvidiaSettings($loop);
        $nvidiaSettings->enableFanControl();

        $output->writeln(
            "<info>nvidia-settings</info> binary version: {$nvidiaSettings->version()}",
            OutputInterface::VERBOSITY_VERBOSE
        );

        $fanSpeedCalculator = new LinearFanSpeedCalculator(
            $startTemperature->toInteger(),
            $startFanSpeed->toInteger(),
            $endTemperature->toInteger(),
            $endFanSpeed->toInteger()
        );
        $writeStream = new WritableResourceStream(STDOUT, $loop);
        $promiseFactory = new ReactPromiseFactory();
        $fanController = new FanController(
            $fanSpeedCalculator,
            $loop,
            $writeStream,
            $nvidiaSettings,
            $promiseFactory
        );

        $loop->addTimer(0, $fanController);
        $loop->addPeriodicTimer($interval->toFloat(), $fanController);
        $loop->addSignal(SIGINT, function () use ($loop, $output): void {
            $output->writeln('<info>Bye bye</info>');
            $loop->stop();
        });
        $loop->run();

        return 0;
    }
}
