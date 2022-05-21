<?php

declare(strict_types=1);

namespace NvFanController\Infrastructure\React\Promise;

use NvFanController\Application\Promise\PromiseInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface as ReactPromiseInterface;

final class ReactPromise implements PromiseInterface
{
    private function __construct(private readonly ReactPromiseInterface $promise)
    {
    }

    public static function fromCallables(\Closure $resolver, \Closure $canceller = null): self
    {
        return new self(new Promise($resolver, $canceller));
    }

    public static function fromPromise(ReactPromiseInterface $promise): self
    {
        return new self($promise);
    }

    public function then(\Closure $onFulfilled = null, \Closure $onRejected = null): PromiseInterface
    {
        $promise = $this->promise
            ->then($onFulfilled, $onRejected)
        ;

        return self::fromPromise($promise);
    }
}
