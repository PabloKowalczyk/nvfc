<?php

declare(strict_types=1);

namespace NvFanController\Application\Promise;

interface PromiseInterface
{
    public function then(\Closure $onFulfilled = null, \Closure $onRejected = null): PromiseInterface;
}
