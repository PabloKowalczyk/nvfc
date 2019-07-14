<?php

declare(strict_types=1);

namespace NvFanController\Application\Promise;

interface PromiseFactoryInterface
{
    public function create(callable $resolver, callable $canceller = null): PromiseInterface;

    /** @param array<string,PromiseInterface> $promises */
    public function all(array $promises): PromiseInterface;
}
