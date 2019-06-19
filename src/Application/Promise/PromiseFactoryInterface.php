<?php

declare(strict_types=1);

namespace NvFanController\Application\Promise;

interface PromiseFactoryInterface
{
    public function create(callable $resolver, callable $canceller = null): PromiseInterface;
}
