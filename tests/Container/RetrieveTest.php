<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class RetrieveTest extends MockeryTestCase
{
    /** @test */
    public function hasReturnsFalseWhenNameIsUnknown()
    {
        $container = new Container();

        self::assertFalse($container->has('foo'));
    }

    /** @test */
    public function getThrowsWhenNameIsUnknown()
    {
        $container = new Container();

        self::expectException(NotFoundExceptionInterface::class);
        self::expectExceptionMessage('Name foo could not be resolved');

        $container->get('foo');
    }

    /** @test */
    public function factoriesUseArguments()
    {
        $container = new Container();
        $args = [];
        $container->add('foo', function () use (&$args) {
            $args = func_get_args();
            return 42;
        });

        $container->get('foo', 'with', 'some', 'args');

        self::assertSame(['with', 'some', 'args'], $args);
    }

    /** @test */
    public function onlyNonSharedFactoriesAllowArguments()
    {
        $container = new Container();
        $args = [];
        $container->add('foo', function () use (&$args) {
            $args = func_get_args();
            return 42;
        })->share();

        $container->get('foo', 'with', 'some', 'args');

        self::assertSame([], $args);
    }

    /** @test */
    public function sharedFactoriesAreExecutedOnce()
    {
        $container = new Container();
        $calls = 0;
        $container->share('foo', function () use (&$calls) {
            $calls++;
            return 42;
        });

        $container->get('foo');
        $container->get('foo');

        self::assertSame(1, $calls);
    }

    /** @test */
    public function catchesAnyExceptionAndThrowsContainerExceptionInstead()
    {
        $container = new Container();
        $exception = new \InvalidArgumentException('everything is wrong');
        $container->add('foo', function () use ($exception) {
            throw $exception;
        });

        self::expectException(ContainerExceptionInterface::class);
        self::expectExceptionMessage('Unexpected exception while resolving foo');

        try {
            $container->get('foo');
        } catch (\Exception $e) {
            self::assertSame($exception, $e->getPrevious());
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }
}
