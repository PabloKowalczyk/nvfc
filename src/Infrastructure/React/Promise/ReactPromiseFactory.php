<?php

declare(strict_types=1);

namespace NvFanController\Infrastructure\React\Promise;

use NvFanController\Application\Promise\PromiseFactoryInterface;
use NvFanController\Application\Promise\PromiseInterface;

final class ReactPromiseFactory implements PromiseFactoryInterface
{
    public function create(callable $resolver, callable $canceller = null): PromiseInterface
    {
        return ReactPromise::fromCallables($resolver, $canceller);
    }
}
