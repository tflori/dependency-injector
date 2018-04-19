<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use DependencyInjector\Test\Examples\DateTimeFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
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
        })->share();

        $container->get('foo', 'with', 'some', 'args');

        self::assertSame([], $args);
    }

    /** @test */
    public function sharedFactoriesAreExecutedOnce()
    {
        $container = new Container();
        $calls = 0;
        $container->add('foo', function () use (&$calls) {
            $calls++;
        })->share();

        $container->get('foo');
        $container->get('foo');

        self::assertSame(1, $calls);
    }
}
