<?php

declare(strict_types=1);

namespace NvFanController\Application\Promise;

interface PromiseInterface
{
    public function then(callable $onFulfilled = null, callable $onRejected = null): PromiseInterface;
}
