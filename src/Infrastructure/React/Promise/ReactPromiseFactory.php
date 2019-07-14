<?php

declare(strict_types=1);

namespace NvFanController\Infrastructure\React\Promise;

use NvFanController\Application\Promise\PromiseFactoryInterface;
use NvFanController\Application\Promise\PromiseInterface;
use function React\Promise\all;

final class ReactPromiseFactory implements PromiseFactoryInterface
{
    public function create(callable $resolver, callable $canceller = null): PromiseInterface
    {
        return ReactPromise::fromCallables($resolver, $canceller);
    }

    /** {@inheritdoc} */
    public function all(array $promises): PromiseInterface
    {
        return ReactPromise::fromPromise(all($promises));
    }
}
