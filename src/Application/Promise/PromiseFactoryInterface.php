<?php

declare(strict_types=1);

namespace NvFanController\Application\Promise;

interface PromiseFactoryInterface
{
    public function create(\Closure $resolver, \Closure $canceller = null): PromiseInterface;

    /** @param array<string,PromiseInterface> $promises */
    public function all(array $promises): PromiseInterface;
}
